<?php


namespace WWFDonationPlugin\Subscriber;


use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Content\Product\DataAbstractionLayer\StockUpdater;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\StateMachine\Event\StateMachineTransitionEvent;
use Symfony\Component\VarDumper\VarDumper;

/**
 * This replaces the original stockupdater mechanisms and filters out wwf donation products
 *
 * Class WWFProductStockUpdater
 * @package WWFDonationPlugin\Subscriber
 */
class WWFProductStockUpdater extends StockUpdater
{
    protected $dbConnection;

    public function __construct(
        Connection $connection,
        ProductDefinition $definition,
        CacheClearer $cache,
        EntityCacheKeyGenerator $cacheKeyGenerator
    )
    {
        parent::__construct($connection, $definition, $cache, $cacheKeyGenerator);
        $this->dbConnection = $connection;
    }

    /**
     * Returns a list of custom business events to listen where the product maybe changed
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

    public function stateChanged(StateMachineTransitionEvent $event): void
    {
        $products = $this->getProductsOfOrder($event->getEntityId());
        VarDumper::dump('state changed');
        VarDumper::dump($event->getEntityId());
        VarDumper::dump($products);
        parent::stateChanged($event);
    }

    public function update(array $ids, Context $context): void
    {
        VarDumper::dump('update');
        VarDumper::dump($ids);
        $products = $this->getProductsOfOrder($ids[0]);
        VarDumper::dump($products);

        parent::update($ids, $context);
    }

    public function orderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        VarDumper::dump($event);
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