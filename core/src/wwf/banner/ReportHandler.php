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

namespace exxeta\wwf\banner;

/**
 * Interface ReportHandler
 *
 * interface to work with the general ReportGenerator to do shop- or plugin-specific stuff
 *
 * @package exxeta\wwf\banner
 */
interface ReportHandler
{
    /**
     * this is appended to the mail subject of a mail report
     *
     * @return string
     */
    public function getMailSubjectSuffix(): string;

    /**
     * hook-method to do the shop-specific implementation of the storage process of a report record
     *
     * @param array $templateVars
     * @param string $mailBody
     */
    public function storeReportRecord(array $templateVars, string $mailBody): void;

    /**
     * hook-method to do the shop-specific implementation of the mailing process
     *
     * @param string $recipient
     * @param string $subject
     * @param string $body
     * @param string $bodyPlain no markup
     * @param array $headers
     */
    public function sendMail(string $recipient, string $subject, string $body, string $bodyPlain, array $headers): void;

    /**
     * method to get the shop name
     *
     * @return string
     */
    public function getShopName(): string;

    /**
     * method to get the url to the concrete shop
     *
     * @return string
     */
    public function getShopUrl(): string;

    /**
     * method to get the name of the current shop system.
     * This is used in report record and mail only.
     *
     * @return string
     */
    public function getShopSystem(): string;
}