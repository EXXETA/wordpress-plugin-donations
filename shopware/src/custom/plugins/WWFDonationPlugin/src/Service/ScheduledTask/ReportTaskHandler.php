<?php declare(strict_types=1);


namespace WWFDonationPlugin\Service\ScheduledTask;


use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ReportTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepository
     */
    private $donationReportRepository;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * ReportTaskHandler constructor.
     *
     * @param EntityRepository $scheduledTaskRepository
     * @param EntityRepository $donationReportRepository
     */
    public function __construct(EntityRepository $scheduledTaskRepository,
                                EntityRepository $donationReportRepository,
                                SystemConfigService $systemConfigService)
    {
        parent::__construct($scheduledTaskRepository);

        $this->donationReportRepository = $donationReportRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getHandledMessages(): iterable
    {
        return [
            ReportTask::class,
        ];
    }

    public function run(): void
    {
        // TODO do report task implementation
    }
}