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

        $output = '<div class="cart-donation-banner">';
        $output .= '<div class="col-left"></div>';
        $output .= '<div class="col-right">';
        $output .= sprintf('<p class="donation-campaign-description">%s</p>', $campaign->getDescription());
        $output .= sprintf('<p class="donation-campaign-details"><a href="%s">Klicke hier für weitere Informationen</a></p>', $campaign->getDetailURL());

        $cartUrl = wc_get_cart_url();
        $output .= sprintf('<div class="donation-campaign-order"><form method="GET" action="%s">', $cartUrl);
        // WWF logo
        $output .= sprintf('<img class="donation-campaign-logo" alt="donation target logo" src="%s" /> <span class="times">x</span>', $this->pluginUrl. 'images/wwf_logo.jpg');

        if (strpos($cartUrl, '?page_id=') !== false) {
            // "nice" urls are not enabled/supported, add page_id as hidden input field to redirect to cart properly
            $cartPageId = wc_get_page_id('cart');
            $output .= sprintf('<input type="hidden" value="%d" name="page_id" />', $cartPageId);
        }

        // NOTE: input names are very important to create a valid form action for WooCommerce cart
        $output .= sprintf('<input type="hidden" value="%d" name="add-to-cart" />', $productId);
        $output .= '<input class="donation-campaign-quantity-input" type="number" value="1" min="1" name="quantity" />';
        $output .= '<button class="donation-campaign-submit" type="submit">';
        $output .= sprintf('<img class="cart-icon" src="%s" alt="" />In den Warenkorb', $this->pluginUrl . 'images/cart-plus-solid.svg');
        $output .= '</button></form></div>';

        $output .= '</div>'; // .right-banner-text
        $output .= '</div>'; // .donation-banner

        return $output;
    }
}