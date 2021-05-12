<?php
declare(strict_types=1);

namespace WWFDonationPlugin\Service;

use DateTime;
use exxeta\wwf\banner\AbstractCharityProductManager;
use exxeta\wwf\banner\model\CharityCampaign;
use exxeta\wwf\banner\model\ReportResultModel;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\VarDumper\VarDumper;

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
     * @var EntityRepository
     */
    protected $salesChannelRepository;

    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * ProductService constructor.
     *
     * @param EntityRepository $taxRepository
     * @param EntityRepository $productRepository
     * @param EntityRepository $productCategoryRepository
     * @param EntityRepository $manufacturerRepository
     * @param EntityRepository $salesChannelRepository
     * @param MediaService $mediaService
     */
    public function __construct(EntityRepository $taxRepository,
                                EntityRepository $productRepository,
                                EntityRepository $productCategoryRepository,
                                EntityRepository $manufacturerRepository,
                                EntityRepository $salesChannelRepository,
                                MediaService $mediaService)
    {
        $this->taxRepository = $taxRepository;
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->salesChannelRepository = $salesChannelRepository;
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
                    'description' => $charityCampaign->getDescription(),
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

    public function getRevenueOfCampaignInTimeRange(string $campaignSlug, DateTime $startDate, DateTime $endDate): ReportResultModel
    {
        $reportResultModel = new ReportResultModel($startDate, $endDate);
        // TODO do for all sales channels!

        $productEntity = $this->getShopwareProductBySlug($campaignSlug, Context::createDefaultContext());
        if (!$productEntity) {
            throw new \Exception(sprintf('Could not find product entity for campaign slug "%s"', $campaignSlug));
        }
        VarDumper::dump($productEntity);

        return $reportResultModel;
    }
}