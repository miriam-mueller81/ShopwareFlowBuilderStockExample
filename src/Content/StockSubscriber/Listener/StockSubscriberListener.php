<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Listener;

use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event\StockSubscriberSubscribedEvent;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event\StockSubscriberUnsubscribedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StockSubscriberListener implements EventSubscriberInterface
{
    public function __construct(protected BusinessEventCollector $businessEventCollector,)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BusinessEventCollectorEvent::NAME => ['onBusinessEventCollect', 1000],
        ];
    }

    public function onBusinessEventCollect(BusinessEventCollectorEvent $event): void
    {
        $collection = $event->getCollection();

        $subscribedDefinition = $this->businessEventCollector->define(StockSubscriberSubscribedEvent::class);
        $unsubscribedDefinition = $this->businessEventCollector->define(StockSubscriberUnsubscribedEvent::class);

        if ($subscribedDefinition) {
            $collection->set($subscribedDefinition->getName(), $subscribedDefinition);
        }

        if ($unsubscribedDefinition) {
            $collection->set($unsubscribedDefinition->getName(), $unsubscribedDefinition);
        }
    }
}