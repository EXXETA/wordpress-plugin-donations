<?php declare(strict_types=1);

namespace WWFDonationPlugin;

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WWFDonationPlugin\Service\DonationPluginInstance;
use WWFDonationPlugin\Service\MediaService;
use WWFDonationPlugin\Service\ProductService;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class WWFDonationPlugin extends Plugin
{
    public const CSRF_TOKEN_ID = 'wwf-banner';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function postInstall(InstallContext $context): void
    {
        $donationPluginInstance = new DonationPluginInstance();
        // create needed services manually
        $taxRepository = $this->container->get('tax.repository');
        $productRepository = $this->container->get('product.repository');
        $productCategoryRepository = $this->container->get('product_category.repository');
        $manufacturerRepository = $this->container->get('product_manufacturer.repository');
        $salesChannelRepository = $this->container->get('sales_channel.repository');
        $mediaRepository = $this->container->get('media.repository');
        $mediaFolderRepository = $this->container->get('media_folder.repository');
        $productMediaRepository = $this->container->get('product_media.repository');
        $fileSaver = $this->container->get(FileSaver::class);

        // we need to initialize and inject services here manually
        // handle media setup first
        $mediaService = new MediaService(
            $donationPluginInstance,
            $mediaRepository,
            $mediaFolderRepository,
            $productMediaRepository,
            $fileSaver
        );
        $mediaService->install();

        // handle product generation second
        $productService = new ProductService(
            $donationPluginInstance->getCharityProductManagerInstance(), $taxRepository,
            $productRepository, $productCategoryRepository,
            $manufacturerRepository, $salesChannelRepository,
            $mediaService
        );

        $productService->createProducts($context->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            // TODO do something different when the user want to keep the plugin's data

            return;
        }

        // TODO delete wwf manufacturer ID
        // TODO delete zero rate tax? iff no entities associated?
        // TODO delete wwf products
        // TODO delete all added media + top level media folder
    }

    // TODO activation event: activate products!
    // TODO deactivation event: deactivate products!
}