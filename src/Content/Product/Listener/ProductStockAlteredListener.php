<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Listener;

use Shopware\Core\Content\Product\Events\ProductStockAlteredEvent;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use ShopwareFlowBuilderStockExample\Content\Product\Event\ProductStockAlteredFlowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductStockAlteredListener implements EventSubscriberInterface
{
    public function __construct(
        protected BusinessEventCollector $businessEventCollector,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductStockAlteredEvent::class => 'onProductStockAltered',
            BusinessEventCollectorEvent::NAME => ['onAddProductStockAlteredEvent', 1000],
        ];
    }

    public function onProductStockAltered(ProductStockAlteredEvent $event): void
    {
        $this->eventDispatcher->dispatch(ProductStockAlteredFlowEvent::class, ProductStockAlteredFlowEvent::EVENT_NAME);
    }

    public function onAddProductStockAlteredEvent(BusinessEventCollectorEvent $event): void
    {
        $collection = $event->getCollection();

        $definition = $this->businessEventCollector->define(ProductStockAlteredFlowEvent::class);

        if (!$definition) {
            return;
        }

        $collection->set($definition->getName(), $definition);
    }
}