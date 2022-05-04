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

namespace donations;

use exxeta\wwf\banner\ReportHandler;

class WooReportHandler implements ReportHandler
{
    public function getMailSubjectSuffix(): string
    {
        return get_bloginfo('name');
    }

    public function storeReportRecord(array $templateVars, string $mailBody): void
    {
        wp_insert_post([
            'post_title' => $templateVars['subject'],
            'post_content' => $mailBody,
            'post_type' => Plugin::$customPostType,
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ]);
    }

    public function sendMail(string $recipient, string $subject, string $body, string $bodyPlain, array $headers): void
    {
        wp_mail($recipient, esc_html($subject), $body, $headers);
    }

    public function getShopName(): string
    {
        return get_bloginfo('name');
    }

    public function getShopUrl(): string
    {
        return wp_guess_url();
    }

    public function getShopSystem(): string
    {
        return 'WooCommerce';
    }
}