<?php


namespace donations;

/**
 * Class MiniBanner
 * @package donations
 */
class MiniBanner extends Banner
{
    /**
     * @see parent::$defaultCampaignIndex
     * @var int
     */
    protected $defaultCampaignIndex = 4; // = diversity coin

    /**
     * MiniBanner constructor.
     * @param string|null $campaign
     * @param string $pluginUrl
     */
    public function __construct(?string $campaign, string $pluginUrl)
    {
        if ($campaign === null) {
            // use preconfigured value - but allow specific overwrite
            $bannerType = SettingsManager::getMiniBannerCampaign();
            if ($bannerType === null || $bannerType == "null") {
                // use default
                $bannerType = CampaignManager::getAllCampaignTypes()[$this->defaultCampaignIndex];
            }
        } else {
            $bannerType = $campaign;
        }
        parent::__construct($bannerType, $pluginUrl);
    }

    public function render(): string
    {
        $campaign = CampaignManager::getCampaignBySlug($this->getCampaign());
        if (!$campaign) {
            error_log(sprintf("Invalid campaign for slug '%s'", $this->getCampaign()));
            return "";
        }
        $product = CharityProductManager::getProductBySlug($this->getCampaign());
        if (!$product) {
            error_log(sprintf("Invalid product for campaign slug '%s'", $this->getCampaign()));
            return "";
        }
        $productId = get_option($product->getProductIdOptionKey());
        $wcProduct = wc_get_product($productId);
        $attachmentId = $this->getImageAttachmentIdByProduct($product, $wcProduct);

        $output = sprintf('<div class="cart-donation-mini-banner %s">', $campaign->getClass());

        $output .= '<div class="coin-area">';
        $output .= sprintf('<img class="campaign-logo" alt="" src="%s" />', wp_get_attachment_image_url($attachmentId));
        $output .= '</div>';
        $output .= '<div class="desc-area">';
        $optionMiniBannerCampaignTarget = SettingsManager::getMiniBannerCampaignTarget();
        if (!$optionMiniBannerCampaignTarget) {
            // use "cart" as default - if nothing is defined
            $optionMiniBannerCampaignTarget = wc_get_page_id('cart');
        }
        $output .= sprintf('<p>%s<br/><a class="more_info_link" href="%s" title="Mehr Informationen zur Spendenkampagne anzeigen">Mehr Informationen</a></p>',
            'Möchtest Du den WWF mit einer Spende unterstützen?',
            get_page_link($optionMiniBannerCampaignTarget));

        $output = $this->getDonationCartRow($output, $wcProduct);

        $output .= '</div>'; //.desc-area
        $output .= '</div>'; //.cart-donation-mini-banner
        return $output;
    }

    /**
     * @param string $output
     * @param bool|null $wcProduct
     * @return string
     */
    public function getDonationCartRow(string $output, ?\WC_Product $wcProduct): string
    {
        $output .= '<div class="donation-cart-row">';
        $output .= '<div class="quantity-field"><input class="donation-campaign-mini-quantity-input" type="number" value="1" min="1" name="quantity" /></div>';
        $output .= sprintf('<div class="button-field"><a rel="nofollow" href="%s" value="%s" data-quantity="1" data-product_id="%s" class="ajax_add_to_cart add_to_cart_button">In den Warenkorb</a></div>',
            esc_attr($wcProduct->add_to_cart_url()),
            esc_attr($wcProduct->get_id()),
            esc_attr($wcProduct->get_id())
        );
        $output .= '</div>';
        return $output;
    }
}