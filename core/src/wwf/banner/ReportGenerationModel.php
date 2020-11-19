<?php


namespace exxeta\wwf\banner;

/**
 * Class ReportGenerationModel
 *
 * this object encapsulates values we need during donation report generation
 *
 * @package donations
 */
class ReportGenerationModel
{
    /**
     * @var \DateTime
     */
    private $startDate;
    /**
     * @var \DateTime
     */
    private $endDate;
    /**
     * @var string
     */
    private $intervalMode;
    /**
     * @var bool
     */
    private $isRegular = false;

    /**
     * @var bool
     */
    private $sendMail = true;

    /**
     * ReportGenerationModel constructor.
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $intervalMode
     * @param bool $isRegular
     * @param bool $sendMail
     */
    public function __construct(\DateTime $startDate, \DateTime $endDate, string $intervalMode,
                                bool $isRegular, bool $sendMail)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->intervalMode = $intervalMode;
        $this->isRegular = $isRegular;
        $this->sendMail = $sendMail;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getIntervalMode(): string
    {
        return $this->intervalMode;
    }

    /**
     * @return bool
     */
    public function isRegular(): bool
    {
        return $this->isRegular;
    }

    /**
     * @return bool
     */
    public function isSendMail(): bool
    {
        return $this->sendMail;
    }
}