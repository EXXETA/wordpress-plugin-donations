<?php declare(strict_types=1);

namespace WWFDonationPlugin\Service;

use exxeta\wwf\banner\BannerHandlerInterface;
use exxeta\wwf\banner\model\CharityProduct;
use Monolog\Logger;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use WWFDonationPlugin\WWFDonationPlugin;

/**
 * Class ShopwareBannerHandler
 *
 * shopware specific implementation details of the generic wwf banners
 *
 * @package WWFDonationPlugin\Service
 */
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
     * @var Logger
     */
    protected $logger;

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
    public function __construct(MediaService $mediaService, CsrfTokenManagerInterface $csrfTokenManager,
                                ProductService $productService, ?string $targetPageId,
                                Logger $logger)
    {
        $this->mediaService = $mediaService;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->productService = $productService;
        $this->targetPageId = $targetPageId;
        $this->logger = $logger;
    }

    public function getLogoImageUrl(CharityProduct $charityProduct): string
    {
        $filenameWithoutExt = basename($charityProduct->getImagePath(), '.png');
        $mediaEntity = $this->mediaService->getPluginMediaRecordByFilename($filenameWithoutExt);
        if (!$mediaEntity) {
            $this->logger->addError(sprintf('Could not find media record for image name "%s"',
                $charityProduct->getImagePath()));
            return "";
        }
        return $mediaEntity->getUrl();
    }

    public function getCartImageUrl(): string
    {
        return $this->getBaseUrl() . 'images/icon_cart.svg';
    }

    public function getProductId(CharityProduct $charityProduct): string
    {
        // this method is never used by the shopware implementation
        return '';
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
        // method not used here in this shopware context
        return 0;
    }

    /**
     * build extra row for mini banner cart add form
     *
     * @param string $output
     * @param CharityProduct $charityProduct
     */
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