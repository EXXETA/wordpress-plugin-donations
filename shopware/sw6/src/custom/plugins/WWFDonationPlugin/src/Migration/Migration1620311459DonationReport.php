<?php declare(strict_types=1);


namespace WWFDonationPlugin\Migration;


use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * Class Migration1620311459DonationReport
 *
 * Creating a new table to store donation report records
 *
 * @package WWFDonationPlugin\Migration
 */
class Migration1620311459DonationReport extends MigrationStep
{

    public function getCreationTimestamp(): int
    {
        return 1620311459;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `wwf_donation_report` (
    `id` BINARY(16) NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `order_count` INT(11) NOT NULL,
    `interval_mode` VARCHAR(16) NOT NULL,
    `is_regular` TINYINT(1) NOT NULL DEFAULT true,
    `total_amount` DOUBLE NOT NULL,
    `campaign_details` JSON NOT NULL,
    `mail_content` MEDIUMTEXT COLLATE utf8mb4_unicode_ci,
    `start_date` DATETIME(3) NOT NULL,
    `end_date` DATETIME(3) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->exec($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // Note: We do not delete the db table here to prevent data loss issues
    }
}