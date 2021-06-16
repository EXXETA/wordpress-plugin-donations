<?php
declare(strict_types=1);

namespace WWFDonationPlugin\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\ReportResultModel;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Thumbnail\Manager;
use Shopware\Models\Article\Article;
use Shopware\Models\Article\Detail;
use Shopware\Models\Article\Image;
use Shopware\Models\Article\Price;
use Shopware\Models\Article\Supplier;
use Shopware\Models\Article\Unit;
use Shopware\Models\Customer\Group;
use Shopware\Models\Media\Media;
use Shopware\Models\Shop\Currency;
use Shopware\Models\Tax\Tax;
use Symfony\Component\VarDumper\VarDumper;
use WWFDonationPlugin\WWFDonationPluginException;

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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $taxRepository;

    /**
     * @var EntityRepository
     */
    protected $articleRepository;

    /**
     * @var EntityRepository
     */
    protected $articleDetailsRepository;

    /**
     * @var EntityRepository
     */
    protected $articlePriceRepository;

    /**
     * @var EntityRepository
     */
    protected $productCategoryRepository;

    /**
     * @var EntityRepository
     */
    protected $customerGroupRepository;

    /**
     * @var Price
     */
    protected $priceUnitRepository;

    /**
     * @var EntityRepository
     */
    protected $supplierRepository;

    /**
     * @var EntityRepository
     */
    protected $orderLineItemRepository;

    /**
     * @var EntityRepository
     */
    protected $currencyRepository;

    /**
     * @var EntityRepository
     */
    protected $articleImageRepository;

    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * ProductService constructor.
     *
     * @param EntityManager $entityManager
     * @param EntityRepository $taxRepository
     * @param EntityRepository $productRepository
     * @param EntityRepository $currencyRepository
     * @param EntityRepository $productCategoryRepository
     * @param EntityRepository $manufacturerRepository
     * @param EntityRepository $orderLineItemRepository
     * @param MediaService $mediaService
     */
    public function __construct(EntityManager $entityManager,
                                EntityRepository $taxRepository,
                                EntityRepository $productRepository,
                                EntityRepository $currencyRepository,
                                EntityRepository $productCategoryRepository,
                                EntityRepository $manufacturerRepository,
                                EntityRepository $orderLineItemRepository,
                                MediaService $mediaService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->articleDetailsRepository = $entityManager->getRepository(Detail::class);
        $this->articlePriceRepository = $entityManager->getRepository(Price::class);
        $this->customerGroupRepository = $entityManager->getRepository(Group::class);
        $this->priceUnitRepository = $entityManager->getRepository(Unit::class);
        $this->articleImageRepository = $entityManager->getRepository(Image::class);

        $this->taxRepository = $taxRepository;
        $this->articleRepository = $productRepository;
        $this->currencyRepository = $currencyRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->supplierRepository = $manufacturerRepository;
        $this->orderLineItemRepository = $orderLineItemRepository;
        $this->mediaService = $mediaService;
    }

    /**
     * @throws WWFDonationPluginException
     * @throws \Doctrine\ORM\ORMException
     */
    public function install(InstallContext $context): void
    {
        $euroCurrencyRecord = $this->currencyRepository->findOneBy(['currency' => 'EUR']);
        if (!$euroCurrencyRecord instanceof Currency) {
            throw new WWFDonationPluginException('No currency with key "EUR" available!');
        }

        $charityCampaigns = $this->getAllCampaigns();
        $productSupplierRecord = $this->getOrCreateProductSupplier();
        $taxRecord = $this->getOrCreateZeroTaxRateEntityId();

        $customerGroupRecord = $this->customerGroupRepository->findOneBy([
            'key' => 'EK',
        ]);
        if (!$customerGroupRecord instanceof Group) {
            throw new WWFDonationPluginException('Could not find customergroup with groupkey "EK"');
        }
        $priceUnitRecord = $this->priceUnitRepository->findOneBy([
            'unit' => 'Stck.'
        ]);
        if (!$priceUnitRecord instanceof Unit) {
            throw new WWFDonationPluginException('Could not find price unit record for unit key "Stck."');
        }
        $mediaAlbum = $this->mediaService->getOrCreateMediaAlbum();

        $mediaCollection = [];
        $productNumberCounter = 0;
        foreach ($charityCampaigns as $charityCampaign) {
            /* @var CharityCampaign $charityCampaign */
            $productNumber = self::WWF_PRODUCT_NUMBER_PREFIX . ++$productNumberCounter;

            $articleDetailsRecord = $this->articleDetailsRepository->findOneBy([
                'number' => $productNumber,
            ]);
            if (!$articleDetailsRecord instanceof Detail) {
                // create new article + details record
                $articleDetailsRecord = new Detail();
                $articleRecord = new Article();
                $articlePrice = new Price();
            } else {
                $articleRecord = $this->articleRepository->find($articleDetailsRecord->getArticleId());
                if (!$articleRecord instanceof Article) {
                    throw new WWFDonationPluginException(sprintf('Article with record "%d" is expected, but could not be retrieved.', $articleDetailsRecord->getId()));
                }
                $articlePrice = $this->articlePriceRepository->findOneBy([
                    'article' => $articleDetailsRecord->getArticleId(),
                ]);
                if (!$articlePrice instanceof Price) {
                    $articlePrice = new Price();
                }
            }

            // set article details information
            $articleDetailsRecord->setNumber($productNumber);
            $articleDetailsRecord->setActive(true);
            $articleDetailsRecord->setKind(1);
            $articleDetailsRecord->setMinPurchase(1);
            $articleDetailsRecord->setPurchasePrice(0);
            $articleDetailsRecord->setStockMin(0);
            $articleDetailsRecord->setWeight(0);
            $articleDetailsRecord->setPurchaseUnit(1);
            $articleDetailsRecord->setReferenceUnit(1);
            $articleDetailsRecord->setShippingFree(true);
            $articleDetailsRecord->setArticle($articleRecord);
            $articleDetailsRecord->setUnit($priceUnitRecord);

            // set article record information
            $articleRecord->setMainDetail($articleDetailsRecord);
            $articleRecord->setTax($taxRecord);
            $articleRecord->setSupplier($productSupplierRecord);
            $articleRecord->setDescription($charityCampaign->getDescription());
            $articleRecord->setDescriptionLong($charityCampaign->getDescription());
            $articleRecord->setActive(true);
            $articleRecord->setName('WWF-Spende: ' . $charityCampaign->getName());

            // set article price
            $articlePrice->setArticle($articleRecord);
            $articlePrice->setDetail($articleDetailsRecord);
            $articlePrice->setPrice(1);
            $articlePrice->setFrom(1);
            $articlePrice->setTo('beliebig');
            $articlePrice->setCustomerGroup($customerGroupRecord);

            $charityProduct = $this->getProductBySlug($charityCampaign->getSlug());
            $internalMediaPath = $this->mediaService->getInternalMediaPathByProduct($charityProduct);

            if ($articleRecord->getId() > 0) {
                $articleImagePreview = $this->articleImageRepository->findOneBy(['article' => $articleRecord->getId(), 'main' => 1]);
                if (!$articleImagePreview instanceof Image) {
                    $articleImagePreview = new Image();
                }
                $articleImageMain = $this->articleImageRepository->findOneBy(['article' => $articleRecord->getId(), 'main' => 2]);
                if (!$articleImageMain instanceof Image) {
                    $articleImageMain = new Image();
                }
            } else {
                // create new
                $articleImagePreview = new Image();
                $articleImageMain = new Image();
            }

            if ($articleRecord->getId() > 0) {
                // media entry should already exist
                $mediaRecord = $this->mediaService->getMediaRecordByCharityProduct($charityProduct, $mediaAlbum);
                if (!$mediaRecord) {
                    // fallback
                    $mediaRecord = $this->mediaService->importProductImage($internalMediaPath, $charityProduct->getImagePath(), $mediaAlbum);
                }
            } else {
                // media entry will be created
                $mediaRecord = $this->mediaService->importProductImage($internalMediaPath, $charityProduct->getImagePath(), $mediaAlbum);
            }
            $mediaCollection[] = $mediaRecord;

            if ($mediaRecord instanceof Media) {
                // product images
                $articleImagePreview->setMain(1);
                $articleImagePreview->setMedia($mediaRecord);
                $articleImagePreview->setArticle($articleRecord);
                $articleImagePreview->setPosition(1);
                $articleImagePreview->setPath($mediaRecord->getFileName());
                $articleImagePreview->setExtension($mediaRecord->getExtension());
                $articleImagePreview->setDescription($mediaRecord->getDescription());
                $articleImagePreview->setArticleDetail(null);

                $articleImageMain->setMain(2);
                $articleImageMain->setMedia($mediaRecord);
                $articleImageMain->setArticle($articleRecord);
                $articleImageMain->setPosition(1);
                $articleImageMain->setPath($mediaRecord->getFileName());
                $articleImageMain->setExtension($mediaRecord->getExtension());
                $articleImageMain->setDescription($mediaRecord->getDescription());
                $articleImageMain->setArticleDetail(null);

                $this->entityManager->persist($mediaRecord);
                $this->entityManager->persist($articleImagePreview);
                $this->entityManager->persist($articleImageMain);

                $articleRecord->getImages()->add($articleImagePreview);
                $articleRecord->getImages()->add($articleImageMain);
            } else {
//                $context = \Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();
                VarDumper::dump('no media record found!');
            }

//            $imageArticleMapping = new Image\Mapping();
//            $imageArticleMapping->setImage($articleImage);

            $this->entityManager->persist($articleRecord);
            $this->entityManager->persist($articleDetailsRecord);
            $this->entityManager->persist($articlePrice);
            $this->entityManager->persist($priceUnitRecord);

//            if ($isUpdate) {
//                // update
//                $data = [
//                    'stock' => static::WWF_PRODUCT_DEFAULT_STOCK,
//                    'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 1.00, 'net' => 1.00, 'linked' => false]],
//                    'restockTime' => 1,
//                    'media' => $mediaInfo,
//                    'cover' => $mediaInfo[0],
//                    'customFields' => [
//                        'wwf_campaign_slug' => $charityCampaign->getSlug()
//                    ]
//                ];
//                $this->productRepository->update([$data], $context);
//            } else {
//                // insert
//                $data = [
//                    'id' => $productId,
//                    'productNumber' => $productNumber,
//                    'visibilities' => $productVisibilities,
//                    'stock' => static::WWF_PRODUCT_DEFAULT_STOCK,
//                    'name' => 'WWF-Spende: ' . $charityCampaign->getName(),
//                    'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 1.00, 'net' => 1.00, 'linked' => false]],
//                    'restockTime' => 1,
//                    'maxPurchase' => static::WWF_PRODUCT_DEFAULT_STOCK,
//                    'weight' => 0,
//                    'height' => 0,
//                    'length' => 0,
//                    'media' => $mediaInfo,
//                    'cover' => $mediaInfo[0],
//                    'customFields' => [
//                        'wwf_campaign_slug' => $charityCampaign->getSlug()
//                    ]
//                ];
//                $this->productRepository->upsert([$data], $context);
//            }
        }
        $this->entityManager->flush();
        // create thumbnails
        $manager = Shopware()->Container()->get('thumbnail_manager');
        /* @var $manager Manager */
        foreach ($mediaCollection as $mediaRecord) {
            $manager->createMediaThumbnail($mediaRecord, [], true);
        }
    }

    /**
     * Method to get or create a WWF product manufacturer/supplier record and its id
     *
     * @return Supplier
     * @throws \Doctrine\ORM\ORMException
     */
    public function getOrCreateProductSupplier(): Supplier
    {
        $possiblyExistingSupplierRecord = $this->supplierRepository->findOneBy([
            'name' => self::MANUFACTURER_NAME_WWF_GERMANY,
        ]);
        if (!$possiblyExistingSupplierRecord instanceof Supplier) {
            // create
            $productManufacturer = new Supplier();
            $productManufacturer->setName(self::MANUFACTURER_NAME_WWF_GERMANY);
            $productManufacturer->setDescription(self::MANUFACTURER_NAME_WWF_GERMANY);
            $productManufacturer->setLink('https://www.wwf.de/');

            $this->entityManager->persist($productManufacturer);
            $this->entityManager->flush();
            return $productManufacturer;
        }
        return $possiblyExistingSupplierRecord;
    }

    /**
     * Method to get the id of a tax rate with zero percent. If no one exists, it will be created.
     *
     * @return Tax
     * @throws \Doctrine\ORM\ORMException
     */
    public function getOrCreateZeroTaxRateEntityId(): Tax
    {
        $possibleExistingTaxRecord = $this->taxRepository->findOneBy([
            'tax' => 0.0,
        ]);
        if (!$possibleExistingTaxRecord instanceof Tax) {
            $taxRecord = new Tax();
            $taxRecord->setName('0 % - Steuerfrei');
            $taxRecord->setTax(0.0);

            $this->entityManager->persist($taxRecord);
            $this->entityManager->flush();
            return $taxRecord;
        }
        return $possibleExistingTaxRecord;
    }

    /**
     * @param \exxeta\wwf\banner\model\CharityProduct $charityProduct
     * @param InstallContext $context
     * @return Article|null
     */
    public function getShopwareProductBySlug(string $charityProductSlug, InstallContext $context): ?ProductEntity
    {
//        $productCriteria = new Criteria();
//        $productCriteria->addFilter(new EqualsFilter('customFields.wwf_campaign_slug', $charityProductSlug));
//        $entitySearchResult = $this->productRepository->search($productCriteria, $context);
//        if ($entitySearchResult->getTotal() > 0) {
//            return $entitySearchResult->first();
//        }
        return null;
    }

    /**
     * Method to check if a given product entity is a WWF charity product
     *
     * @param Article|null $productEntity
     * @return bool
     */
    static function isWWFProduct(?Article $productEntity): bool
    {
        if (!$productEntity || !$productEntity instanceof Article) {
            return false;
        }
        if (0 === mb_stripos($productEntity->getProductNumber(), self::WWF_PRODUCT_NUMBER_PREFIX)) {
            return true;
        }
        return false;
    }

    public function getCharityProductEntities(): ?array
    {
        return null;
//        $criteria = new Criteria();
//        $productSlugs = $this->getAllCharityProductSlugs();
//        $criteria->addFilter(new EqualsAnyFilter('customFields.wwf_campaign_slug', $productSlugs));
//
//        $entitySearchResult = $this->productRepository->search($criteria, Context::createDefaultContext());
//        if ($entitySearchResult->getTotal() != count($productSlugs)) {
//            // TODO log this error!
//            return null;
//        }
//        return $entitySearchResult->getEntities();
    }

    /**
     * Central method to set status of this plugin's products
     *
     * @param InstallContext $deactivateContext
     * @param bool $activeStatus
     */
    public function setProductsActiveStatus(InstallContext $context, bool $activeStatus): void
    {
        $entityCollection = $this->getCharityProductEntities();
        if (!$entityCollection) {
            // TODO log this case!!
            return;
        }
        // disable products
        $updates = [];
//        foreach ($entityCollection->getIterator() as $entity) {
//            /* @var ProductEntity $entity */
//            $entity->setActive(false);
//            $updates[] = [
//                'id' => $entity->getId(),
//                'active' => $activeStatus
//            ];
//        }
//        $this->productRepository->update($updates, $context);
    }

    public function uninstall()
    {
        // TODO delete product media too!
//        if (!$this->getCharityProductEntities()) {
//            return;
//        }
//        $products = $this->getCharityProductEntities()->getIds();
//        if (!$products) {
//            // TODO log this case
//            return;
//        }
//        $ids = [];
//        foreach ($products as $singleProductId) {
//            $ids[] = ['id' => $singleProductId];
//        }
//        $this->productRepository->delete($ids, Context::createDefaultContext());
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
//        $reportResultModel = new ReportResultModel($startDate, $endDate);
//        // TODO do for all sales channels!
//
//        $salesChannelContext = Context::createDefaultContext();
//        
//        $productEntity = $this->getShopwareProductBySlug($campaignSlug, $salesChannelContext);
//        if (!$productEntity) {
//            throw new \Exception(sprintf('Could not find product entity for campaign slug "%s"', $campaignSlug));
//        }
//        $productId = $productEntity->getId();
//
//        $orderLineItemCriteria = new Criteria();
//        $orderLineItemCriteria->addFilter(new EqualsFilter('productId', $productId));
//        $orderLineItemCriteria->addAssociation('order');
//        // unfortunately atm there is not programmatic way to use a "between"-sql condition...
//        // therefore we need to check the orders to be in range of given start and end date
//        $orderLineItemCriteria->addFilter(new RangeFilter('order.orderDateTime', [
//            RangeFilter::GTE => $startDate->format(Defaults::STORAGE_DATE_TIME_FORMAT),
//        ]));
//
//        // some order/payment states are not considered during report, e.g. cancelled orders..
//        $excludedOrderStates = [
////            'failed',
//            'cancelled',
////            'refunded',
//            //'refunded_partially', // ? TODO
//            //'reminded', // ?
//            //'paid_partially', // ?
////            'chargeback'
//        ];
//
//        $sum = 0;
//        $orderIDs = [];
//        $entitySearchResult = $this->orderLineItemRepository->search($orderLineItemCriteria, $salesChannelContext);
//        if ($entitySearchResult->getTotal() > 0) {
//            foreach ($entitySearchResult->getEntities() as $singleOrderLineItem) {
//                /* @var $singleOrderLineItem OrderLineItemEntity */
//                $orderEntity = $singleOrderLineItem->getOrder();
//                if (!$orderEntity) {
//                    // this should never happen!
//                    // TODO log!
//                    continue;
//                }
//                // the db dal query criteria above ensures the iterated orders are >= startDate
//                if ($orderEntity->getOrderDateTime() <= $endDate) {
//                    // .. so we need to filter out the orders which are out of the range interval [on the right/end side]
//                    continue;
//                }
//                $stateMachineState = $orderEntity->getStateMachineState();
//                if (!$stateMachineState) {
//                    // TODO log error
//                    continue;
//                }
//                if (in_array($stateMachineState->getTechnicalName(), $excludedOrderStates)) {
//                    // skip excluded line items by current order transaction state
//                    continue;
//                }
//                $sum += $singleOrderLineItem->getTotalPrice();
//                $orderIDs[] = $singleOrderLineItem->getOrderId();
//            }
//        }
//        $orderIDs = array_unique($orderIDs);
//
//        $reportResultModel->setAmount($sum);
//        $reportResultModel->setOrderCountTotal(count($orderIDs));
//
//        return $reportResultModel;
        return new ReportResultModel(new DateTime(), new DateTime());
    }
}