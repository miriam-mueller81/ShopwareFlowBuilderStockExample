<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Api\Storefront;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class StockSubscriberRoute extends AbstractStockSubscriberRoute
{
    public function __construct(protected EntityRepository $stockSubscriberRepository)
    {
    }

    public function getDecorated(): AbstractStockSubscriberRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/stock-subscriber/subscribe', name: 'store-api.stock-subscriber.subscribe', methods: ['POST'], defaults: ['_entity' => 'mmueller_stock_subscriber'])]
    public function subscribe(SalesChannelContext $salesChannelContext): SuccessResponse
    {
        return new SuccessResponse();
    }

    #[Route(path: '/store-api/stock-subscriber/unsubscribe', name: 'store-api.stock-subscriber.unsubscribe', methods: ['POST'], defaults: ['_entity' => 'mmueller_stock_subscriber'])]
    public function unsubscribe(SalesChannelContext $salesChannelContext): SuccessResponse
    {
        // TODO: Implement unsubscribe() method.
    }
}