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

    public static $bannerShortCode = 'wp_donations_banner';
    public static $blockTypeName = 'wp-donations-plugin/checkout-banner';

    /**
     * Plugin constructor.
     * @param string $pluginFile
     */
    public function __construct(string $pluginFile)
    {
        self::$pluginFile = $pluginFile;
    }

    public function registerPluginHooks()
    {
        register_activation_hook(self::$pluginFile, [Plugin::class, 'activate']);
        register_deactivation_hook(self::$pluginFile, [Plugin::class, 'deactivate']);
        register_uninstall_hook(self::$pluginFile, [Plugin::class, 'uninstall']);
        // register gutenberg block
        add_action('init', [Plugin::class, 'setup_banner_block']);
        // register shortcode
        add_shortcode(self::$bannerShortCode, [Plugin::class, 'setup_banner_shortcode']);
    }

    // (de-)activation and (un-)install logic

    /**
     * this is called by wordpress if the plugin is activated
     * This is the place to init all products at once and store their WooCommerce product IDs in wordpress options.
     */
    private function activate()
    {
        // FIXME implement settings
        // add plugin pages
        // add_menu_page('Test Plugin Page', 'Donations Plugin', 'manage_options', 'wp-donations-plugin', 'init_menu_page');
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
                $product->set_virtual(true);
                $product->set_catalog_visibility('hidden');
                $product->save();

                $product_id = $product->get_id();
                update_option($singleProduct->getProductIdOptionKey(), $product_id);
            }
        }
    }

    /**
     * this is called if the plugin is disabled
     */
    private function deactivate()
    {
        // atm there is nothing to do here
        remove_shortcode(self::$bannerShortCode);

        if (function_exists('unregister_block_type')) {
            // Gutenberg is not active.
            unregister_block_type(self::$blockTypeName);
        }
    }

    /**
     * this is called if the plugin is uninstalled
     */
    private function uninstall()
    {
        // remove products of this plugin
        foreach (CharityProductManager::getAllProducts() as $singleProduct) {
            /* @var $singleProduct \donations\CharityProduct */
            $product_id = get_option($singleProduct->getProductIdOptionKey());
            if (!empty($product_id)) {
                // delete product
                $product = wc_get_product($product_id);
                $product->delete();
            }
            // delete option
            delete_option($singleProduct->getProductIdOptionKey());
        }

        // remove woo commerce product category which is technically a wordpress term
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
        $asset_file = include(plugin_dir_path(self::$pluginFile) . 'build/index.asset.php');
        wp_register_script('checkout-charity-banner', plugins_url('build/index.js', self::$pluginFile),
            $asset_file['dependencies'],
            $asset_file['version']
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
        $banner = new Banner($campaign);
        return $banner->render();
    }

    static function setup_banner_shortcode($atts) {
        $bannerType = shortcode_atts([
            'campaign' => CampaignManager::getAllCampaignTypes()[0],
        ], $atts, self::$bannerShortCode);
        return (new Banner($bannerType['campaign']))->render();
    }
}