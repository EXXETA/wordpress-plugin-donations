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
     * ShopwareBannerHandler constructor.
     * @param MediaService $mediaService
     */
    public function __construct(MediaService $mediaService, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->mediaService = $mediaService;
        $this->csrfTokenManager = $csrfTokenManager;
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
        // TODO: Implement getCartImageUrl() method.
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
        // TODO: Implement getBaseUrl() method.
    }

    public function getCartUrl(): string
    {
        return '/checkout/cart';
    }

    public function getMiniBannerTargetPageUrl($pageId): string
    {
        // TODO: Implement getMiniBannerTargetPageUrl() method.
    }

    public function getCartPageId(): int
    {
        // TODO: Implement getCartPageId() method.
    }

    public function applyMiniBannerCartRowHook(string &$output, CharityProduct $charityProduct): void
    {
        // TODO: Implement applyMiniBannerCartRowHook() method.
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