<?php declare(strict_types=1);

namespace WWFDonationPlugin;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use WWFDonationPlugin\Service\CharityCampaignManager;
use WWFDonationPlugin\Service\ProductService;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class WWFDonationPlugin extends Plugin
{

    public function postInstall(InstallContext $context): void
    {
        $charityCampaignManager = new CharityCampaignManager();
        $taxRepository = $this->container->get('tax.repository');
        $productRepository = $this->container->get('product.repository');
        $productCategoryRepository = $this->container->get('product_category.repository');
        $manufacturerRepository = $this->container->get('product_manufacturer.repository');
        $productService = new ProductService($charityCampaignManager, $taxRepository,
            $productRepository, $productCategoryRepository, $manufacturerRepository);

        $productService->createProducts($context->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            // TODO do something different when the user want to keep the plugin's data
            return;
        }
    }
}