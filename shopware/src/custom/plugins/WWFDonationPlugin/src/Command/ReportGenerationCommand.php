<?php declare(strict_types=1);


namespace WWFDonationPlugin\Command;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportGenerator;
use exxeta\wwf\banner\SettingsManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WWFDonationPlugin\Service\ShopwareReportHandler;

/**
 * Class ReportGenerationCommand
 *
 * @package WWFDonationPlugin\Command
 */
class ReportGenerationCommand extends Command
{
    protected static $defaultName = 'wwf:report-generate';

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
        $this->setDescription('Manual trigger to start report generation process for ordered WWF products. For debugging only!');
        // hide in prod environments
        $isDev = boolval(getenv('APP_ENV') == 'dev');
        $this->setHidden($isDev);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // weekly and now
        $reportModel = ReportGenerator::getReportModel(SettingsManagerInterface::REPORT_INTERVAL_MODE_WEEKLY, new \DateTime());
        ReportGenerator::generateReport($reportModel, $this->pluginInstance, $this->reportHandler);

        return 0;
    }
}