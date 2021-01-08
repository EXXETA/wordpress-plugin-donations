<?php


namespace WWFDonationPlugin\Subscriber;


use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Content\Product\DataAbstractionLayer\StockUpdater;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\StateMachine\Event\StateMachineTransitionEvent;
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
     * @param Connection $connection
     * @param ProductDefinition $definition
     * @param CacheClearer $cache
     * @param EntityCacheKeyGenerator $cacheKeyGenerator
     * @param Logger $logger
     * @param EntityRepository $productRepository
     */
    public function __construct(
        Connection $connection,
        ProductDefinition $definition,
        CacheClearer $cache,
        EntityCacheKeyGenerator $cacheKeyGenerator,
        Logger $logger,
        EntityRepository $productRepository
    )
    {
        parent::__construct($connection, $definition, $cache, $cacheKeyGenerator);
        $this->dbConnection = $connection;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     * Returns a list of custom business events to listen where the product maybe changed
     *
     * We need to register at least the events of the default sw StockUpdater
     */
    public static function getSubscribedEvents()
    {
        return [
            CheckoutOrderPlacedEvent::class => 'orderPlaced',
            StateMachineTransitionEvent::class => 'stateChanged',
            PreWriteValidationEvent::class => 'triggerChangeSet',
            OrderEvents::ORDER_LINE_ITEM_WRITTEN_EVENT => 'lineItemWritten',
            OrderEvents::ORDER_LINE_ITEM_DELETED_EVENT => 'lineItemWritten',
        ];
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
            if (ProductService::isWWFProduct($singleProduct) &&
                ($singleProduct->getStock() != ProductService::WWF_PRODUCT_DEFAULT_STOCK
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
        $this->logger->debug('update');
        $products = $this->productRepository->search(new Criteria($ids), $context);
        $this->logger->debug('products:!');

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

    /**
     * simple delegation
     *
     * @param CheckoutOrderPlacedEvent $event
     */
    public function orderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        parent::orderPlaced($event);
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