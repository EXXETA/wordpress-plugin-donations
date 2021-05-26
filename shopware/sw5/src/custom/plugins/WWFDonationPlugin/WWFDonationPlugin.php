<?php


namespace WWFDonationPlugin;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class WWFDonationPlugin extends Plugin
{
    public function build(ContainerBuilder $container)
    {
        $fancyVariable = 'some-fancy-variable';

        $container->setParameter('swag_slogan_of_the_day.fancy_variable', $fancyVariable);
        $container->addCompilerPass(new SloganCompilerPass());

        parent::build($container);
    }

    public function install(InstallContext $installContext)
    {

    }

    public function uninstall(UninstallContext $uninstallContext)
    {

    }

    public function activate(ActivateContext $activateContext)
    {
        // on plugin activation clear the cache
        $activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    public function deactivate(DeactivateContext $deactivateContext)
    {
        // on plugin deactivation clear the cache
        $deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }
}