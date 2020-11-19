<?php

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
     * @param array $headers
     */
    public function sendMail(string $recipient, string $subject, string $body, array $headers): void;

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