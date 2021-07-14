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


namespace WWFDonationPlugin\Commands;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\model\ReportGenerationModel;
use exxeta\wwf\banner\ReportGenerator;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WWFDonationPlugin\Service\ShopwareReportHandler;

/**
 * Class ReportGenerationCommand
 * This command is hidden in non-dev environments.
 *
 * Use this for debugging purposes only!
 *
 * @package WWFDonationPlugin\Command
 */
class ReportGenerationCommand extends ShopwareCommand
{
    /**
     * @var DonationPluginInterface
     */
    private $pluginInstance;

    /**
     * @var ShopwareReportHandler
     */
    private $reportHandler;

    /**
     * ReportGenerationCommand constructor.
     * @param DonationPluginInterface $pluginInstance
     * @param ShopwareReportHandler $reportHandler
     */
    public function __construct(DonationPluginInterface $pluginInstance, ShopwareReportHandler $reportHandler)
    {
        parent::__construct();
        $this->pluginInstance = $pluginInstance;
        $this->reportHandler = $reportHandler;
    }

    protected function configure(): void
    {
        $this->setName('wwf:report-generate')
            ->setDescription('Manual trigger to start report generation process for ordered WWF products. For debugging only!');
        // hide/disable in prod environments
        $this->setHidden(!$this->isEnabled());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (Shopware()->Environment() !== 'dev') {
            $output->writeln('Environment "dev" is required to run this debug command.');
            return 1;
        }

        // weekly and now
        $reportModel = ReportGenerator::getReportModel($this->pluginInstance->getSettingsManagerInstance()->getCurrentReportingInterval(), new \DateTime());
        // pass these values to a new instance
        $newReportModel = new ReportGenerationModel($reportModel->getStartDate(), $reportModel->getEndDate(), $reportModel->getIntervalMode(), false, true);

        ReportGenerator::generateReport($newReportModel, $this->pluginInstance, $this->reportHandler);

        $reportCounter = $this->pluginInstance->getSettingsManagerInstance()->getReportCounter();
        $output->writeln(sprintf('Generated donation report #%d', $reportCounter));

        return 0;
    }

    public function isEnabled(): bool
    {
        return Shopware()->Environment() === 'dev';
    }
}