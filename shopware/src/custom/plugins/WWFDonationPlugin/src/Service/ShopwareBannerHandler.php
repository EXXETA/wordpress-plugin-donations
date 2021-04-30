<?php


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\BannerHandlerInterface;
use exxeta\wwf\banner\model\CharityProduct;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use WWFDonationPlugin\WWFDonationPlugin;

class ShopwareBannerHandler implements BannerHandlerInterface
{
    // injected via setter
    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var string|null
     */
    protected $targetPageId;

    /**
     * ShopwareBannerHandler constructor.
     * @param MediaService $mediaService
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param ProductService $productService
     * @param string|null $targetPageId
     */
    public function __construct(MediaService $mediaService, CsrfTokenManagerInterface $csrfTokenManager, ProductService $productService, ?string $targetPageId)
    {
        $this->mediaService = $mediaService;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->productService = $productService;
        $this->targetPageId = $targetPageId;
    }

    public function getLogoImageUrl(CharityProduct $charityProduct): string
    {
        $filenameWithoutExt = basename($charityProduct->getImagePath(), '.png');
        $mediaEntity = $this->mediaService->getPluginMediaRecordByFilename($filenameWithoutExt);
        if (!$mediaEntity) {
            // TODO log this case!
            return "";
        }
        return $mediaEntity->getUrl();
    }

    public function getCartImageUrl(): string
    {
        return $this->getBaseUrl() . 'images/icon_cart.svg';
    }

    public function getProductId(CharityProduct $charityProduct): int
    {
        // FIXME needs to be of type string!!!
        return 'e391a3d564224973956fbbf773c89d68';
        // TODO: Implement getProductId() method.
    }

    public function getBaseUrl(): string
    {
        return '/bundles/wwfdonationplugin/';
    }

    public function getCartUrl(): string
    {
        return '/wwfdonation/add-donation-line-item';
    }

    public function getMiniBannerTargetPageUrl($pageId): string
    {
        return $this->targetPageId;
    }

    public function getCartPageId(): int
    {
        // method not used here in this context
        return 0;
    }

    public function applyMiniBannerCartRowHook(string &$output, CharityProduct $charityProduct): void
    {
        $output .= '<div class="donation-cart-row">';
        $output .= sprintf('<form class="mini-banner-add-to-cart-form" method="GET" action="%s" data-form-csrf-handler="true"
                              data-form-validation="true">', $this->getCartUrl() . '-ajax');
        $this->applyCartFormHook($output, $charityProduct);

        $output .= sprintf('<div class="quantity-field"><input class="donation-campaign-mini-quantity-input" type="number" value="1" min="1" name="%s" /></div>', $this->getFormQuantityInputName());
        $output .= '<div class="button-field"><input type="submit" value="In den Warenkorb" class="add_to_cart_button" /></div>';
        $output .= '</form></div>'; //.donation-cart-row
    }

    public function applyCartFormHook(&$output, CharityProduct $charityProduct): void
    {
        $output .= sprintf('<input name="donation" type="hidden" value="%s"/>', $charityProduct->getSlug());
        $output .= sprintf('<input name="banner_csrf_token" type="hidden" value="%s"/>', $this->csrfTokenManager->getToken(WWFDonationPlugin::CSRF_TOKEN_ID));
    }

    public function getFormQuantityInputName(): string
    {
        return 'quantity';
    }
}