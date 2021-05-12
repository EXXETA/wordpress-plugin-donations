<?php


namespace exxeta\wwf\banner;

/**
 * Class DonationPlugin
 *
 * Generic implementation of a donation plugin
 *
 * @package exxeta\wwf\banner
 */
class DonationPlugin implements DonationPluginInterface
{
    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var CharityProductManagerInterface
     */
    private $charityProductManager;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * a custom css class added to the (mini-)banner markup
     * @var string|null
     */
    private $customClass = null;

    /**
     * DonationPlugin constructor.
     *
     * @param string $pluginName
     * @param CharityProductManagerInterface $charityProductManager
     * @param SettingsManagerInterface $settingsManager
     * @param string|null $customClass optional css top-level class
     */
    public function __construct(string $pluginName, CharityProductManagerInterface $charityProductManager,
                                SettingsManagerInterface $settingsManager, ?string $customClass)
    {
        $this->pluginName = $pluginName;
        $this->charityProductManager = $charityProductManager;
        $this->settingsManager = $settingsManager;
        if ($customClass) {
            $this->customClass = $customClass;
        }
    }

    public function getCharityProductManagerInstance(): CharityProductManagerInterface
    {
        return $this->charityProductManager;
    }

    public function getSettingsManagerInstance(): SettingsManagerInterface
    {
        return $this->settingsManager;
    }

    /**
     * @return string|null
     */
    public function getCustomClass(): ?string
    {
        return $this->customClass;
    }

    /**
     * You should not change this
     *
     * @param array $args
     */
    public final function includeContentTemplate(array $args): void
    {
        include(__DIR__ . '/template/content.php');
    }

    /**
     * You should not change this
     *
     * @param array $args
     */
    public final function includeReportTemplate(array $args): void
    {
        include(__DIR__ . '/template/report.php');
    }

    public final function includePlainTemplate(array $args): void
    {
        include(__DIR__ . '/template/plain.php');
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }
}