<?php


namespace donations;


/**
 * Class Banner
 *
 * wrapper class for banner rendering which is used by shortcode and gutenberg serverside rendered block.
 *
 * @package donations
 */
class Banner
{
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
            // use fallback, take first of campaign types
            $bannerType = CampaignManager::getAllCampaignTypes()[0];
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
        $product = CharityProductManager::getProductBySlug($this->campaign);
        $productId = get_option($product->getProductIdOptionKey());

        $randomString = uniqid();
        $moreInfoId = sprintf('donation-campaign-more-info-%s-%s', $campaign->getSlug(), $randomString);
        $infoAreaId = sprintf("donation-campaign-more-info-area-%s-%s", $campaign->getSlug(), $randomString);
        $hideInfoAreaId = sprintf("donation-campaign-hide-more-info-area-%s-%s", $campaign->getSlug(), $randomString);

        $output = sprintf('<div class="cart-donation-banner %s">', $campaign->getClass());
        $output .= sprintf('<div class="cart-donation-banner-background %s">', $campaign->getClass());
        $output .= '<div class="cart-banner-content">';
        #$output .= '<p class="cart-banner-title">Yoga for the protection of endangered animal species and biological diversity</p>';
        $output .= sprintf('<p class="cart-banner-title">%s</p>', $campaign->getHeadline());
        $output .= sprintf('<p class="donation-campaign-description">%s<br/> ', $campaign->getDescription());

        $output .= sprintf('Click <a id="%s" href="#" 
                    title="More information about the donation">here</a> for more information</p>', $moreInfoId);

        $cartUrl = wc_get_cart_url();
        $output .= sprintf('<div class="donation-campaign-order"><form method="GET" action="%s">', $cartUrl);
        // WWF logo

        $output .= sprintf('<img class="donation-campaign-logo" alt="donation target logo" src="%s" />
                            <span class="times"></span>', wp_get_attachment_image_url(get_option($product->getImageIdOptionKey())));

        if (strpos($cartUrl, '?page_id=') !== false) {
            // "nice" urls are not enabled/supported, add page_id as hidden input field to redirect to cart properly
            $cartPageId = wc_get_page_id('cart');
            $output .= sprintf('<input type="hidden" value="%d" name="page_id" />', $cartPageId);
        }

        // NOTE: input names are very important to create a valid form action for WooCommerce cart
        $output .= sprintf('<input type="hidden" value="%d" name="add-to-cart" />', $productId);
        $output .= '<input class="donation-campaign-quantity-input" type="number" value="1" min="1" name="quantity" />';
        $output .= '<button class="donation-campaign-submit" type="submit">';
        $output .= sprintf('<img class="cart-icon" src="%s" alt="" /><span class="donation-campaign-cart-text">%s</span>',
            $this->pluginUrl, $campaign->getButtonDescription() . 'images/icon_cart.svg');
        $output .= '</button></form></div>';

        $output .= '</div>'; // .cart-banner-content
        $output .= '</div>'; // .cart-donation-banner-background

        // add collapsible content here
        $output .= sprintf('<div class="donation-campaign-collapsible" id="%s">', $infoAreaId);
        $output .= sprintf('<p class="donation-campaign-more-info">%s', $campaign->getFullText());
        $output .= sprintf('&nbsp;<a href="#" id="%s" class="fade-out-link">(Ausblenden)</a></p>', $hideInfoAreaId);
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
}