<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event;

use Shopware\Core\Framework\Event\FlowEventAware;

interface StockSubscriberAware extends FlowEventAware
{
    public const STOCKSUBSCRIBER_ID = 'stockSubscriberId';

    public const STOCKSUBSCRIBER = 'stockSubscriber';

    public function getStockSubscriberId(): string;
}