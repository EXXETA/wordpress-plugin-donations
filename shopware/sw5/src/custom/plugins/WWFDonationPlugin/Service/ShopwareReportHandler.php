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


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportHandler;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use WWFDonationPlugin\Models\DonationReportEntity;

/**
 * Class ShopwareReportHandler
 * @package WWFDonationPlugin\Service
 */
class ShopwareReportHandler implements ReportHandler
{
    /**
     * @var ModelManager
     */
    private $entityManager;

    /**
     * ShopwareReportHandler constructor.
     * @param ModelManager $entityManager
     */
    public function __construct(ModelManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getMailSubjectSuffix(): string
    {
        return $this->getShopName();
    }

    public function storeReportRecord(array $templateVars, string $mailBody): void
    {
        $donationPlugin = $templateVars['pluginInstance'];
        /* @var $donationPlugin DonationPluginInterface */

        $donationReportEntity = new DonationReportEntity();
        $donationReportEntity->setName($templateVars['subject']);
        $donationReportEntity->setOrderCounter($templateVars['totalOrderCount']);
        $donationReportEntity->setIntervalMode($donationPlugin->getSettingsManagerInstance()->getCurrentReportingInterval());
        $donationReportEntity->setIsRegular($templateVars['isRegular']);
        $donationReportEntity->setTotalAmount($templateVars['sum']);
        $donationReportEntity->setCampaignDetails(json_encode($templateVars['revenues']));
        $donationReportEntity->setMailContent($mailBody);
        $donationReportEntity->setStartDate($templateVars['startDate']);
        $donationReportEntity->setEndDate($templateVars['endDate']);

        $this->entityManager->persist($donationReportEntity);
        $this->entityManager->flush();
    }

    /**
     * @param string $recipient
     * @param string $subject
     * @param string $body
     * @param string $bodyPlain
     * @param array $headers
     */
    public function sendMail(string $recipient, string $subject, string $body, string $bodyPlain, array $headers): void
    {
        $mail = \Shopware()->TemplateMail()->createMail(MailingService::WWF_REPORT_MAIL_KEY, ['parameters' => []]);
        /* @var $mail \Enlight_Components_Mail */

        $mail->clearRecipients();
        $mail->addTo($recipient, 'E-Shop Spenden WWF');

        $mail->clearBody();
        $mail->setBodyHtml($body);
        $mail->setBodyText($bodyPlain);

        $mail->clearSubject();
        $mail->setSubject($subject);

        try {
            $mail->send();
        } catch (\Exception $ex) {
            echo $ex->getMessage() . "\n";
        }
    }

    public function getShopName(): string
    {
        $shopRepository = $this->entityManager->getRepository(Shop::class);
        $defaultShopRecord = $shopRepository->findOneBy([
            'default' => 1,
            'active' => 1,
        ]);
        if (!$defaultShopRecord) {
            return 'Unknown';
        }
        /* @var $defaultShopRecord Shop */
        return $defaultShopRecord->getName();
    }

    /**
     * method to retrieve all known shop urls
     *
     * @return string
     */
    public function getShopUrl(): string
    {
        $shopRepository = $this->entityManager->getRepository(Shop::class);
        $defaultShopRecord = $shopRepository->findOneBy([
            'default' => 1,
            'active' => 1,
        ]);
        if (!$defaultShopRecord) {
            return '';
        }
        /* @var $defaultShopRecord Shop */
        $urlSchema = 'http://';
        if ($defaultShopRecord->getSecure()) {
            $urlSchema = 'https://';
        }
        return sprintf('%s%s%s', $urlSchema, $defaultShopRecord->getHost(), $defaultShopRecord->getBaseUrl());
    }

    public function getShopSystem(): string
    {
        return 'Shopware 5.4+';
    }
}