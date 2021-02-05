<?php

namespace WWFDonationPlugin\Twig;

use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\Banner;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\TwigFunction;
use WWFDonationPlugin\Service\DonationPluginInstance;
use WWFDonationPlugin\Service\MediaService;
use WWFDonationPlugin\Service\ShopwareBannerHandler;

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

    public function getFunctions()
    {
        return [
            new TwigFunction('wwfBanner', [$this, 'wwfBannerMarkup'])
        ];
    }

    public function wwfBannerMarkup()
    {
        $bannerHandler = new ShopwareBannerHandler($this->mediaService, $this->csrfTokenManager);

        // TODO make campaign dynamic
        $banner = new Banner($bannerHandler, new DonationPluginInstance(), AbstractCharityProductManager::$PROTECT_CLIMATE_COIN);
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
}