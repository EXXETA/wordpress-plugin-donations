<?php

namespace exxeta\wwf\banner\model;

use DateTime;

/**
 * Class ReportResultModel
 *
 * Plain old PHP object of the report results
 *
 * @package exxeta\wwf\banner\model
 */
class ReportResultModel
{
    /**
     * @var int
     */
    private $orderCountTotal;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $endDate;

    /**
     * ReportResultModel constructor.
     * @param DateTime $startDate
     * @param DateTime $endDate
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->orderCountTotal = 0;
        $this->amount = 0;
    }

    /**
     * @param int $orderCountTotal
     */
    public function setOrderCountTotal(int $orderCountTotal): void
    {
        $this->orderCountTotal = $orderCountTotal;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getOrderCountTotal(): int
    {
        return $this->orderCountTotal;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }
}