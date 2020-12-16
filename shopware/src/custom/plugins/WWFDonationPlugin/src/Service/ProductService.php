<?php
declare(strict_types=1);

namespace WWFDonationPlugin\Service;

use exxeta\wwf\banner\model\CharityCampaign;
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
    /**
     * @var CharityCampaignManager
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
     * ProductService constructor.
     *
     * @param CharityCampaignManager $campaignManager
     * @param EntityRepository $taxRepository
     * @param EntityRepository $productRepository
     * @param EntityRepository $productCategoryRepository
     * @param EntityRepository $manufacturerRepository
     */
    public function __construct(CharityCampaignManager $campaignManager,
                                EntityRepository $taxRepository,
                                EntityRepository $productRepository,
                                EntityRepository $productCategoryRepository,
                                EntityRepository $manufacturerRepository)
    {
        $this->campaignManager = $campaignManager;
        $this->taxRepository = $taxRepository;
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    public function createProducts(Context $context): void
    {
        $charityCampaigns = $this->campaignManager->getAllCampaigns();

        foreach ($charityCampaigns as $charityCampaign) {
            /* @var CharityCampaign $charityCampaign */
            $productId = Uuid::randomHex();
            $productNumber = Uuid::randomHex();

            $taxId = $this->getOrCreateZeroTaxRateEntityId($context);

            $data = [
                'id' => $productId,
                'productNumber' => $productNumber,
                'stock' => 1,
                'name' => $charityCampaign->getName(),
                'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 1.00, 'net' => 1.00, 'linked' => false]],
//            'manufacturer' => ['name' => 'create'],
                'taxId' => $taxId,
                'active' => true,
            ];

            $this->productRepository->create([$data], $context);
        }
    }

    public function getOrCreateProductManufacturer(Context $context): string
    {
        $getProductManufacturer = function () use (&$context): ?string {
            $criteria = (new Criteria())->addFilter(new EqualsFilter(""));
            return $this->manufacturerRepository->searchIds($criteria, $context)->firstId();
        };
        $productManufacturer = $getProductManufacturer();
        if (empty($productManufacturer)) {
            $data = [

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
}