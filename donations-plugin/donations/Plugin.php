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

    /**
     * Plugin constructor.
     * @param string $pluginFile
     */
    public function __construct(string $pluginFile)
    {
        self::$pluginFile = $pluginFile;
    }

    public function check()
    {
        $allActivePlugins = apply_filters('active_plugins', get_option('active_plugins'));
        if (!in_array('woocommerce/woocommerce.php', $allActivePlugins)) {
            die('Missing required plugin woocommerce');
        }
        if (!in_array('woocommerce-services/woocommerce-services.php', $allActivePlugins)) {
            die('Missing required plugin woocommerce-services');
        }
    }

    public function registerPluginHooks()
    {
        // plugin lifecycle hooks
        register_activation_hook(self::$pluginFile, [Plugin::class, 'activate']);
        register_deactivation_hook(self::$pluginFile, [Plugin::class, 'deactivate']);
        register_uninstall_hook(self::$pluginFile, [Plugin::class, 'uninstall']);
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
        }
    }

    // (de-)activation and (un-)install logic

    /**
     * this is called by wordpress if the plugin is activated
     * This is the place to init all products at once and store their WooCommerce product IDs in wordpress options.
     */
    static function activate()
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
                $product->save();

                $product_id = $product->get_id();
                update_option($singleProduct->getProductIdOptionKey(), $product_id);
            }
        }
    }

    /**
     * this is called if the plugin is disabled
     */
    static function deactivate()
    {
        // atm there is nothing to do here
        remove_shortcode(self::$bannerShortCode);

        if (function_exists('unregister_block_type')) {
            // Gutenberg is active.
            unregister_block_type(self::$blockTypeName);
        }
    }

    /**
     * this is called if the plugin is uninstalled
     */
    static function uninstall()
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
    }

    /** banner block logic */
    static function setup_banner_block()
    {
        if (!function_exists('register_block_type')) {
            // Gutenberg is not active.
            return;
        }

        // automatically load dependencies and version
        $assetFile = include(plugin_dir_path(self::$pluginFile) . 'build/index.asset.php');
        wp_register_script('checkout-charity-banner', plugins_url('build/index.js', self::$pluginFile),
            $assetFile['dependencies'],
            $assetFile['version']
        );
        wp_localize_script('checkout-charity-banner', 'cart_page_id', get_option('woocommerce_cart_page_id'));
        register_block_type(self::$blockTypeName, [
            'editor_script' => 'checkout-charity-banner',
            'render_callback' => [Plugin::class, 'render_cart_block']
        ]);
    }

    static function render_cart_block($attributes = [], $content = '')
    {
        if (!isset($attributes['donationMode'])) {
            $campaign = CampaignManager::getAllCampaignTypes()[0];
        } else {
            $campaign = $attributes['donationMode'];
        }
        $banner = new Banner($campaign, plugin_dir_url(self::$pluginFile));
        return $banner->render();
    }

    static function setup_banner_shortcode($atts)
    {
        $shortCodeAtts = shortcode_atts([
            'campaign' => CampaignManager::getAllCampaignTypes()[0],
        ], $atts, self::$bannerShortCode);
        return (new Banner($shortCodeAtts['campaign'], plugin_dir_url(self::$pluginFile)))->render();
    }

    static function handle_styles()
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
            wp_enqueue_style('wp-donations-plugin-styles', plugin_dir_url(self::$pluginFile) . 'styles/banner.css');
        }
    }

    // menu related code

    static function setup_menu()
    {
        // add plugin pages
        add_menu_page('Spendenübersicht', 'Spendenübersicht', 'manage_options',
            self::$menuSlug, null, 'dashicons-cart');

        add_action('admin_notices', [Plugin::class, 'add_donation_info_banner']);

        // submenu page:
        add_submenu_page(self::$menuSlug, 'Einstellungen', 'Einstellungen', 'manage_options',
            'wp-donations-settings', [Plugin::class, 'handle_menu_settings']);
    }

    static function handle_menu_settings()
    {
        echo '<div class="wrap">
            <h2>Einstellungen</h2>
        </div>';
    }

    static function setup_report_post_type()
    {
        register_post_type('donation_report', [
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
                'custom-fields',
            ],
            'has_archive' => false,
            'can_export' => true,
            'map_meta_cap' => false,
            'rewrite' => [
                ['slug' => 'donation_report']
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
    }

    static function add_donation_info_banner()
    {
        $screen = get_current_screen();
        if ($screen->post_type !== 'donation_report'
            || $screen->id !== 'edit-donation_report') {
            return;
        }
        
        $allProductIds = [];
        foreach (CharityProductManager::getAllProducts() as $charityProduct) {
            /* @var $charityProduct CharityProduct */
            $allProductIds[] = get_option($charityProduct->getProductIdOptionKey());
        }
        
        // TODO add correct iban information
        $output = '<div class="notice notice-info"><p>';
        $output .= 'Dieses Plugin erweitert den Shop um mehrere Produkte, um Gelder für 
                    Wohltätigkeitsorganisationen zu sammeln.<br/>';
        $output .= sprintf('Produkt-IDs: <strong>%s</strong>', join(', ', $allProductIds)) . '<br/>';
        $output .= 'Bitte überweisen Sie in regelmäßigen Abständen die Beträge der eingenommenen Spenden 
                    unter Angabe des jeweilig gewünschten Spendenzwecks auf folgendes Konto:<br/>';
        $output .= '<strong>IBAN:</strong> DE1234567890';
        $output .= '</p></div>';
        echo $output;
    }
}