<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Api\Storefront;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;

abstract class AbstractStockSubscriberRoute
{
    abstract public function getDecorated(): AbstractStockSubscriberRoute;

    abstract public function subscribe(SalesChannelContext $salesChannelContext): SuccessResponse;

    abstract public function unsubscribe(SalesChannelContext $salesChannelContext): SuccessResponse;
}