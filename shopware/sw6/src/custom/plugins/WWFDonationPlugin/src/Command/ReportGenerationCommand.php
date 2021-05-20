<?php declare(strict_types=1);


namespace WWFDonationPlugin\Command;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\model\ReportGenerationModel;
use exxeta\wwf\banner\ReportGenerator;
use exxeta\wwf\banner\SettingsManagerInterface;
use Symfony\Component\Console\Command\Command;
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
        // hide/disable in prod environments
        $this->setHidden(!$this->isEnabled());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // weekly and now
        $reportModel = ReportGenerator::getReportModel(SettingsManagerInterface::REPORT_INTERVAL_MODE_WEEKLY, new \DateTime());
        // pass these values to a new instance
        $newReportModel = new ReportGenerationModel($reportModel->getStartDate(), $reportModel->getEndDate(), $reportModel->getIntervalMode(), false, true);

        ReportGenerator::generateReport($newReportModel, $this->pluginInstance, $this->reportHandler);

        $reportCounter = $this->pluginInstance->getSettingsManagerInstance()->getReportCounter();
        $output->writeln(sprintf('Generated donation report #%d', $reportCounter));

        return 0;
    }

    public function isEnabled()
    {
        return boolval(getenv('APP_ENV') == 'dev');
    }
}