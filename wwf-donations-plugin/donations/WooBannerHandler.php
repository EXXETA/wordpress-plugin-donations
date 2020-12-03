<?php


namespace donations;

use exxeta\wwf\banner\model\CharityProduct;

/**
 * Class WooBannerHandler
 * @package donations
 */
class WooBannerHandler implements \exxeta\wwf\banner\BannerHandlerInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * WooBannerHandler constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getLogoImageUrl(CharityProduct $charityProduct): string
    {
        $productId = $this->getProductId($charityProduct);
        $wcProduct = wc_get_product($productId);
        $attachmentId = $this->getImageAttachmentIdByProduct($charityProduct, $wcProduct);
        return wp_get_attachment_image_url($attachmentId);
    }

    public function getProductId(CharityProduct $charityProduct): int
    {
        return intval(get_option($charityProduct->getProductIdSettingKey()));
    }

    /**
     * @param CharityProduct $product
     * @param $wcProduct
     * @return int
     */
    protected function getImageAttachmentIdByProduct(CharityProduct $product, $wcProduct): int
    {
        $attachmentId = intval(get_option($product->getImageIdSettingKey()));

        if ($wcProduct instanceof \WC_Product) {
            $productAttachmentId = intval($wcProduct->get_image_id());
            if ($productAttachmentId && $productAttachmentId > 0 && $productAttachmentId != $attachmentId) {
                $attachmentId = $productAttachmentId;
            }
        }

        return $attachmentId;
    }

    public function getCartUrl(): string
    {
        return wc_get_cart_url();
    }

    public function applyCartFormHook(&$output, CharityProduct $charityProduct): void
    {
        if (strpos($this->getCartUrl(), '?page_id=') !== false) {
            // "nice" urls are not enabled/supported, add page_id as hidden input field to redirect to cart properly
            $cartPageId = wc_get_page_id('cart');
            $output .= sprintf('<input type="hidden" value="%d" name="page_id" />', $cartPageId);
        }

        // NOTE: input names are very important to create a valid form action for WooCommerce cart
        $output .= sprintf('<input type="hidden" value="%d" name="add-to-cart" />', $this->getProductId($charityProduct));
    }

    public function getFormQuantityInputName(): string
    {
        return 'quantity';
    }

    public function getCartImageUrl(): string
    {
        return $this->getBaseUrl() . 'images/icon_cart.svg';
    }

    public function getMiniBannerTargetPageUrl($pageId): string
    {
        return get_page_uri($pageId);
    }

    public function getCartPageId(): int
    {
        return wc_get_page_id('cart');
    }

    public function applyMiniBannerCartRowHook(string &$output, CharityProduct $charityProduct): void
    {
        $productId = $this->getProductId($charityProduct);
        $wcProduct = wc_get_product($productId);

        $output .= '<div class="donation-cart-row">';
        $output .= '<div class="quantity-field"><input class="donation-campaign-mini-quantity-input" type="number" value="1" min="1" name="quantity" /></div>';
        $output .= sprintf('<div class="button-field"><a rel="nofollow" href="%s" value="%s" data-quantity="1" data-product_id="%s" class="ajax_add_to_cart add_to_cart_button">In den Warenkorb</a></div>',
            esc_attr($wcProduct->add_to_cart_url()),
            esc_attr($wcProduct->get_id()),
            esc_attr($wcProduct->get_id())
        );
        $output .= '</div>'; //.donation-cart-row
    }
}