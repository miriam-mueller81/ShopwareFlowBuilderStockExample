<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event;

use Shopware\Core\Framework\Event\FlowEventAware;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberEntity;

interface StockSubscriberAware extends FlowEventAware
{
    public const STOCKSUBSCRIBER_ID = 'stockSubscriberId';

    public const STOCKSUBSCRIBER = 'stockSubscriber';

    public function getStockSubscriberId(): string;
}