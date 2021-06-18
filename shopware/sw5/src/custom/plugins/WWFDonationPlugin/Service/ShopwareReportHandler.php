<?php


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportHandler;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use WWFDonationPlugin\Entity\DonationReportEntity;

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