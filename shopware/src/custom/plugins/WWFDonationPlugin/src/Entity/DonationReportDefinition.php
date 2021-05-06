<?php declare(strict_types=1);


namespace WWFDonationPlugin\Entity;


use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class DonationReportDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'wwf_donation_report';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return DonationReportEntity::class;
    }

    public function getCollectionClass(): string
    {
        return DonationReportCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new IntField('order_count', 'orderCount')),
            (new StringField('interval_mode', 'intervalMode'))->addFlags(new Required()),
            (new BoolField('is_regular', 'isRegular')),
            (new FloatField('total_amount', 'totalAmount'))->addFlags(new Required(), new WriteProtected()),
            (new JsonField('campaign_details', 'campaignDetails'))->addFlags(new Required()),
            (new LongTextField('mail_content', 'mailContent'))->addFlags(new AllowHtml()),
            (new DateTimeField('start_date', 'startDate'))->addFlags(new Required(), new WriteProtected()),
            (new DateTimeField('end_date', 'endDate'))->addFlags(new Required(), new WriteProtected()),
        ]);
    }
}