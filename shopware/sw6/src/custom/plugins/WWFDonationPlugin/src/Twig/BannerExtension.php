<?php declare(strict_types=1);

namespace WWFDonationPlugin\Twig;

use exxeta\wwf\banner\Banner;
use exxeta\wwf\banner\MiniBanner;
use Monolog\Logger;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\TwigFunction;
use WWFDonationPlugin\Service\DonationPluginInstance;
use WWFDonationPlugin\Service\MediaService;
use WWFDonationPlugin\Service\ProductService;
use WWFDonationPlugin\Service\ShopwareBannerHandler;

/**
 * Class BannerExtension
 *
 * @package WWFDonationPlugin\Twig
 */
class BannerExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var DonationPluginInstance
     */
    private $donationPluginInstance;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var Logger
     */
    private $logger;

    public function getFunctions(): array
    {
        return [
            new TwigFunction('wwfBanner', [$this, 'wwfBannerMarkup'])
        ];
    }

    /**
     * this method triggers generating the banner content markup (HTML)
     *
     * @param string $campaign
     * @param bool $isMiniBanner
     * @param string|null $miniBannerTargetPage
     * @return string
     */
    public function wwfBannerMarkup(string $campaign, bool $isMiniBanner, ?string $miniBannerTargetPage): string
    {
        $bannerHandler = new ShopwareBannerHandler(
            $this->mediaService, $this->csrfTokenManager,
            $this->productService, $miniBannerTargetPage,
            $this->logger
        );
        if ($isMiniBanner) {
            $banner = new MiniBanner($bannerHandler, $this->donationPluginInstance, $campaign);
        } else {
            $banner = new Banner($bannerHandler, $this->donationPluginInstance, $campaign);
        }
        return $banner->render();
    }

    /**
     * @return MediaService
     */
    public function getMediaService(): MediaService
    {
        return $this->mediaService;
    }

    /**
     * @param MediaService $mediaService
     */
    public function setMediaService(MediaService $mediaService): void
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @return CsrfTokenManagerInterface
     */
    public function getCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->csrfTokenManager;
    }

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager): void
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param DonationPluginInstance $donationPluginInstance
     */
    public function setDonationPluginInstance(DonationPluginInstance $donationPluginInstance): void
    {
        $this->donationPluginInstance = $donationPluginInstance;
    }

    /**
     * @param ProductService $productService
     */
    public function setProductService(ProductService $productService): void
    {
        $this->productService = $productService;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }
}