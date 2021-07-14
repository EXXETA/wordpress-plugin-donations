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

namespace WWFDonationPlugin\Subscriber\Storefront;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WWFDonationPlugin\Service\SimpleCharityProductManager;

class Subscriber implements EventSubscriberInterface
{

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Subscriber constructor.
     * @param SystemConfigService $systemConfigService
     * @param LoggerInterface $logger
     */
    public function __construct(SystemConfigService $systemConfigService, LoggerInterface $logger)
    {
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
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
            $this->logger->warning('Invalid plugin configuration detected. Expected configuration array.');
            return;
        }

        $isCartIntegrationActive = $this->getConfigurationValueBool($config, 'isCartIntegrationActive');
        $isCartMiniBannerEnabled = $this->getConfigurationValueBool($config, 'wwfDonationsMiniBannerShowMiniCart');
        $cartCampaignKey = $this->getConfigurationValueStr($config, 'wwfDonationsMiniBannerCampaign');
        if (!$cartCampaignKey) {
            $this->logger->warning('Invalid empty wwf banner campaign selected in plugin configuration. Fallback to protect_species_coin.');
            $cartCampaignKey = SimpleCharityProductManager::$PROTECT_SPECIES_COIN; // default fallback value
        }
        $cartMiniBannerPageTargetEntity = $this->getConfigurationValueStr($config, 'wwfDonationsMiniBannerCampaignTargetPage');
        if (!$cartMiniBannerPageTargetEntity) {
            $cartMiniBannerPageTargetEntity = null;
        }

        // validation/hint step
        if ($isCartIntegrationActive && $isCartMiniBannerEnabled && empty($cartMiniBannerPageTargetEntity)) {
            $this->logger->warning('No target category(=page) entity configured for mini banner. This leads to an empty link of the mini banner. Please setup the plugin correctly.');
        }

        // pass config data to the templates
        $data = [
            'isCartIntegrationActive' => $isCartIntegrationActive,
            'wwfDonationsMiniBannerShowMiniCart' => $isCartMiniBannerEnabled,
            'wwfDonationsMiniBannerCampaignTargetPage' => $cartMiniBannerPageTargetEntity,
            'wwfDonationsMiniBannerCampaign' => $cartCampaignKey,
            'wwfDonationsMiniBannerOffcanvasShowMiniCart' => $this->getConfigurationValueBool($config, 'wwfDonationsMiniBannerOffcanvasShowMiniCart'),
            'wwfDonationsMiniBannerOffcanvasCampaign' => $this->getConfigurationValueStr($config, 'wwfDonationsMiniBannerOffcanvasCampaign'),
            'wwfDonationsMiniBannerOffcanvasCampaignTargetPage' => $this->getConfigurationValueStr($config, 'wwfDonationsMiniBannerOffcanvasCampaignTargetPage'),
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
