<?php


namespace WWFDonationPlugin\Service;


use exxeta\wwf\banner\DonationPluginInterface;
use exxeta\wwf\banner\ReportHandler;
use Shopware\Core\Content\Mail\Service\MailService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ShopwareReportHandler
 * @package WWFDonationPlugin\Service
 */
class ShopwareReportHandler implements ReportHandler
{
    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private $donationReportRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    /**
     * ShopwareReportHandler constructor.
     * @param SystemConfigService $systemConfigService
     * @param EntityRepositoryInterface $donationReportRepository
     * @param EntityRepositoryInterface $salesChannelRepository
     * @param MailService $mailService
     */
    public function __construct(SystemConfigService $systemConfigService,
                                EntityRepositoryInterface $donationReportRepository,
                                EntityRepositoryInterface $salesChannelRepository,
                                MailService $mailService)
    {
        $this->systemConfigService = $systemConfigService;
        $this->donationReportRepository = $donationReportRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->mailService = $mailService;
    }

    public function getMailSubjectSuffix(): string
    {
        return $this->getShopName();
    }

    public function storeReportRecord(array $templateVars, string $mailBody): void
    {
        $donationPlugin = $templateVars['pluginInstance'];
        /* @var $donationPlugin DonationPluginInterface */

        $this->donationReportRepository->create([[
            'id' => Uuid::randomHex(),
            'name' => $templateVars['subject'],
            'orderCount' => $templateVars['totalOrderCount'],
            'intervalMode' => $donationPlugin->getSettingsManagerInstance()->getCurrentReportingInterval(),
            'isRegular' => $templateVars['isRegular'],
            'totalAmount' => $templateVars['sum'],
            'campaignDetails' => $templateVars['revenues'],
            'mailContent' => $mailBody,
            'startDate' => $templateVars['startDate'],
            'endDate' => $templateVars['endDate'],
        ]], Context::createDefaultContext());
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
        $mailParams = new ParameterBag();
        $mailParams->set('recipients', [
            $recipient => 'E-Shop Spenden WWF',
        ]);
        $mailParams->set('senderName', sprintf('%s Spendenplugin', $this->getShopName()));
        $mailParams->set('contentHtml', $body);
        $mailParams->set('contentPlain', $bodyPlain);
        $mailParams->set('subject', $subject);
        $mailParams->set('salesChannelId', Defaults::SALES_CHANNEL);

        $this->mailService->send($mailParams->all(), Context::createDefaultContext());
    }

    public function getShopName(): string
    {
        return $this->systemConfigService->get('core.basicInformation.shopName');
    }

    /**
     * method to retrieve all known shop urls
     *
     * @return string
     */
    public function getShopUrl(): string
    {
        $urls = [];
        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $salesChannelIds = $this->salesChannelRepository->search($criteria, Context::createDefaultContext());
        foreach ($salesChannelIds->getEntities()->getElements() as $salesChannelId => $salesChannel) {
            /* @var $salesChannel SalesChannelEntity */
            foreach ($salesChannel->getDomains()->getElements() as $domainEntity) {
                $urls[] = $domainEntity->getUrl();
            }
        }
        return implode('; ', $urls);
    }

    public function getShopSystem(): string
    {
        return 'Shopware 6.x';
    }
}