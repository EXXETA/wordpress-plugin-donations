<?php

namespace WWFDonationPlugin\Smarty;

use Symfony\Component\DependencyInjection\ContainerBuilder;


class SmartyBannerPluginCompilerPass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $template = $container->getDefinition('template');
        $template->addMethodCall('addPluginsDir', [__DIR__ . '/plugins']);
    }
}