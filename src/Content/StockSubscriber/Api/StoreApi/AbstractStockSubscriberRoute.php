<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Api\StoreApi;

use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;

abstract class AbstractStockSubscriberRoute
{
    abstract public function getDecorated(): AbstractStockSubscriberRoute;

    abstract public function subscribe(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): SuccessResponse;

    abstract public function unsubscribe(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): SuccessResponse;
}