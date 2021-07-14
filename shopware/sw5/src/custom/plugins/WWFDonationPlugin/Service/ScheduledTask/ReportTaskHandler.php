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


namespace WWFDonationPlugin\Service\ScheduledTask;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportGenerator;
use Shopware\Components\Logger;
use WWFDonationPlugin\Service\ShopwareReportHandler;

/**
 * Class ReportTaskHandler
 *
 * Shopware task handler of a daily check for report generation.
 *
 * @package WWFDonationPlugin\Service\ScheduledTask
 */
class ReportTaskHandler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var DonationPluginInterface
     */
    private $donationPluginInstance;

    /**
     * @var ShopwareReportHandler
     */
    private $shopwareReportHandler;

    /**
     * ReportTaskHandler constructor.
     *
     * @param Logger $logger
     * @param DonationPluginInterface $donationPluginInstance
     * @param ShopwareReportHandler $shopwareReportHandler
     */
    public function __construct(Logger $logger,
                                DonationPluginInterface $donationPluginInstance,
                                ShopwareReportHandler $shopwareReportHandler)
    {
        $this->logger = $logger;
        $this->donationPluginInstance = $donationPluginInstance;
        $this->shopwareReportHandler = $shopwareReportHandler;
    }

    public function run(): void
    {
        try {
            ReportGenerator::checkReportGeneration($this->donationPluginInstance, $this->shopwareReportHandler);
            $this->donationPluginInstance->getSettingsManagerInstance()->setReportLastCheck();
        } catch (\Exception $ex) {
            echo $ex->getMessage() . '\n';
            echo $ex->getFile() . ':' . $ex->getLine() . '\n';
            echo $ex->getTraceAsString() . '\n';
            $this->logger->addError('Error encountered during check for report generation in ReportTaskHandler');
            return;
        }
    }
}