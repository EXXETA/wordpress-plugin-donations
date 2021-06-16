<?php


namespace WWFDonationPlugin;

use Doctrine\ORM\EntityManager;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Models\Article\Article;
use Shopware\Models\Article\Supplier;
use Shopware\Models\Category\Category;
use Shopware\Models\Order\Detail;
use Shopware\Models\Shop\Currency;
use Shopware\Models\Tax\Tax;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WWFDonationPlugin\Service\MediaService;
use WWFDonationPlugin\Service\ProductService;
use WWFDonationPlugin\Service\SimpleCharityProductManager;


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
    }

    public function install(InstallContext $context): void
    {
        parent::install($context);
        // we need to initialize and inject services here manually
        // handle media setup first
        $mediaService = $this->createMediaService();
        $mediaService->install();
        $productService = $this->createProductServiceInstance($mediaService);
        $productService->install($context);
        // NOTE: after this step all products of this plugin should be created and disabled
        // enabling/disabling is done in plugin de-/activation steps below
    }

    public function activate(ActivateContext $activateContext): void
    {
//        $configWriter = $this->container->get('shopware.plugin.config_writer');
//        /* @var ConfigWriter $configWriter */
//        CharitySettingsManager::setConfigWriterStatic($configWriter);
//        CharitySettingsManager::init();
//
//        $mediaService = $this->createMediaService();
//        $productService = $this->createProductServiceInstance($mediaService);
//        // set all products of this plugin ACTIVE
//        $productService->setProductsActiveStatus($activateContext, true);
//
//        $activateContext->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
//
//        parent::activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
//        $mediaService = $this->createMediaService();
//        $productService = $this->createProductServiceInstance($mediaService);
//        // set all products of this plugin INACTIVE
//        $productService->setProductsActiveStatus($deactivateContext->getContext(), false);
//
//        parent::deactivate($deactivateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
//        if ($uninstallContext->keepUserData()) {
//            // TODO do something different when the user want to keep the plugin's data
//            return;
//        }
//        $configWriter = $this->container->get('shopware.plugin.config_writer');
//        /* @var $configWriter Plugin\ConfigWriter */
//        $mediaService = $this->createMediaService();
//        $productService = $this->createProductServiceInstance($mediaService);
//        $productService->uninstall();
//
//        CharitySettingsManager::setConfigWriterStatic($configWriter);
//        CharitySettingsManager::uninstall();
//
//        if ($uninstallContext->getPlugin()->getActive()) {
//            $uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
//        }
//        parent::uninstall($uninstallContext);
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

        $taxRepository = $entityManager->getRepository(Tax::class);
        $currencyRepository = $entityManager->getRepository(Currency::class);
        $productRepository = $entityManager->getRepository(Article::class);
        $productCategoryRepository = $entityManager->getRepository(Category::class);
        $manufacturerRepository = $entityManager->getRepository(Supplier::class);
        $orderDetailsRepository = $entityManager->getRepository(Detail::class);

        // handle product generation second
        $productService = new ProductService(
            $entityManager,
            $taxRepository, $productRepository,
            $currencyRepository,
            $productCategoryRepository,
            $manufacturerRepository,
            $orderDetailsRepository, $mediaService
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
}