<?php declare(strict_types=1);

namespace WWFDonationPlugin\Twig;

use exxeta\wwf\banner\Banner;
use exxeta\wwf\banner\MiniBanner;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\TwigFunction;
use WWFDonationPlugin\Service\DonationPluginInstance;
use WWFDonationPlugin\Service\MediaService;
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

    public function getFunctions()
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
     * @return string
     */
    public function wwfBannerMarkup(string $campaign, bool $isMiniBanner)
    {
        $bannerHandler = new ShopwareBannerHandler($this->mediaService, $this->csrfTokenManager);
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
}