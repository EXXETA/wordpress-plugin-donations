<?php declare(strict_types=1);
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

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