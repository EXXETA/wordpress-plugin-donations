<?php declare(strict_types=1);

namespace WWFDonationPlugin;

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WWFDonationPlugin\Service\CharitySettingsManager;
use WWFDonationPlugin\Service\DonationPluginInstance;
use WWFDonationPlugin\Service\MediaService;
use WWFDonationPlugin\Service\ProductService;
use WWFDonationPlugin\Service\SimpleCharityProductManager;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
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
    public const CSRF_TOKEN_ID = 'wwf-banner';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function postInstall(InstallContext $context): void
    {
        parent::postInstall($context);

        $systemConfigService = $this->container->get(SystemConfigService::class);
        // we need to initialize and inject services here manually
        // handle media setup first
        $mediaService = $this->createMediaService();
        $mediaService->install();
        $productService = $this->createProductServiceInstance($mediaService);
        $productService->install($context->getContext());
        // NOTE: after this step all products of this plugin should be created and disabled
        // enabling/disabling is done in plugin de-/activation steps below

        CharitySettingsManager::setSystemConfigServiceStatic($systemConfigService);
        CharitySettingsManager::init();
    }

    public function activate(ActivateContext $activateContext): void
    {
        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        // set all products of this plugin ACTIVE
        $productService->setProductsActiveStatus($activateContext->getContext(), true);

        parent::activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        // set all products of this plugin INACTIVE
        $productService->setProductsActiveStatus($deactivateContext->getContext(), false);

        parent::deactivate($deactivateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            // TODO do something different when the user want to keep the plugin's data
            return;
        }
        $systemConfigService = $this->container->get(SystemConfigService::class);
        /* @var $systemConfigService SystemConfigService */
        $mediaService = $this->createMediaService();
        $productService = $this->createProductServiceInstance($mediaService);
        // TODO implement deletion of created media!
//        $mediaService->uninstall();
        $productService->uninstall();

        CharitySettingsManager::setSystemConfigServiceStatic($systemConfigService);
        CharitySettingsManager::uninstall();

        // TODO delete wwf manufacturer ID
        // TODO delete zero rate tax? iff no entities associated?
        // TODO delete wwf products
        // TODO delete all added media + top level media folder
    }

    // TODO activation event: activate products! already done?
    // TODO deactivation event: deactivate products! already done?
    /**
     * @param MediaService $mediaService
     * @return ProductService
     */
    private function createProductServiceInstance(MediaService $mediaService): ProductService
    {
        $taxRepository = $this->container->get('tax.repository');
        $productRepository = $this->container->get('product.repository');
        $productCategoryRepository = $this->container->get('product_category.repository');
        $manufacturerRepository = $this->container->get('product_manufacturer.repository');
        $salesChannelRepository = $this->container->get('sales_channel.repository');
        $orderLineItemRepository = $this->container->get('order_line_item.repository');

        // handle product generation second
        $productService = new ProductService(
            $taxRepository,
            $productRepository, $productCategoryRepository,
            $manufacturerRepository, $salesChannelRepository,
            $orderLineItemRepository, $mediaService
        );
        return $productService;
    }

    /**
     * @param DonationPluginInstance $donationPluginInstance
     * @return MediaService
     */
    private function createMediaService(): MediaService
    {
        // create needed services manually
        $mediaRepository = $this->container->get('media.repository');
        $mediaFolderRepository = $this->container->get('media_folder.repository');
        $productMediaRepository = $this->container->get('product_media.repository');
        $fileSaver = $this->container->get(FileSaver::class);

        $mediaService = new MediaService(
            new SimpleCharityProductManager(),
            $mediaRepository,
            $mediaFolderRepository,
            $productMediaRepository,
            $fileSaver
        );
        return $mediaService;
    }
}