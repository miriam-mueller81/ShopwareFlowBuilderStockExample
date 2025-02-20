<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Listener;

use Monolog\Level;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PostWriteValidationEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use ShopwareFlowBuilderStockExample\Content\Product\Event\ProductStockChangedFlowEvent;
use ShopwareFlowBuilderStockExample\Content\Product\ProductService;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\StockSubscriberService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductStockChangedListener implements EventSubscriberInterface
{
    public function __construct(
        protected BusinessEventCollector $businessEventCollector,
        protected EventDispatcherInterface $eventDispatcher,
        protected ProductService $productService,
        protected StockSubscriberService $stockSubscriberService,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            PostWriteValidationEvent::class => 'onPostWriteValidation',
            PreWriteValidationEvent::class => 'onPreWriteValidation',
            BusinessEventCollectorEvent::NAME => ['onBusinessEventCollect', 1000],
        ];
    }

    public function onPreWriteValidation(PreWriteValidationEvent $event): void
    {
        foreach ($event->getCommands() as $command) {
            if ($command->getEntityName() === ProductDefinition::ENTITY_NAME
                && $command instanceof UpdateCommand
            ) {
                if ($command->hasField('stock')) {
                    $command->requestChangeSet();
                }
            }
        }
    }

    public function onPostWriteValidation(PostWriteValidationEvent $event): void
    {
        foreach ($event->getCommands() as $command) {
            if ($command->getEntityName() === ProductDefinition::ENTITY_NAME
                && $command instanceof UpdateCommand
            ) {
                if ($command->getChangeSet() === null) {
                    continue;
                }

                $productId = $command->getDecodedPrimaryKey()['id'];

                $productStockChangedEvent = new ProductStockChangedFlowEvent(
                    $event->getContext(),
                    $productId,
                    (int) $command->getChangeSet()->getBefore('stock'),
                    (int) $command->getChangeSet()->getAfter('stock'),
                    Level::Info,
                    new MailRecipientStruct($this->getStockSubscriberRecipients($productId, $event->getContext()))
                );
                $this->eventDispatcher->dispatch($productStockChangedEvent, ProductStockChangedFlowEvent::EVENT_NAME);
            }
        }
    }

    public function onBusinessEventCollect(BusinessEventCollectorEvent $event): void
    {
        $collection = $event->getCollection();

        $definition = $this->businessEventCollector->define(ProductStockChangedFlowEvent::class);

        if (!$definition) {
            return;
        }

        $collection->set($definition->getName(), $definition);
    }

    protected function getStockSubscriberRecipients(string $productId, Context $context): array
    {
        $stockSubscribers = $this->stockSubscriberService->findActiveStockSubscriberForProduct($productId, $context);

        $recipients = [];
        foreach ($stockSubscribers as $stockSubscriber) {
            $recipients[$stockSubscriber->getCustomer()->getEmail()] = $stockSubscriber->getCustomer()->getFirstName() . ' ' . $stockSubscriber->getCustomer()->getLastName();
        }
        return $recipients;
    }
}