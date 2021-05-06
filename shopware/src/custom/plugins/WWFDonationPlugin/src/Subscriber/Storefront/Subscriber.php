<?php

namespace WWFDonationPlugin\Subscriber\Storefront;

use Monolog\Logger;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WWFDonationPlugin\Service\CharityCampaignManager;

class Subscriber implements EventSubscriberInterface
{

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Subscriber constructor.
     * @param SystemConfigService $systemConfigService
     * @param Logger $logger
     */
    public function __construct(SystemConfigService $systemConfigService, Logger $logger)
    {
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            StorefrontRenderEvent::class => 'onStorefrontRender',
        ];
    }

    /**
     * @param StorefrontRenderEvent $event
     */
    public function onStorefrontRender(StorefrontRenderEvent $event)
    {
        $config = $this->systemConfigService->get('WWFDonationPlugin.config');
        if (!is_array($config)) {
            $this->logger->addWarning('Invalid plugin configuration detected. Expected configuration array.');
            return;
        }

        $isCartIntegrationActive = $this->getConfigurationValueBool($config, 'isCartIntegrationActive');
        $isCartMiniBannerEnabled = $this->getConfigurationValueBool($config, 'isCartMiniBannerEnabled');
        $cartCampaignKey = $this->getConfigurationValueStr($config, 'cartBannerCampaignKey');
        if (!$cartCampaignKey) {
            $this->logger->addWarning('Invalid empty wwf banner campaign selected in plugin configuration. Fallback to protect_species_coin.');
            $cartCampaignKey = CharityCampaignManager::$PROTECT_SPECIES_COIN; // default fallback value
        }
        $cartMiniBannerPageTargetEntity = $this->getConfigurationValueStr($config, 'miniBannerTargetPageEntity');
        if (!$cartMiniBannerPageTargetEntity) {
            $cartMiniBannerPageTargetEntity = null;
        }

        // validation/hint step
        if ($isCartIntegrationActive && $isCartMiniBannerEnabled && empty($cartMiniBannerPageTargetEntity)) {
            $this->logger->addWarning('No target category(=page) entity configured for mini banner. This leads to an empty link of the mini banner. Please setup the plugin correctly.');
        }

        // pass config data to the templates
        $data = [
            'isCartIntegrationActive' => $isCartIntegrationActive,
            'isCartMiniBannerEnabled' => $isCartMiniBannerEnabled,
            'miniBannerTargetPageEntity' => $cartMiniBannerPageTargetEntity,
            'cartBannerCampaignKey' => $cartCampaignKey,
        ];
        $event->setParameter('wwf_donation_plugin', $data);
    }

    /**
     * @param array $config
     * @param string $configKey
     * @return bool
     */
    private function getConfigurationValueBool(array $config, string $configKey): bool
    {
        if (!isset($config[$configKey])) {
            return false;
        }
        return boolval($config[$configKey]);
    }

    /**
     * @param array $config
     * @param string $configKey
     * @return ?string
     */
    private function getConfigurationValueStr(array $config, string $configKey): ?string
    {
        if (!isset($config[$configKey])) {
            return null;
        }
        return $config[$configKey] ?? null;
    }
}
