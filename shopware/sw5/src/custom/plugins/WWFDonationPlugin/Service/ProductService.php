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
    public function __construct(EntityManager $entityManager, MediaService $mediaService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->articleDetailsRepository = $entityManager->getRepository(Detail::class);
        $this->articlePriceRepository = $entityManager->getRepository(Price::class);
        $this->customerGroupRepository = $entityManager->getRepository(Group::class);
        $this->priceUnitRepository = $entityManager->getRepository(Unit::class);
        $this->articleImageRepository = $entityManager->getRepository(Image::class);
        $this->taxRepository = $entityManager->getRepository(Tax::class);
        $this->articleRepository = $entityManager->getRepository(Article::class);
        $this->currencyRepository = $entityManager->getRepository(Currency::class);;
        $this->supplierRepository = $entityManager->getRepository(Supplier::class);

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
            $articleDetailsRecord->setAdditionalText($charityCampaign->getSlug());
            $articleDetailsRecord->setActive(true);
            $articleDetailsRecord->setKind(1);
            $articleDetailsRecord->setInStock(static::WWF_PRODUCT_DEFAULT_STOCK);
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

            if ($mediaRecord instanceof Media) {
                $mediaCollection[] = $mediaRecord;
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
                throw new WWFDonationPluginException('Problem handling media record of wwf products!');
            }

            $this->entityManager->persist($articleRecord);
            $this->entityManager->persist($articleDetailsRecord);
            $this->entityManager->persist($articlePrice);
            $this->entityManager->persist($priceUnitRecord);
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
     * @param string $charityProductSlug
     * @return Article|null
     */
    public function getShopwareProductBySlug(string $charityProductSlug): ?Article
    {
        $articleDetailsRecord = $this->articleDetailsRepository->findOneBy(['additionalText' => $charityProductSlug]);
        if ($articleDetailsRecord instanceof Detail) {
            $article = $articleDetailsRecord->getArticle();
            return $article;
        }
        return null;
    }

    /**
     * Method to check if a given product entity is a WWF charity product
     *
     * @param Article|null $articleEntity
     * @return bool
     */
    static function isWWFProduct(?Article $articleEntity): bool
    {
        if (!$articleEntity || !$articleEntity instanceof Article) {
            return false;
        }
        $articleDetailRecord = $articleEntity->getMainDetail();
        $simpleProductManager = new SimpleCharityProductManager();

        if ($articleDetailRecord instanceof Detail) {
            if (0 === mb_stripos($articleDetailRecord->getNumber(), self::WWF_PRODUCT_NUMBER_PREFIX)
                && in_array($articleDetailRecord->getAdditionalText(), $simpleProductManager->getAllCharityProductSlugs())) {
                return true;
            }
        } else {
            throw new WWFDonationPluginException('Could not find article detail record of shopware product');
        }

        return false;
    }

    public function getCharityProductEntities(): array
    {
        $wwfProducts = [];
        foreach ($this->getAllCharityProductSlugs() as $singleSlug) {
            $articleRecord = $this->getShopwareProductBySlug($singleSlug);
            if ($articleRecord instanceof Article) {
                $wwfProducts[] = $articleRecord;
            }
        }
        return $wwfProducts;
    }

    /**
     * Central method to set status of this plugin's products
     *
     * @param InstallContext $deactivateContext
     * @param bool $activeStatus
     */
    public function setProductsActiveStatus(bool $activeStatus): void
    {
        $entityCollection = $this->getCharityProductEntities();
        if (!$entityCollection || count($entityCollection) === 0) {
            // TODO log this case!!
            return;
        }
        foreach ($entityCollection as $entity) {
            /* @var Article $entity */
            $articleDetailsRecord = $entity->getMainDetail();
            if (!$articleDetailsRecord instanceof Detail) {
                throw new WWFDonationPluginException('could not find article details record');
            }
            $articleDetailsRecord->setActive($activeStatus);
            $this->entityManager->persist($articleDetailsRecord);
        }
        $this->entityManager->flush();
    }

    public function uninstall()
    {
        // TODO delete product media too!
        if (!$this->getCharityProductEntities()) {
            return;
        }
        $this->setProductsActiveStatus(false);
    }

    public function getCharityProductCategory()
    {
        // this method is not used in shopware 5 context!
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