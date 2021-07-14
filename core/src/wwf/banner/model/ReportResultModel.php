<?php
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