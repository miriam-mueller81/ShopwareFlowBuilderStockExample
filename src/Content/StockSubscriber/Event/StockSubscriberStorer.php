<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event;

use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Content\Flow\Dispatching\Storer\FlowStorer;
use Shopware\Core\Framework\Event\FlowEventAware;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\StockSubscriberService;

class StockSubscriberStorer extends FlowStorer
{
    public function __construct(protected StockSubscriberService $stockSubscriberService)
    {
    }

    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof StockSubscriberAware || isset($stored[StockSubscriberAware::STOCKSUBSCRIBER_ID])) {
            return $stored;
        }

        $stored[StockSubscriberAware::STOCKSUBSCRIBER_ID] = $event->getStockSubscriberId();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(StockSubscriberAware::STOCKSUBSCRIBER_ID)) {
            return;
        }

        $stockSubscriberId = $storable->getStore(StockSubscriberAware::STOCKSUBSCRIBER_ID);

        $storable->setData(StockSubscriberAware::STOCKSUBSCRIBER_ID, $stockSubscriberId);

        $stockSubscriber = $this->stockSubscriberService->findStockSubscriberById($storable->getStore(StockSubscriberAware::STOCKSUBSCRIBER_ID), $storable->getContext());

        $storable->setData(StockSubscriberAware::STOCKSUBSCRIBER, $stockSubscriber);
    }
}