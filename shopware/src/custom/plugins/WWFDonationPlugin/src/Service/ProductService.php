<?php
declare(strict_types=1);

namespace WWFDonationPlugin\Service;

use exxeta\wwf\banner\CharityProductManagerInterface;
use exxeta\wwf\banner\model\CharityCampaign;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * Class ProductService
 *
 * @package WWFDonationPlugin\Service
 */
class ProductService
{
    const MANUFACTURER_NAME_WWF_GERMANY = 'WWF Deutschland';
    const WWF_PRODUCT_NUMBER_PREFIX = 'WWF-DE-';
    const WWF_PRODUCT_DEFAULT_STOCK = 5000;

    /**
     * @var CharityProductManagerInterface
     */
    protected $campaignManager;

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
     * @param CharityProductManagerInterface $campaignManager
     * @param EntityRepository $taxRepository
     * @param EntityRepository $productRepository
     * @param EntityRepository $productCategoryRepository
     * @param EntityRepository $manufacturerRepository
     * @param EntityRepository $salesChannelRepository
     * @param MediaService $mediaService
     */
    public function __construct(CharityProductManagerInterface $campaignManager,
                                EntityRepository $taxRepository,
                                EntityRepository $productRepository,
                                EntityRepository $productCategoryRepository,
                                EntityRepository $manufacturerRepository,
                                EntityRepository $salesChannelRepository,
                                MediaService $mediaService)
    {
        $this->campaignManager = $campaignManager;
        $this->taxRepository = $taxRepository;
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->mediaService = $mediaService;
    }

    public function createProducts(Context $context): void
    {
        $charityCampaigns = $this->campaignManager->getAllCampaigns();
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
        // TODO add product images
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
                $mediaRecord = $this->mediaService->getMediaRecordBySlug($charityCampaign->getSlug());
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
                    'active' => true,
                    'shippingFree' => true,
                    'restockTime' => 1,
                    'media' => $mediaInfo,
                    'cover' => $mediaInfo[0],
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
                    'active' => true,
                    'shippingFree' => true,
                    'restockTime' => 1,
                    'minPurchase' => 1,
                    'maxPurchase' => static::WWF_PRODUCT_DEFAULT_STOCK,
                    'weight' => 0,
                    'height' => 0,
                    'length' => 0,
                    'media' => $mediaInfo,
                    'cover' => $mediaInfo[0],
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
}