<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Api\StoreApi;

use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SuccessResponse;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event\StockSubscriberSubscribedEvent;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event\StockSubscriberUnsubscribedEvent;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\StockSubscriberService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class StockSubscriberRoute extends AbstractStockSubscriberRoute
{
    public function __construct(
        protected StockSubscriberService $stockSubscriberService,
        protected EventDispatcherInterface $eventDispatcher,
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
            $stockSubscriber = $this->stockSubscriberService->createStockSubscriber($dataBag->get('customerId'), $dataBag->get('productId'), true, $salesChannelContext);
        }  else {
            $stockSubscriber = $this->stockSubscriberService->updateStockSubscriber($existingCustomer->getId(), true, $salesChannelContext);
        }

        if ($stockSubscriber) {
            $stockSubscriberSubscribeEvent = new StockSubscriberSubscribedEvent(
                $salesChannelContext->getContext(),
                new MailRecipientStruct([
                    $stockSubscriber->getCustomer()->getEmail() => $stockSubscriber->getCustomer()->getFirstName() . ' ' . $stockSubscriber->getCustomer()->getLastName(),
                ]),
                $stockSubscriber->getId(),
                $stockSubscriber->getCustomer()->getId(),
            );
            $this->eventDispatcher->dispatch($stockSubscriberSubscribeEvent, StockSubscriberSubscribedEvent::EVENT_NAME);
        }

        return new SuccessResponse();
    }

    #[Route(path: '/store-api/stock-subscriber/unsubscribe', name: 'store-api.stock-subscriber.unsubscribe', methods: ['POST'], defaults: ['_entity' => 'mmueller_stock_subscriber'])]
    public function unsubscribe(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): SuccessResponse
    {
        $this->stockSubscriberService->validateData($dataBag, $salesChannelContext);

        $existingStockSubscriber = $this->stockSubscriberService->findStockSubscriber($dataBag->get('customerId'), $dataBag->get('productId'), $salesChannelContext);

        $stockSubscriber = $this->stockSubscriberService->updateStockSubscriber($existingStockSubscriber->getId(), false, $salesChannelContext);

        if ($stockSubscriber) {
            $stockSubscriberUnsubscribeEvent = new StockSubscriberUnsubscribedEvent(
                $salesChannelContext->getContext(),
                new MailRecipientStruct([
                    $stockSubscriber->getCustomer()->getEmail() => $stockSubscriber->getCustomer()->getFirstName() . ' ' . $stockSubscriber->getCustomer()->getLastName(),
                ]),
                $stockSubscriber->getId(),
                $stockSubscriber->getCustomer()->getId(),
            );
            $this->eventDispatcher->dispatch($stockSubscriberUnsubscribeEvent, StockSubscriberUnsubscribedEvent::EVENT_NAME);
        }

        return new SuccessResponse();
    }
}