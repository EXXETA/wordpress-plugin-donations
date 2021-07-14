<?php declare(strict_types=1);
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */


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