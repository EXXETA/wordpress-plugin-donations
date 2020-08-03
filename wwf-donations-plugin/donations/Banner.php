<?php


namespace donations;


/**
 * Class Banner
 *
 * wrapper class for banner rendering which is used by shortcode and gutenberg serverside rendered block.
 *
 * TODO: make more generic and independent of Wordpress and wooCommerce logic
 *
 * @package donations
 */
class Banner
{
    /**
     * define the index of the default campaign which is also used as fallback of
     * CharityProductManager::getAllCharityProductSlugs()
     *
     * @var int
     */
    protected $defaultCampaignIndex = 2;

    /**
     * this is always a value out of CharityProductManager::getAllCampaignTypes()
     *
     * @var string
     */
    private $campaign;

    /**
     * @var string
     */
    private $pluginUrl;

    /**
     * Banner constructor.
     * @param string $bannerType
     */
    public function __construct(string $bannerType, string $pluginUrl)
    {
        $isValid = false;

        foreach (CampaignManager::getAllCampaignTypes() as $singleCampaign) {
            if ($singleCampaign === $bannerType) {
                $isValid = true;
                break;
            }
        }
        if (!$isValid) {
            // select default campaign - if input values were invalid
            // use fallback, take third of campaign types = protect species
            $bannerType = CampaignManager::getAllCampaignTypes()[$this->defaultCampaignIndex];
        }
        $this->campaign = $bannerType;
        $this->pluginUrl = $pluginUrl;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $campaign = CampaignManager::getCampaignBySlug($this->campaign);
        if (!$campaign) {
            error_log(sprintf("Invalid campaign for slug '%s'", $this->campaign));
            return "";
        }
        $product = CharityProductManager::getProductBySlug($this->campaign);
        if (!$product) {
            error_log(sprintf("Invalid product for campaign slug '%s'", $this->campaign));
            return "";
        }
        $productId = get_option($product->getProductIdOptionKey());
        $wcProduct = wc_get_product($productId);
        $attachmentId = $this->getImageAttachmentIdByProduct($product, $wcProduct);

        $randomString = uniqid();
        $moreInfoId = sprintf('donation-campaign-more-info-%s-%s', $campaign->getSlug(), $randomString);
        $infoAreaId = sprintf("donation-campaign-more-info-area-%s-%s", $campaign->getSlug(), $randomString);
        $hideInfoAreaId = sprintf("donation-campaign-hide-more-info-area-%s-%s", $campaign->getSlug(), $randomString);

        // start to generate output
        $output = sprintf('<div class="cart-donation-banner %s">', $campaign->getClass());
        $output .= sprintf('<div class="cart-donation-banner-background %s">', $campaign->getClass());
        $output .= '<div class="cart-banner-content">';
        $output .= sprintf('<p class="cart-banner-title">%s</p>', $campaign->getHeadline());
        $output .= sprintf('<p class="donation-campaign-description">%s. ', $campaign->getDescription());

        $output .= sprintf('Klicke <a id="%s" href="#" title="Mehr Informationen über die Spende">hier</a> für weitere Informationen</p>',
            $moreInfoId,
        );

        $cartUrl = wc_get_cart_url();
        $output .= sprintf('<div class="donation-campaign-order"><form method="GET" action="%s">', $cartUrl);

        // do not add a line break here!
        $output .= sprintf('<img class="donation-campaign-logo" alt="" src="%s" /><span class="times"></span>',
            wp_get_attachment_image_url($attachmentId));

        $this->applyWooCartFormData($cartUrl, $output, $productId);

        $output .= '<button class="donation-campaign-submit" type="submit">';
        $output .= sprintf('<img class="cart-icon" src="%s" alt="" /><span class="donation-campaign-cart-text">%s</span>',
            $this->pluginUrl . 'images/icon_cart.svg', $campaign->getButtonDescription());
        $output .= '</button></form></div>';

        $output .= '</div>'; // .cart-banner-content
        $output .= '</div>'; // .cart-donation-banner-background

        // add collapsible content here
        $output .= sprintf('<div class="donation-campaign-collapsible" id="%s">', $infoAreaId);
        $output .= sprintf('<p class="donation-campaign-more-info">%s', $campaign->getFullText());

        $defaultClosingText = 'Informationstext schlie&szlig;en';

        $output .= sprintf('&nbsp;<br/><br/><a href="#" id="%s" class="fade-out-link">%s</a></p>',
            $hideInfoAreaId, $defaultClosingText);
        $output .= '</div>'; // .donation-campaign-collapsible

        $output .= <<<SCRIPT
<script lang="js">
(function() {
    const moreInfoButton = document.getElementById("$moreInfoId");
    const moreInfoArea = document.getElementById("$infoAreaId");
    moreInfoButton.addEventListener("click", e => {
        e.preventDefault();
        moreInfoArea.classList.toggle("fade");
    });
    const hideInfoArea = document.getElementById("$hideInfoAreaId");
    hideInfoArea.addEventListener("click", e => {
        e.preventDefault();
        moreInfoArea.classList.remove("fade");
    });
})();
</script>
SCRIPT;

        $output .= '</div>'; // .cart-donation-banner

        return $output;
    }

    /**
     * @return string
     */
    public function getCampaign(): string
    {
        return $this->campaign;
    }

    /**
     * @return string
     */
    public function getPluginUrl(): string
    {
        return $this->pluginUrl;
    }

    /**
     * @param CharityProduct $product
     * @param $wcProduct
     * @return int
     */
    protected function getImageAttachmentIdByProduct(CharityProduct $product, $wcProduct): int
    {
        $attachmentId = intval(get_option($product->getImageIdOptionKey()));

        if ($wcProduct instanceof \WC_Product) {
            $productAttachmentId = intval($wcProduct->get_image_id());
            if ($productAttachmentId && $productAttachmentId > 0 && $productAttachmentId != $attachmentId) {
                $attachmentId = $productAttachmentId;
            }
        }
        return $attachmentId;
    }

    /**
     * this method uses the &$output reference directly
     *
     * @param string $cartUrl
     * @param string &$output
     * @param bool $productId
     */
    protected function applyWooCartFormData(string $cartUrl, string &$output, bool $productId)
    {
        if (strpos($cartUrl, '?page_id=') !== false) {
            // "nice" urls are not enabled/supported, add page_id as hidden input field to redirect to cart properly
            $cartPageId = wc_get_page_id('cart');
            $output .= sprintf('<input type="hidden" value="%d" name="page_id" />', $cartPageId);
        }

        // NOTE: input names are very important to create a valid form action for WooCommerce cart
        $output .= sprintf('<input type="hidden" value="%d" name="add-to-cart" />', $productId);
        $output .= '<input class="donation-campaign-quantity-input" type="number" value="1" min="1" name="quantity" />';
    }
}