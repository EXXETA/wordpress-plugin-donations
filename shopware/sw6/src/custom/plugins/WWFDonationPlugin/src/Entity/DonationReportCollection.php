<?php declare(strict_types=1);

namespace WWFDonationPlugin\Entity;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class DonationReportCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return DonationReportEntity::class;
    }
}