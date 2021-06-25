<?php


namespace WWFDonationPlugin;

use Doctrine\ORM\EntityManager;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Article\Detail;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WWFDonationPlugin\Bootstrap\Database;
use WWFDonationPlugin\Service\CharitySettingsManager;
use WWFDonationPlugin\Service\MailingService;
use WWFDonationPlugin\Service\MediaService;
use WWFDonationPlugin\Service\ProductService;
use WWFDonationPlugin\Service\ScheduledTask\ReportTaskHandler;
use WWFDonationPlugin\Service\SimpleCharityProductManager;
use WWFDonationPlugin\Service\SystemConfigService;
use WWFDonationPlugin\Smarty\SmartyBannerPluginCompilerPass;


if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Class WWFDonationPlugin
 *
 * Note: you do not have the DI container of this plugin during plugin lifecycle hooks,
 * required services need to be created manually.
 *
 * @package WWFDonationPlugin
 */
class WWFDonationPlugin extends Plugin
{
    public const PLUGIN_NAME = 'WWFDonationPlugin';
    public const CSRF_TOKEN_ID = 'wwf-banner';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SmartyBannerPluginCompilerPass());
    }

    public function install(InstallContext $context): void
    {
        parent::install($context);
        // we need to initialize and inject services here manually
        // handle media setup first
        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        $productService->install($context);
        // NOTE: after this step all products of this plugin should be created and disabled
        // enabling/disabling is done in plugin de-/activation steps below

        $database = new Database($this->container->get('models'));
        $database->install();

        $mailingService = $this->createMailingService();
        $mailingService->install();
    }

    public function activate(ActivateContext $activateContext): void
    {
        CharitySettingsManager::setSystemConfigServiceStatic($this->createSystemConfigService());
        CharitySettingsManager::init();

        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        // set all products of this plugin ACTIVE
        $productService->setProductsActiveStatus(true);

        $activateContext->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);

        parent::activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        // set all products of this plugin INACTIVE
        $productService->setProductsActiveStatus(false);

        parent::deactivate($deactivateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        $database = new Database(
            $this->container->get('models')
        );

        if ($uninstallContext->keepUserData()) {
            // TODO do something different when the user want to keep the plugin's data
            return;
        }

        $database->uninstall();

        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        $productService->uninstall();

        $systemConfigService = $this->createSystemConfigService();
        CharitySettingsManager::setSystemConfigServiceStatic($systemConfigService);
        CharitySettingsManager::uninstall();

        if ($uninstallContext->getPlugin()->getActive()) {
            $uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
        }

        $mailingService = $this->createMailingService();
        $mailingService->uninstall();

        parent::uninstall($uninstallContext);
    }

    // TODO activation event: activate products! already done?
    // TODO deactivation event: deactivate products! already done?
    /**
     * @param MediaService $mediaService
     * @return ProductService
     * @throws WWFDonationPluginException
     */
    private function createProductServiceInstance(MediaService $mediaService): ProductService
    {
        $entityManager = $this->container->get('models');
        if (!$entityManager instanceof EntityManager) {
            throw new WWFDonationPluginException('Could not retrieve EntityManager!');
        }
        // handle product generation second
        $productService = new ProductService(
            $entityManager, $mediaService
        );
        return $productService;
    }

    /**
     * @return MediaService
     * @throws WWFDonationPluginException
     */
    private function createMediaService(): MediaService
    {
        $entityManager = $this->container->get('models');
        if (!$entityManager instanceof ModelManager) {
            throw new WWFDonationPluginException('Could not retrieve an instance of EntityManager');
        }
        $mediaService = $this->container->get('shopware_media.media_service');
        if (!$mediaService instanceof \Shopware\Bundle\MediaBundle\MediaService) {
            throw new WWFDonationPluginException('Invalid media service received!');
        }

        return new MediaService(
            new SimpleCharityProductManager(),
            $entityManager,
            $mediaService
        );
    }

    private function createSystemConfigService(): SystemConfigService
    {
        $configReader = $this->container->get('shopware.plugin.config_reader');
        /* @var $configReader Plugin\ConfigReader */
        $configWriter = $this->container->get('shopware.plugin.config_writer');
        /* @var $configWriter Plugin\ConfigWriter */
        return new SystemConfigService($configReader, $configWriter);
    }

    private function createMailingService(): MailingService
    {
        $entityManager = $this->container->get('models');
        /* @var $entityManager EntityManager */
        return new MailingService($entityManager);
    }

    public static function getSubscribedEvents()
    {
        return [
            'product_stock_was_changed' => 'onStockUpdate',
            'WWFDonationPlugin_CronJob_WWFDonationReportCheck' => 'onDonationReportCheck',
        ];
    }

    /**
     * This event is fired right after an order was placed and the sw5 stock update logic took place.
     * We use this to
     *
     * @param \Enlight_Event_EventArgs $args
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onStockUpdate(\Enlight_Event_EventArgs $args): void
    {
        $entityManager = $this->container->get('models');
        if (!$entityManager instanceof EntityManager) {
            return;
        }
        $orderNumber = $args->get('number');
        if (empty($orderNumber)) {
            return;
        }
        $articleDetailRecord = $entityManager
            ->getRepository(\Shopware\Models\Article\Detail::class)
            ->findOneBy(['number' => $orderNumber]);
        /* @var Detail $articleDetailRecord */
        $articleRecord = $articleDetailRecord->getArticle();

        if (ProductService::isWWFProduct($articleRecord) && $articleDetailRecord instanceof Detail) {
            $articleDetailRecord->setInStock(ProductService::WWF_PRODUCT_DEFAULT_STOCK);

            $entityManager->persist($articleDetailRecord);
            $entityManager->flush();
        }
    }

    public function onDonationReportCheck(\Shopware_Components_Cron_CronJob $cronJobArgs): void
    {
        $reportTaskHandler = $this->container->get(ReportTaskHandler::class);
        if (!$reportTaskHandler instanceof ReportTaskHandler) {
            throw new WWFDonationPluginException('Could not get service instance of ReportTaskHandler');
        }
        $reportTaskHandler->run();
    }
}