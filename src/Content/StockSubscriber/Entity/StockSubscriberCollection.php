<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(StockSubscriberEntity $entity)
 * @method void                         set(string $key, StockSubscriberEntity $entity)
 * @method StockSubscriberEntity[]      getIterator()
 * @method StockSubscriberEntity[]      getElements()
 * @method StockSubscriberEntity|null   get(string $key)
 * @method StockSubscriberEntity|null   first()
 * @method StockSubscriberEntity|null   last()
 */
class StockSubscriberCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return StockSubscriberEntity::class;
    }
}