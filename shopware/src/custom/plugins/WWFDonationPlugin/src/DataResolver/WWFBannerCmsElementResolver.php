<?php declare(strict_types=1);


namespace WWFDonationPlugin\DataResolver;


use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;

class WWFBannerCmsElementResolver extends \Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'wwf-banner';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
    }
}