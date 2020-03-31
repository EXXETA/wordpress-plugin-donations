<?php


namespace donations;


/**
 * Class Banner
 *
 * wrapper class for banner rendering which is used by shortcode and gutenberg serverside rendered block.
 *
 * @package donations
 */
class Banner
{
    /**
     * this is always a value out of CharityProductManager::getAllCampaignTypes()
     *
     * @var string
     */
    private $campaign;

    /**
     * Banner constructor.
     * @param string $bannerType
     */
    public function __construct(string $bannerType)
    {
        $isValid = false;
        foreach (CampaignManager::getAllCampaignTypes() as $singleCampaign) {
            if ($singleCampaign === $bannerType) {
                $isValid = true;
                break;
            }
        }
        if (!$isValid) {
            // use fallback, take first of campaign types
            $bannerType = CampaignManager::getAllCampaignTypes()[0];
        }
        $this->campaign = $bannerType;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $campaign = CampaignManager::getCampaignBySlug($this->campaign);

        $output = '<div class="cart-donation-banner">';
        $output .= '<div class="col-left left-banner-image"></div>';
        $output .= '<div class="col-right right-banner-text">';
        $output .= sprintf('<p>%s</p>', $campaign->getDescription());
        $output .= sprintf('<p><a href="%s">Klicke hier für weitere Informationen</a></p>', $campaign->getDetailURL());
        $output .= '</div>'; // .right-banner-text
        $output .= '</div>'; // .donation-banner

        return $output;
    }
}