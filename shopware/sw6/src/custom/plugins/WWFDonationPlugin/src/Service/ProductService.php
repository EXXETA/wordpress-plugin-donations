<?php
declare(strict_types=1);

namespace WWFDonationPlugin\Service;

use DateTime;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\ReportResultModel;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * Class ProductService
 *
 * @package WWFDonationPlugin\Service
 */
class ProductService extends AbstractCharityProductManager
{
    const MANUFACTURER_NAME_WWF_GERMANY = 'WWF Deutschland';
    const WWF_PRODUCT_NUMBER_PREFIX = 'WWF-DE-';
    const WWF_PRODUCT_DEFAULT_STOCK = 5000;

    /**
     * @var EntityRepositoryInterface
     */
    protected $taxRepository;

    /**
     * @var EntityRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    protected $productCategoryRepository;

    /**
     * @var EntityRepositoryInterface
     */
    protected $manufacturerRepository;

    /**
     * @var EntityRepositoryInterface
     */
    protected $salesChannelRepository;

    /**
     * @var EntityRepositoryInterface
     */
    protected $orderLineItemRepository;

    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * ProductService constructor.
     *
     * @param EntityRepositoryInterface $taxRepository
     * @param EntityRepositoryInterface $productRepository
     * @param EntityRepositoryInterface $productCategoryRepository
     * @param EntityRepositoryInterface $manufacturerRepository
     * @param EntityRepositoryInterface $salesChannelRepository
     * @param EntityRepositoryInterface $orderLineItemRepository
     * @param MediaService $mediaService
     */
    public function __construct(EntityRepositoryInterface $taxRepository,
                                EntityRepositoryInterface $productRepository,
                                EntityRepositoryInterface $productCategoryRepository,
                                EntityRepositoryInterface $manufacturerRepository,
                                EntityRepositoryInterface $salesChannelRepository,
                                EntityRepositoryInterface $orderLineItemRepository,
                                MediaService $mediaService)
    {
        parent::__construct();
        $this->taxRepository = $taxRepository;
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->orderLineItemRepository = $orderLineItemRepository;
        $this->mediaService = $mediaService;
    }

    public function install(Context $context): void
    {
        $charityCampaigns = $this->getAllCampaigns();
        $productManufacturerId = $this->getOrCreateProductManufacturerId($context);
        $taxId = $this->getOrCreateZeroTaxRateEntityId($context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('maintenance', false));

        // enable products for all sales channels
        $salesChannelSearch = $this->salesChannelRepository->search(new Criteria(), $context);
        $salesChannelIds = [Defaults::SALES_CHANNEL];
        if ($salesChannelSearch->getTotal() > 0) {
            $salesChannelIds = $salesChannelSearch->getIds();
        }
        $productVisibilities = [];
        foreach ($salesChannelIds as $salesChannelId) {
            $productVisibilities[] = ['salesChannelId' => $salesChannelId, 'visibility' => ProductVisibilityDefinition::VISIBILITY_ALL];
        }

        // TODO add category
        // TODO add parent + children products?

        $productNumberCounter = 0;
        foreach ($charityCampaigns as $charityCampaign) {
            /* @var CharityCampaign $charityCampaign */

            $productNumber = self::WWF_PRODUCT_NUMBER_PREFIX . ++$productNumberCounter;

            $productCriteria = (new Criteria())->addFilter(new EqualsFilter('productNumber', $productNumber));
            $productCriteria->addAssociation('media');

            $potentiallyExistingProduct = $this->productRepository->search($productCriteria, $context);
            $isUpdate = $potentiallyExistingProduct->getTotal() > 0;
            if ($isUpdate) {
                $first = $potentiallyExistingProduct->first();
                /* @var ProductEntity $first */
                $productId = $first->getId();
                // FIXME safe access - consider null values!
                $mediaRecord = $first->getMedia()->getMedia()->first();
                $mediaId = $mediaRecord->getId();
                $productMediaEntity = $this->mediaService->getProductMediaRecord($mediaId, $productId);
                if (!$productMediaEntity) {
                    // FIXME error log
                    continue;
                }
            } else {
                // insert
                $productId = Uuid::randomHex();
                $mediaId = Uuid::randomHex();
                $mediaRecord = $this->mediaService->getProductMediaRecordBySlug($charityCampaign->getSlug());
                if (!$mediaRecord) {
                    // FIXME handle this case!
                }
            }

            $mediaInfo = [['id' => $mediaId, 'mediaId' => $mediaRecord->getId(), 'productId' => $productId]];

            if ($isUpdate) {
                // update
                $data = [
                    'id' => $productId,
                    'description' => $charityCampaign->getFullText(),
                    'stock' => static::WWF_PRODUCT_DEFAULT_STOCK,
                    'name' => 'WWF-Spende: ' . $charityCampaign->getName(),
                    'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 1.00, 'net' => 1.00, 'linked' => false]],
                    'active' => false,
                    'shippingFree' => true,
                    'restockTime' => 1,
                    'media' => $mediaInfo,
                    'cover' => $mediaInfo[0],
                    'customFields' => [
                        'wwf_campaign_slug' => $charityCampaign->getSlug()
                    ]
                ];
                $this->productRepository->update([$data], $context);
            } else {
                // insert
                $data = [
                    'id' => $productId,
                    'productNumber' => $productNumber,
                    'description' => $charityCampaign->getDescription(),
                    'visibilities' => $productVisibilities,
                    'stock' => static::WWF_PRODUCT_DEFAULT_STOCK,
                    'name' => 'WWF-Spende: ' . $charityCampaign->getName(),
                    'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 1.00, 'net' => 1.00, 'linked' => false]],
                    'manufacturerId' => $productManufacturerId,
                    'taxId' => $taxId,
                    'active' => false,
                    'shippingFree' => true,
                    'restockTime' => 1,
                    'minPurchase' => 1,
                    'maxPurchase' => static::WWF_PRODUCT_DEFAULT_STOCK,
                    'weight' => 0,
                    'height' => 0,
                    'length' => 0,
                    'media' => $mediaInfo,
                    'cover' => $mediaInfo[0],
                    'customFields' => [
                        'wwf_campaign_slug' => $charityCampaign->getSlug()
                    ]
                ];
                $this->productRepository->upsert([$data], $context);
            }
        }
    }

    /**
     * Method to get or create a WWF product manufacturer record and its id
     *
     * @param Context $context
     * @return string
     */
    public function getOrCreateProductManufacturerId(Context $context): string
    {
        $getProductManufacturer = function () use (&$context): ?string {
            $criteria = (new Criteria())->addFilter(new EqualsFilter('name', self::MANUFACTURER_NAME_WWF_GERMANY));
            return $this->manufacturerRepository->searchIds($criteria, $context)->firstId();
        };
        $productManufacturer = $getProductManufacturer();
        if (empty($productManufacturer)) {
            $data = [
                'name' => self::MANUFACTURER_NAME_WWF_GERMANY,
                'link' => 'https://www.wwf.de/',
                'description' => self::MANUFACTURER_NAME_WWF_GERMANY,
            ];
            $this->manufacturerRepository->create([$data], $context);
            $productManufacturer = $getProductManufacturer();
        }
        return $productManufacturer;
    }

    /**
     * Method to get the id of a tax rate with zero percent. If no one exists, it will be created.
     *
     * @param Context $context
     * @return string
     */
    public function getOrCreateZeroTaxRateEntityId(Context $context): string
    {
        $getTaxRecords = function () use (&$context): ?string {
            $criteria = (new Criteria())->addFilter(new EqualsFilter("taxRate", 0.0))->setLimit(1);
            return $this->taxRepository->searchIds($criteria, $context)->firstId();
        };
        $taxEntity = $getTaxRecords();
        if (empty($taxEntity)) {
            // create one
            $taxData = [
                'name' => 'Keine Steuer (0 %)',
                'taxRate' => 0.0,
            ];
            $this->taxRepository->create([$taxData], $context);
            $taxEntity = $getTaxRecords();
        }
        return $taxEntity;
    }

    /**
     * @param \exxeta\wwf\banner\model\CharityProduct $charityProduct
     * @param Context $context
     * @return ProductEntity|null
     */
    public function getShopwareProductBySlug(string $charityProductSlug, Context $context): ?ProductEntity
    {
        $productCriteria = new Criteria();
        $productCriteria->addFilter(new EqualsFilter('customFields.wwf_campaign_slug', $charityProductSlug));
        $entitySearchResult = $this->productRepository->search($productCriteria, $context);
        if ($entitySearchResult->getTotal() > 0) {
            return $entitySearchResult->first();
        }
        return null;
    }

    /**
     * Method to check if a given product entity is a WWF charity product
     *
     * @param ProductEntity|null $productEntity
     * @return bool
     */
    static function isWWFProduct(?ProductEntity $productEntity): bool
    {
        if (!$productEntity || !$productEntity instanceof ProductEntity) {
            return false;
        }
        if (0 === mb_stripos($productEntity->getProductNumber(), self::WWF_PRODUCT_NUMBER_PREFIX)) {
            return true;
        }
        return false;
    }

    public function getCharityProductEntities(): ?EntityCollection
    {
        $criteria = new Criteria();
        $productSlugs = $this->getAllCharityProductSlugs();
        $criteria->addFilter(new EqualsAnyFilter('customFields.wwf_campaign_slug', $productSlugs));

        $entitySearchResult = $this->productRepository->search($criteria, Context::createDefaultContext());
        if ($entitySearchResult->getTotal() != count($productSlugs)) {
            // TODO log this error!
            return null;
        }
        return $entitySearchResult->getEntities();
    }

    /**
     * Central method to set status of this plugin's products
     *
     * @param Context $deactivateContext
     * @param bool $activeStatus
     */
    public function setProductsActiveStatus(Context $context, bool $activeStatus): void
    {
        $entityCollection = $this->getCharityProductEntities();
        if (!$entityCollection) {
            // TODO log this case!!
            return;
        }
        // disable products
        $updates = [];
        foreach ($entityCollection->getIterator() as $entity) {
            /* @var ProductEntity $entity */
            $entity->setActive(false);
            $updates[] = [
                'id' => $entity->getId(),
                'active' => $activeStatus
            ];
        }
        $this->productRepository->update($updates, $context);
    }

    public function uninstall()
    {
        // TODO delete product media too!
        if (!$this->getCharityProductEntities()) {
            return;
        }
        $products = $this->getCharityProductEntities()->getIds();
        if (!$products) {
            // TODO log this case
            return;
        }
        $ids = [];
        foreach ($products as $singleProductId) {
            $ids[] = ['id' => $singleProductId];
        }
        $this->productRepository->delete($ids, Context::createDefaultContext());
    }

    public function getCharityProductCategory()
    {
        // this method is not used in shopware 6 context!
    }

    /**
     * @param string $campaignSlug
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportResultModel
     * @throws \Exception
     */
    public function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel
    {
        $reportResultModel = new ReportResultModel($startDate, $endDate);
        // TODO do for all sales channels!

        $salesChannelContext = Context::createDefaultContext();


        $productEntity = $this->getShopwareProductBySlug($campaignSlug, $salesChannelContext);
        if (!$productEntity) {
            throw new \Exception(sprintf('Could not find product entity for campaign slug "%s"', $campaignSlug));
        }
        $productId = $productEntity->getId();

        $orderLineItemCriteria = new Criteria();
        $orderLineItemCriteria->addFilter(new EqualsFilter('productId', $productId));
        $orderLineItemCriteria->addAssociation('order');
        // unfortunately atm there is not programmatic way to use a "between"-sql condition...
        // therefore we need to check the orders to be in range of given start and end date
        $orderLineItemCriteria->addFilter(new RangeFilter('order.orderDateTime', [
            RangeFilter::GTE => $startDate->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]));

        // some order/payment states are not considered during report, e.g. cancelled orders..
        $excludedOrderStates = [
//            'failed',
            'cancelled',
//            'refunded',
            //'refunded_partially', // ? TODO
            //'reminded', // ?
            //'paid_partially', // ?
//            'chargeback'
        ];

        $sum = 0;
        $orderIDs = [];
        $entitySearchResult = $this->orderLineItemRepository->search($orderLineItemCriteria, $salesChannelContext);
        if ($entitySearchResult->getTotal() > 0) {
            foreach ($entitySearchResult->getEntities() as $singleOrderLineItem) {
                /* @var $singleOrderLineItem OrderLineItemEntity */
                $orderEntity = $singleOrderLineItem->getOrder();
                if (!$orderEntity) {
                    // this should never happen!
                    // TODO log!
                    continue;
                }
                // the db dal query criteria above ensures the iterated orders are >= startDate
                if ($orderEntity->getOrderDateTime() <= $endDate) {
                    // .. so we need to filter out the orders which are out of the range interval [on the right/end side]
                    continue;
                }
                $stateMachineState = $orderEntity->getStateMachineState();
                if (!$stateMachineState) {
                    // TODO log error
                    continue;
                }
                if (in_array($stateMachineState->getTechnicalName(), $excludedOrderStates)) {
                    // skip excluded line items by current order transaction state
                    continue;
                }
                $sum += $singleOrderLineItem->getTotalPrice();
                $orderIDs[] = $singleOrderLineItem->getOrderId();
            }
        }
        $orderIDs = array_unique($orderIDs);

        $reportResultModel->setAmount($sum);
        $reportResultModel->setOrderCountTotal(count($orderIDs));

        return $reportResultModel;
    }
}