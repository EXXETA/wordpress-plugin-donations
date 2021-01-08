<?php declare(strict_types=1);

namespace WWFDonationPlugin;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WWFDonationPlugin\Service\CharityCampaignManager;
use WWFDonationPlugin\Service\ProductService;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class WWFDonationPlugin extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function postInstall(InstallContext $context): void
    {
        $charityCampaignManager = new CharityCampaignManager();
        $taxRepository = $this->container->get('tax.repository');
        $productRepository = $this->container->get('product.repository');
        $productCategoryRepository = $this->container->get('product_category.repository');
        $manufacturerRepository = $this->container->get('product_manufacturer.repository');
        $salesChannelRepository = $this->container->get('sales_channel.repository');

        $productService = new ProductService($charityCampaignManager, $taxRepository,
            $productRepository, $productCategoryRepository, $manufacturerRepository, $salesChannelRepository);

        $productService->createProducts($context->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            // TODO do something different when the user want to keep the plugin's data

            // TODO delete wwf manufacturer ID
            // TODO delete zero rate tax? iff no entities associated?
            // TODO delete wwf products

            return;
        }
    }

    // TODO activation event: activate products!
    // TODO deactivation event: deactivate products!
}