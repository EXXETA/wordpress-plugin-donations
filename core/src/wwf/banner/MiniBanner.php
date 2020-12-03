<?php


namespace exxeta\wwf\banner;

/**
 * Class MiniBanner
 *
 * Derived from the default Banner rendering class and at the same time this class is a "reference" of how to
 * add custom instances of the banner markup this package provides.
 *
 * @package exxeta\wwf\banner;
 */
class MiniBanner extends Banner
{
    /**
     * MiniBanner constructor.
     *
     * @param BannerHandlerInterface $bannerHandler
     * @param DonationPluginInterface $donationPlugin
     * @param string|null $campaign
     */
    public function __construct(BannerHandlerInterface $bannerHandler, DonationPluginInterface $donationPlugin,
                                ?string $campaign)
    {
        if ($campaign === null) {
            // use preconfigured value - but allow specific overwrite
            $bannerType = call_user_func($donationPlugin->getSettingsManager() . '::getMiniBannerCampaign');
            if ($bannerType === null || $bannerType == "null") {
                // use default as fallback
                $bannerType = call_user_func($donationPlugin->getCampaignManager() . '::getAllCampaignTypes', $this->getDefaultCampaignIndex());
            }
        } else {
            $bannerType = $campaign;
        }
        parent::__construct($bannerHandler, $donationPlugin, $bannerType);
    }

    public function render(): string
    {
        $campaign = call_user_func($this->getDonationPlugin()->getCampaignManager() . '::getCampaignBySlug',
            $this->getCampaign());
        if (!$campaign) {
            error_log(sprintf("Invalid campaign for slug '%s'", $this->getCampaign()));
            return '';
        }
        $product = call_user_func($this->getDonationPlugin()->getCharityProductManager() . '::getProductBySlug',
            $this->getCampaign());
        if (!$product) {
            error_log(sprintf("Invalid product for campaign slug '%s'", $this->getCampaign()));
            return '';
        }

        $output = sprintf('<div class="cart-donation-mini-banner %s">', $campaign->getClass());
        $output .= '<div class="coin-area">';
        $output .= sprintf('<img class="campaign-logo" alt="" src="%s" />', $this->getBannerHandler()->getLogoImageUrl($product));
        $output .= '</div>';

        $output .= '<div class="desc-area">';
        $optionMiniBannerCampaignTarget = call_user_func($this->getDonationPlugin()->getSettingsManager() . '::getMiniBannerCampaignTargetPageId');
        if (!$optionMiniBannerCampaignTarget) {
            // use "cart" as default - if nothing is defined
            $optionMiniBannerCampaignTarget = $this->getBannerHandler()->getCartPageId();
        }
        $output .= sprintf('<p>%s<br/><a class="more_info_link" href="%s" title="Mehr Informationen zur Spendenkampagne anzeigen">Mehr Informationen</a></p>',
            'Möchtest Du den WWF mit einer Spende unterstützen?',
            $this->getBannerHandler()->getMiniBannerTargetPageUrl($optionMiniBannerCampaignTarget));

        $this->getBannerHandler()->applyMiniBannerCartRowHook($output, $product);

        $output .= '</div>'; //.desc-area
        $output .= '</div>'; //.cart-donation-mini-banner
        return $output;
    }

    public function getDefaultCampaignIndex(): int
    {
        return $defaultCampaignIndex = 4; // = diversity coin;
    }
}