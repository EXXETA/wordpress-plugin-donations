<?php
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

namespace exxeta\wwf\banner;

/**
 * Class Banner
 *
 * generic wrapper class for banner rendering which is used by shortcode and gutenberg serverside rendered block.
 *
 * @package exxeta\wwf\banner
 */
class Banner
{
    /**
     * define the index of the default campaign which is also used as fallback of
     * AbstractCharityProductManager#getAllCharityProductSlugs()
     *
     * @var int
     */
    private $defaultCampaignIndex = 2;

    /**
     * this is always a value out of AbstractCharityProductManager#getAllCampaignTypes()
     *
     * @var string
     */
    private $campaign;

    /**
     * @var BannerHandlerInterface
     */
    private $bannerHandler;

    /**
     * @var DonationPluginInterface
     */
    private $donationPlugin;

    /**
     * Banner constructor.
     * @param BannerHandlerInterface $bannerHandler
     * @param DonationPluginInterface $donationPlugin
     * @param string $bannerType
     */
    public function __construct(BannerHandlerInterface $bannerHandler, DonationPluginInterface $donationPlugin,
                                string $bannerType)
    {
        $this->bannerHandler = $bannerHandler;
        $this->donationPlugin = $donationPlugin;

        $isValid = false;
        $allCampaigns = $donationPlugin->getCharityProductManagerInstance()->getAllCampaignTypes();
        foreach ($allCampaigns as $singleCampaign) {
            if ($singleCampaign === $bannerType) {
                $isValid = true;
                break;
            }
        }
        if (!$isValid) {
            // select default campaign - if input values were invalid
            // use fallback, take third of campaign types = protect species
            $bannerType = $allCampaigns[$this->getDefaultCampaignIndex()];
        }
        $this->campaign = $bannerType;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $charityProductManager = $this->getDonationPlugin()->getCharityProductManagerInstance();
        $campaign = $charityProductManager->getCampaignBySlug($this->getCampaign());
        if (!$campaign) {
            error_log(sprintf("Invalid campaign for slug '%s'", $this->campaign));
            return '';
        }
        $product = $charityProductManager->getProductBySlug($this->campaign);
        if (!$product) {
            error_log(sprintf("Invalid product for campaign slug '%s'", $this->campaign));
            return '';
        }
        $randomString = uniqid();
        $moreInfoId = sprintf('donation-campaign-more-info-%s-%s', $campaign->getSlug(), $randomString);
        $infoAreaId = sprintf('donation-campaign-more-info-area-%s-%s', $campaign->getSlug(), $randomString);
        $hideInfoAreaId = sprintf('donation-campaign-hide-more-info-area-%s-%s', $campaign->getSlug(), $randomString);

        // start to generate output
        if ($this->getDonationPlugin()->getCustomClass()) {
            $output = sprintf('<div class="cart-donation-banner %s %s">',
                $campaign->getClass(), $this->getDonationPlugin()->getCustomClass());
        } else {
            $output = sprintf('<div class="cart-donation-banner %s">', $campaign->getClass());
        }
        $output .= sprintf('<div class="cart-donation-banner-background %s">', $campaign->getClass());
        $output .= '<div class="cart-banner-content">';
        $output .= sprintf('<p class="cart-banner-title">%s</p>', $campaign->getHeadline());
        $output .= sprintf('<p class="donation-campaign-description">%s. ', $campaign->getDescription());

        $output .= sprintf('Klicke <a id="%s" href="#" title="Mehr Informationen über die Spende">hier</a> für weitere Informationen</p>',
            $moreInfoId,
        );

        // form starts here
        $output .= sprintf('<div class="donation-campaign-order"><form method="%s" %s action="%s">',
            $this->getBannerHandler()->getFormMethod(),
            $this->getBannerHandler()->getFormAttributes(),
            $this->getBannerHandler()->getCartUrl());

        // do not add a line break here!
        $output .= sprintf('<img class="donation-campaign-logo" alt="" src="%s" /><span class="times"></span>',
            $this->getBannerHandler()->getLogoImageUrl($product));

        $this->getBannerHandler()->applyCartFormHook($output, $product);

        // add quantity field
        $output .= sprintf('<input class="donation-campaign-quantity-input" type="number" value="1" min="1" name="%s" />',
            $this->getBannerHandler()->getFormQuantityInputName());

        $output .= '<button class="donation-campaign-submit" type="submit">';
        $output .= sprintf('<img class="cart-icon" src="%s" alt="cart icon" /><span class="donation-campaign-cart-text">%s</span>',
            $this->getBannerHandler()->getCartImageUrl(), $campaign->getButtonDescription());
        $output .= '</button></form></div>';

        $output .= '</div>'; // .cart-banner-content
        $output .= '</div>'; // .cart-donation-banner-background

        // add collapsible content here
        $output .= sprintf('<div class="donation-campaign-collapsible" id="%s">', $infoAreaId);
        $output .= sprintf('<p class="donation-campaign-more-info">%s', $campaign->getFullText());

        $defaultClosingText = 'Informationstext schlie&szlig;en';

        $output .= sprintf('&nbsp;<br/><br/><a href="#" id="%s" class="fade-out-link">%s</a></p>',
            $hideInfoAreaId, $defaultClosingText);
        $output .= '</div>'; // .donation-campaign-collapsible

        // this js needs to be plain js to support a wide variety of themes/browsers etc.
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

    /**
     * @return string
     */
    public function getCampaign(): string
    {
        return $this->campaign;
    }

    /**
     * @return int
     */
    public function getDefaultCampaignIndex(): int
    {
        return $this->defaultCampaignIndex;
    }

    /**
     * @return BannerHandlerInterface
     */
    public function getBannerHandler(): BannerHandlerInterface
    {
        return $this->bannerHandler;
    }

    /**
     * @return DonationPluginInterface
     */
    public function getDonationPlugin(): DonationPluginInterface
    {
        return $this->donationPlugin;
    }
}