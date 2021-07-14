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


namespace WWFDonationPlugin\Subscriber;


use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Content\Product\DataAbstractionLayer\StockUpdater;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\StateMachine\Event\StateMachineTransitionEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use WWFDonationPlugin\Service\ProductService;

/**
 * This replaces the original stockupdater mechanisms of sw 6 and filters out wwf donation products
 *
 * Class WWFProductStockUpdater
 * @package WWFDonationPlugin\Subscriber
 */
class WWFProductStockUpdater extends StockUpdater
{
    /**
     * @var Connection
     */
    protected $dbConnection;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EntityRepository
     */
    protected $productRepository;

    /**
     * WWFProductStockUpdater constructor.
     *
     * @param Connection $connection
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     * @param Logger $logger
     * @param EntityRepository $productRepository
     */
    public function __construct(
        Connection $connection,
        EventDispatcherInterface $dispatcher,
        Logger $logger,
        EntityRepository $productRepository
    )
    {
        parent::__construct($connection, $dispatcher);
        $this->dbConnection = $connection;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     * simple delegation
     *
     * @param StateMachineTransitionEvent $event
     */
    public function stateChanged(StateMachineTransitionEvent $event): void
    {
        parent::stateChanged($event);

        if ($event->getContext()->getVersionId() !== Defaults::LIVE_VERSION) {
            return;
        }

        if ($event->getEntityName() !== 'order') {
            return;
        }

        $productInfo = $this->getProductsOfOrder($event->getEntityId());
        $productIds = array_column($productInfo, 'referenced_id');
        $affectedProducts = $this->productRepository
            ->search(new Criteria($productIds), $event->getContext())
            ->getIterator();

        $updateQuery = new RetryableQuery(
            $this->dbConnection->prepare('UPDATE product SET available_stock = :stock, stock = :stock WHERE id = :id')
        );

        // identify changes to reset the stock of the wwf products
        foreach ($affectedProducts as $singleProduct) {
            /* @var ProductEntity $singleProduct */
            if (ProductService::isWWFProduct($singleProduct)
                && ($singleProduct->getStock() != ProductService::WWF_PRODUCT_DEFAULT_STOCK
                    || $singleProduct->getAvailableStock() != ProductService::WWF_PRODUCT_DEFAULT_STOCK)) {
                $this->logger->debug('this is a wwf product! re-adjust stock! ' . $singleProduct->getId());
                $updateQuery->execute([
                    'id' => Uuid::fromHexToBytes((string)$singleProduct->getId()),
                    'stock' => ProductService::WWF_PRODUCT_DEFAULT_STOCK,
                ]);
            }
        }
    }

    /**
     * strip wwf products from stock management
     *
     * @param array $ids of products
     * @param Context $context
     */
    public function update(array $ids, Context $context): void
    {
        $products = $this->productRepository->search(new Criteria($ids), $context);

        $cleanedIds = [];
        foreach ($ids as $singleId) {
            $productRecord = $products->get($singleId);
            if (ProductService::isWWFProduct($productRecord)) {
                $this->logger->debug('wwf product detected!');
                continue; // skip wwf products from id list
            }
            $cleanedIds[] = $singleId;
        }
        if (empty($cleanedIds)) {
            return;
        }
        parent::update($cleanedIds, $context);
    }

    private function getProductsOfOrder(string $orderId): array
    {
        $query = $this->dbConnection->createQueryBuilder();
        $query->select(['referenced_id', 'quantity']);
        $query->from('order_line_item');
        $query->andWhere('type = :type');
        $query->andWhere('order_id = :id');
        $query->andWhere('version_id = :version');
        $query->setParameter('id', Uuid::fromHexToBytes($orderId));
        $query->setParameter('version', Uuid::fromHexToBytes(Defaults::LIVE_VERSION));
        $query->setParameter('type', LineItem::PRODUCT_LINE_ITEM_TYPE);

        return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
}