<?php declare(strict_types=1);


namespace WWFDonationPlugin\Service\ScheduledTask;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportGenerator;
use Monolog\Logger;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use WWFDonationPlugin\Service\ShopwareReportHandler;

/**
 * Class ReportTaskHandler
 *
 * Shopware task handler of a daily check for report generation.
 *
 * @package WWFDonationPlugin\Service\ScheduledTask
 */
class ReportTaskHandler extends ScheduledTaskHandler
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
     * @param EntityRepository $scheduledTaskRepository
     * @param Logger $logger
     * @param DonationPluginInterface $donationPluginInstance
     * @param ShopwareReportHandler $shopwareReportHandler
     */
    public function __construct(EntityRepository $scheduledTaskRepository,
                                Logger $logger,
                                DonationPluginInterface $donationPluginInstance,
                                ShopwareReportHandler $shopwareReportHandler)
    {
        parent::__construct($scheduledTaskRepository);

        $this->logger = $logger;
        $this->donationPluginInstance = $donationPluginInstance;
        $this->shopwareReportHandler = $shopwareReportHandler;
    }

    public static function getHandledMessages(): iterable
    {
        return [
            ReportTask::class,
        ];
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