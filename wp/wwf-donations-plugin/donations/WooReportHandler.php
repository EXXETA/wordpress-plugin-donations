<?php

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

    public function sendMail(string $recipient, string $subject, string $body, array $headers): void
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