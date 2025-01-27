<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Api\StoreApi;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberCollection;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\StockSubscriberService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class StockSubscriberRoute extends AbstractStockSubscriberRoute
{
    public function __construct(
        protected StockSubscriberService $stockSubscriberService
    ) {
    }

    public function getDecorated(): AbstractStockSubscriberRoute
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * Example Json:
     * {
     *     customerId: <customerId>,
     *     productId: <productId>
     * }
     */
    #[Route(path: '/store-api/stock-subscriber/subscribe', name: 'store-api.stock-subscriber.subscribe', methods: ['POST'], defaults: ['_entity' => 'mmueller_stock_subscriber'])]
    public function subscribe(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): SuccessResponse
    {
        $this->stockSubscriberService->validateData($dataBag, $salesChannelContext);

        $existingCustomer = $this->stockSubscriberService->findStockSubscriber($dataBag->get('customerId'), $dataBag->get('productId'), $salesChannelContext);

        if ($existingCustomer === null) {
            $this->stockSubscriberService->createStockSubscriber($dataBag->get('customerId'), $dataBag->get('productId'), true, $salesChannelContext);
        }  else {
            $this->stockSubscriberService->updateStockSubscriber($existingCustomer->getId(), true, $salesChannelContext);
        }

        return new SuccessResponse();
    }

    #[Route(path: '/store-api/stock-subscriber/unsubscribe', name: 'store-api.stock-subscriber.unsubscribe', methods: ['POST'], defaults: ['_entity' => 'mmueller_stock_subscriber'])]
    public function unsubscribe(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): SuccessResponse
    {
        $this->stockSubscriberService->validateData($dataBag, $salesChannelContext);

        $existingCustomer = $this->stockSubscriberService->findStockSubscriber($dataBag->get('customerId'), $dataBag->get('productId'), $salesChannelContext);

        $this->stockSubscriberService->updateStockSubscriber($existingCustomer->getId(), false, $salesChannelContext);

        return new SuccessResponse();
    }
}