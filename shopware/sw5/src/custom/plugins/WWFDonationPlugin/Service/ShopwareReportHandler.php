<?php


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportHandler;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use Symfony\Component\VarDumper\VarDumper;
use WWFDonationPlugin\Entity\DonationReportEntity;

/**
 * Class ShopwareReportHandler
 * @package WWFDonationPlugin\Service
 */
class ShopwareReportHandler implements ReportHandler
{
    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var ModelManager
     */
    private $entityManager;

    /**
     * ShopwareReportHandler constructor.
     * @param SystemConfigService $systemConfigService
     * @param ModelManager $entityManager
     */
    public function __construct(SystemConfigService $systemConfigService,
                                ModelManager $entityManager)
    {
        $this->systemConfigService = $systemConfigService;
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
        // TODO
        $mail = Shopware()->Container()->get('mail');
        /* @var $mail \Enlight_Components_Mail */

        $mail->setFromToDefaultFrom();
        $mail->addTo($recipient, 'E-Shop Spenden WWF');

        $mail->setBodyHtml($body);
        $mail->setBodyText($bodyPlain);
        $mail->setSubject($subject);
        VarDumper::dump($mail->getRecipients());

        $mail->send();
//        $this->mailService->send($mailParams->all(), Context::createDefaultContext());
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
        return sprintf('%s%s%s', $urlSchema, $defaultShopRecord->getHost(), $defaultShopRecord->getBasePath());
    }

    public function getShopSystem(): string
    {
        return 'Shopware 5.4+';
    }
}