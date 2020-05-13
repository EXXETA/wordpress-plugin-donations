<?php


namespace donations;


use WC_Product_Simple;
use WP_Term;

/**
 * this class is a wrapper around the wordpress plugin integration logic
 */
class Plugin
{
    /**
     * path to the main plugin file
     *
     * @var string
     */
    private static $pluginFile;
    // shortcode of this plugin
    public static $bannerShortCode = 'wp_donations_banner';
    // block name of this plugin
    public static $blockTypeName = 'wp-donations-plugin/checkout-banner';
    // parent menu slug of this plugin
    public static $menuSlug = 'wp-donations-plugin';
    // custom post type for report
    public static $customPostType = 'donation_report';

    /**
     * Plugin constructor.
     * @param string $pluginFile
     */
    public function __construct(string $pluginFile)
    {
        self::$pluginFile = $pluginFile;
    }

    private static function uploadImage(CharityProduct $singleProduct): ?int
    {
        // source: https://gist.github.com/hissy/7352933
        $file = plugin_dir_path(self::$pluginFile) . 'images/' . $singleProduct->getImagePath();
        $filename = basename($file);

        $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
        if (!$upload_file['error']) {
            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_parent' => 0,
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], 0);
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                return $attachment_id;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public static function getPluginFile(): string
    {
        return self::$pluginFile;
    }

    /**
     * checking if dependent plugins (= woocommerce-services and woocommerce) are activated and present on class path
     */
    public function check(): void
    {
        $allActivePlugins = apply_filters('active_plugins', get_option('active_plugins'));
        if (!in_array('woocommerce/woocommerce.php', $allActivePlugins)) {
            die('Missing required plugin woocommerce');
        }
        #if (!in_array('woocommerce-services/woocommerce-services.php', $allActivePlugins)) {
        #    die('Missing required plugin woocommerce-services');
        #}
    }

    public function registerPluginHooks(): void
    {
        // plugin lifecycle hooks
        register_activation_hook(self::getPluginFile(), [Plugin::class, 'activate']);
        register_deactivation_hook(self::getPluginFile(), [Plugin::class, 'deactivate']);
        register_uninstall_hook(self::getPluginFile(), [Plugin::class, 'uninstall']);
        // register report custom post type
        add_action('init', [Plugin::class, 'setup_report_post_type'], 0);
        // register gutenberg block
        add_action('init', [Plugin::class, 'setup_banner_block']);
        // register shortcode
        add_shortcode(self::$bannerShortCode, [Plugin::class, 'setup_banner_shortcode']);
        // register styles
        add_action('wp_enqueue_scripts', [Plugin::class, 'handle_styles']);

        if (is_admin()) {
            // add menu to wp admin section
            add_action('admin_menu', [Plugin::class, 'setup_menu']);
            // register settings
            add_action('admin_init', [Plugin::class, 'setup_settings']);
        }

        // register cron hook as action
        if (!has_action('wp_donations_report_check')) {
            add_action('wp_donations_report_check', [Plugin::class, 'do_report_check']);
        }

        // uncomment the following line for debugging and force-register the scheduled event
        // wp_clear_scheduled_hook('wp_donations_report_check');
        if (wp_next_scheduled('wp_donations_report_check') === false) {
            $result = wp_schedule_event(time(), 'daily', 'wp_donations_report_check');
            if (!$result) {
                error_log(sprintf('%s: Error registering check job for report generation', self::$pluginFile));
            }
        }
    }

    // (de-)activation and (un-)install logic

    /**
     * this is called by wordpress if the plugin is activated
     * This is the place to init all products at once and store their WooCommerce product IDs in wordpress options.
     */
    static function activate(): void
    {
        // init all known products and store their IDs
        $result = CharityProductManager::getCharityProductCategory();
        $termId = null;
        if (!$result instanceof WP_Term) {
            // create it
            $result = wp_insert_term("Spendemünzen", CharityProductManager::getWooProductCategoryTaxonomy(), [
                'description' => 'Kategorie für Charity Coins',
                'parent' => 0,
                'slug' => CharityProductManager::getCategoryId(),
            ]);
            $termId = $result['term_id'];
        } else {
            $termId = $result->term_id;
        }
        if (!$termId) {
            error_log("Could not create default coin category");
        }
        // add default products
        foreach (CharityProductManager::getAllProducts() as $singleProduct) {
            /* @var $singleProduct CharityProduct */
            if (empty(get_option($singleProduct->getProductIdOptionKey()))) {
                $product = new WC_Product_Simple();
                $product->set_name($singleProduct->getName());
                $product->set_slug($singleProduct->getSlug());
                $product->set_description($singleProduct->getDescription());
                // prices
                $product->set_regular_price($singleProduct->getPrice());
                $product->set_sale_price($singleProduct->getPrice());
                // disable stock management
                $product->set_manage_stock(false);
                $product->set_sold_individually(false);
                // disable reviews
                $product->set_reviews_allowed(false);
                // set category id
                $product->set_category_ids([$termId]);
                $product->set_tax_status('none');
                $product->set_catalog_visibility('hidden');
                $product->set_virtual(true);

                // upload image
                $imageId = self::uploadImage($singleProduct);
                if ($imageId) {
                    update_option($singleProduct->getImageIdOptionKey(), $imageId);
                    $product->set_image_id($imageId);
                } else {
                    error_log(sprintf('%s: problem uploading image "%s"', self::$pluginFile,
                        $singleProduct->getImagePath()));
                }
                $product->save();

                $productId = $product->get_id();
                update_option($singleProduct->getProductIdOptionKey(), $productId);
            }
        }
    }

    /**
     * this is called if the plugin is disabled
     */
    static function deactivate(): void
    {
        // atm there is nothing to do here
        remove_shortcode(self::$bannerShortCode);

        if (function_exists('unregister_block_type')) {
            // Gutenberg is active.
            unregister_block_type(self::$blockTypeName);
        }
        // cron clear
        wp_clear_scheduled_hook('wp_donations_report_check');
    }

    /**
     * this is called if the plugin is uninstalled
     */
    static function uninstall(): void
    {
        // remove products of this plugin
        foreach (CharityProductManager::getAllProducts() as $singleProduct) {
            /* @var $singleProduct CharityProduct */
            $productId = get_option($singleProduct->getProductIdOptionKey());
            if (!empty($productId)) {
                // delete product
                $product = wc_get_product($productId);
                $product->delete();
            }
            // delete option
            delete_option($singleProduct->getProductIdOptionKey());
        }

        // remove woo commerce product category which is technically a wordpress term - but only if it's not empty!
        $result = CharityProductManager::getCharityProductCategory();
        if ($result instanceof WP_Term) {
            $productsInCategory = wc_get_term_product_ids($result->term_id, CharityProductManager::getWooProductCategoryTaxonomy());
            if (count($productsInCategory) === 0) {
                // we do only delete the category if its empty!
                wp_delete_term($result->term_id, CharityProductManager::getWooProductCategoryTaxonomy());
            }
        }
        SettingsManager::uninstall();
    }

    /** banner block logic */
    static function setup_banner_block(): void
    {
        if (!function_exists('register_block_type')) {
            // Gutenberg is not active.
            return;
        }

        // automatically load dependencies and version
        $assetFile = include(plugin_dir_path(self::getPluginFile()) . 'build/index.asset.php');
        wp_register_script('checkout-charity-banner', plugins_url('build/index.js', self::getPluginFile()),
            $assetFile['dependencies'],
            $assetFile['version']
        );
        // after wooCommerce setup this value is available
        if (strlen(trim(get_option('woocommerce_cart_page_id'))) > 0) {
            wp_localize_script('checkout-charity-banner', 'cart_page_id', get_option('woocommerce_cart_page_id'));
        }
        register_block_type(self::$blockTypeName, [
            'editor_script' => 'checkout-charity-banner',
            'render_callback' => [Plugin::class, 'render_cart_block']
        ]);
    }

    static function render_cart_block($attributes = [], $content = ''): string
    {
        if (!isset($attributes['donationMode'])) {
            $campaign = CampaignManager::getAllCampaignTypes()[0];
        } else {
            $campaign = $attributes['donationMode'];
        }
        $banner = new Banner($campaign, plugin_dir_url(self::getPluginFile()));
        return $banner->render();
    }

    static function setup_banner_shortcode($atts): string
    {
        $shortCodeAtts = shortcode_atts([
            'campaign' => CampaignManager::getAllCampaignTypes()[0],
        ], $atts, self::$bannerShortCode);
        return (new Banner($shortCodeAtts['campaign'], plugin_dir_url(self::getPluginFile())))->render();
    }

    static function handle_styles(): void
    {
        // no style inclusion on admin pages atm
        if (is_admin()) {
            return;
        }
        $post = get_post();
        $isStyleNeeded = false;
        if ($post !== null) {
            // only add banner styles if the block of this plugin is used
            if (has_blocks($post->post_content)) {
                $blocks = parse_blocks($post->post_content);
                foreach ($blocks as $singleBlock) {
                    if ($singleBlock['blockName'] === self::$blockTypeName) {
                        $isStyleNeeded = true;
                        break;
                    }
                }
            }
            // AND/OR only add banner styles if the shortcode is used
            if (has_shortcode($post->post_content, self::$bannerShortCode)) {
                $isStyleNeeded = true;
            }
        }
        if ($isStyleNeeded) {
            wp_enqueue_style('wp-donations-plugin-styles', plugin_dir_url(self::getPluginFile()) . 'styles/banner.css');
        }
    }

    // menu related code
    static function setup_menu(): void
    {
        // add plugin pages
        add_menu_page('Spendenübersicht', 'Spendenübersicht', 'manage_options',
            self::$menuSlug, null, 'dashicons-cart');
        add_action('admin_notices', [Plugin::class, 'add_donation_info_banner']);

        // submenu page:
        add_submenu_page(self::$menuSlug, 'Aktuell', 'Aktuell', 'manage_options',
            'wp-donations-current', [Plugin::class, 'handle_menu_current']);
        // add options menu
        add_options_page('Spenden', 'Spenden', 'manage_options',
            'wp-donations-settings', [Plugin::class, 'handle_menu_settings']);
    }

    static function handle_menu_settings(): void
    {
        include plugin_dir_path(self::getPluginFile()) . 'donations/pages/settings.php';
    }

    static function handle_menu_current(): void
    {
        include plugin_dir_path(self::getPluginFile()) . 'donations/pages/current.php';
    }

    static function setup_report_post_type(): void
    {
        // TODO add translation labels
        register_post_type(self::$customPostType, [
            'public' => false,
            'label' => 'Spenden-Reports',
            'description' => 'Einträge über die Spendenaktivität im Shop',
            'hierarchical' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => self::$menuSlug,
            'show_in_admin_bar' => false,
            'show_in_rest' => false,
            'supports' => [
                'title',
                'editor',
            ],
            'has_archive' => false,
            'can_export' => true,
            'map_meta_cap' => false,
            'rewrite' => [
                ['slug' => self::$customPostType]
            ],
            'delete_with_user' => false,
            'query_var' => false,
            'capabilities' => [
                'create_posts' => 'do_not_allow',
                // you need admin rights to use the following capabilities
                'edit_post' => 'manage_options',
                'read_post' => 'manage_options',
                'delete_post' => 'do_not_allow',
                'edit_posts' => 'manage_options',
                'edit_others_posts' => 'do_not_allow',
                'publish_posts' => 'do_not_allow',
                'read_private_posts' => 'manage_options',
            ],
        ]);

        if (is_admin()) {
            // hide default edit features of wordpress
            add_filter('post_row_actions', [Plugin::class, 'remove_post_type_row_actions']);
            add_action('admin_head', [Plugin::class, 'remove_post_type_edit_button']);
        }
    }

    static function remove_post_type_row_actions(array $actions)
    {
        // hide "edit" and "quick edit" in rows of donation reports
        $screen = get_current_screen();
        if ($screen && $screen->post_type === self::$customPostType) {
            return [];
        }
        return $actions;
    }

    static function remove_post_type_edit_button()
    {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === self::$customPostType) {
            // disable edit button and edit form entirely via styles and script
            echo '<style>#publishing-action { display: none; }
                         #misc-publishing-actions .edit-post-status { display: none; }
                    </style>
                    <script lang="js">
                    jQuery(document).ready(() => {
                        jQuery("#post").submit((e) => {
                            e.preventDefault();
                        })    
                    });
                    </script>';
        }
    }

    /**
     * this banner is showing up on dashboard page of this plugin
     */
    static function add_donation_info_banner(): void
    {
        $screen = get_current_screen();
        if ($screen->post_type !== self::$customPostType
            || $screen->id !== 'edit-' . self::$customPostType) {
            return;
        }

        $allProductIds = [];
        foreach (CharityProductManager::getAllProducts() as $charityProduct) {
            /* @var $charityProduct CharityProduct */
            $allProductIds[] = get_option($charityProduct->getProductIdOptionKey());
        }

        // TODO add correct iban information
        $output = '<div class="notice notice-info"><p>';
        $output .= 'Dieses Plugin erweitert den Shop mit mehreren Produkten, um Gelder für 
                    Wohltätigkeitsorganisationen zu sammeln.<br/>';
        // TODO name shortcode and/or Gutenberg Block
        $output .= sprintf('Produkt-IDs: <strong>%s</strong>', join(', ', $allProductIds)) . '<br/>';
        $output .= 'Bitte überweisen Sie in regelmäßigen Abständen die Beträge der eingenommenen Spenden 
                    unter Angabe des jeweilig gewünschten Spendenzwecks zusätzlich zum angegebenen Verwendungszweck
                    auf folgendes Konto:<br/><br/>';
        $output .= '<strong>IBAN:</strong> DE06 5502 0500 0222 2222 22<br/>';
        $output .= '<strong>BIC:</strong> BFSWDE33MNZ &ndash; Bank für Sozialwirtschaft<br/>';
        $output .= '<strong>Verwendungszweck:</strong> 20ISAZ2002';
        $output .= '</p></div>';

        $currentReportMode = SettingsManager::getOptionCurrentReportingInterval();
        $interval = SettingsManager::getReportingIntervals()[$currentReportMode];
        $recipient = SettingsManager::getOptionReportRecipientMail();

        $output .= '<div class="notice notice-info"><p>';
        $output .= '<strong>Automatisches Erzeugen von Spendenberichten:</strong> ' . $interval . '<br/>';
        $lastGenerationDate = SettingsManager::getOptionReportLastGenerationDate();
        $output .= sprintf('<strong>Letztes Berichtsdatum:</strong> %s<br/>',
            $lastGenerationDate ? $lastGenerationDate->format('Y-m-d') : '-');
        $nextExecutionDate = ReportGenerator::calculateNextExecutionDate($currentReportMode, $lastGenerationDate);
        $output .= sprintf('<strong>Nächste Berichtserzeugung:</strong> %s<br/>',
            $nextExecutionDate ? $nextExecutionDate->format('Y-m-d') : '-');
        $lastCheckDate = SettingsManager::getOptionReportLastCheck();
        $output .= sprintf('<strong>Letzte Überprüfung:</strong> %s<br/>',
            $lastCheckDate ? get_date_from_gmt(date('Y-m-d H:i:s', $lastCheckDate->getTimestamp()), 'F j, Y H:i:s') : '-');

        $output .= '<strong>Empfangsadresse:</strong> ';
        $output .= sprintf('<a href="mailto:%s">%s</a>', $recipient, esc_attr($recipient));
        $output .= '</p></div>';

        echo $output;
    }

    // settings related code
    static function setup_settings(): void
    {
        SettingsManager::init();
    }

    /**
     * @param \DateTime|null $timeRangeStart
     * @param \DateTime|null $timeRangeEnd
     * @param bool $isRegular
     * @throws \Exception
     */
    static function do_report_generate(\DateTime $timeRangeStart = null, \DateTime $timeRangeEnd = null,
                                       $isRegular = false): void
    {
        $mode = SettingsManager::getOptionCurrentReportingInterval();
        ReportGenerator::generateReport(new ReportGenerationModel($timeRangeStart, $timeRangeEnd, $mode,
            $isRegular, true));
    }

    static function do_report_check(): void
    {
        try {
            ReportGenerator::checkReportGeneration();
            SettingsManager::setOptionReportLastCheck();
        } catch (\Exception $ex) {
            error_log(Plugin::getPluginFile() . ': error encountered during check for report generation');
            return;
        }
    }
}