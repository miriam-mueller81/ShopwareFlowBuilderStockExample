<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Listener;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\Events\ProductStockAlteredEvent;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use ShopwareFlowBuilderStockExample\Content\Product\Event\ProductStockAlteredFlowEvent;
use ShopwareFlowBuilderStockExample\Content\Product\ProductService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductStockAlteredListener implements EventSubscriberInterface
{
    public function __construct(
        protected BusinessEventCollector $businessEventCollector,
        protected EventDispatcherInterface $eventDispatcher,
        protected LoggerInterface $logger,
        protected ProductService $productService
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
        foreach ($event->getIds() as $id) {
            $product = $this->productService->findProductById($id, $event->getContext());
            $this->logger->info($product);
            $this->eventDispatcher->dispatch(new ProductStockAlteredFlowEvent($event->getContext(), $product), ProductStockAlteredFlowEvent::EVENT_NAME);
        }
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