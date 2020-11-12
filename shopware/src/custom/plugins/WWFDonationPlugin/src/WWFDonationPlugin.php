<?php declare(strict_types=1);

namespace WWFDonationPlugin;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class WWFDonationPlugin extends Plugin
{

    public function install(InstallContext $context): void
    {
        // TODO add installation routine
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