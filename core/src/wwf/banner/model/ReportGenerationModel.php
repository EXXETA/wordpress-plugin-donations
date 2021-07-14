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
 * Class ReportGenerationModel
 *
 * this object encapsulates values we need during donation report generation
 *
 * @package exxeta\wwf\banner\model
 */
class ReportGenerationModel
{
    /**
     * @var DateTime
     */
    private $startDate;
    /**
     * @var DateTime
     */
    private $endDate;
    /**
     * @var string
     */
    private $intervalMode;
    /**
     * @var bool
     */
    private $isRegular;

    /**
     * @var bool
     */
    private $sendMail;

    /**
     * ReportGenerationModel constructor.
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $intervalMode
     * @param bool $isRegular
     * @param bool $sendMail
     */
    public function __construct(DateTime $startDate, DateTime $endDate, string $intervalMode,
                                bool $isRegular, bool $sendMail)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->intervalMode = $intervalMode;
        $this->isRegular = $isRegular;
        $this->sendMail = $sendMail;
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