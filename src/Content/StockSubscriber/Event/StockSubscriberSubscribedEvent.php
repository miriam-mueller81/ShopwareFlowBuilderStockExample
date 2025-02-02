<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber\Event;

use Monolog\Level;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\CustomerAware;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Log\LogAware;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberDefinition;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberEntity;
use Symfony\Contracts\EventDispatcher\Event;

class StockSubscriberSubscribedEvent extends Event implements FlowEventAware, MailAware, CustomerAware, StockSubscriberAware
{
    public const EVENT_NAME = 'stock_subscriber.subscribed';

    public function __construct(
        protected Context $context,
        protected MailRecipientStruct $recipients,
        protected string $stockSubscriberId,
        protected string $customerId,
    ) {
    }

    /* required by FlowEventAware */
    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    /* required by FlowEventAware */
    public function getContext(): Context
    {
        return $this->context;
    }

    /* required by MailAware */
    public function getSalesChannelId(): ?string
    {
        if (method_exists($this->context->getSource(), 'getSalesChannelId')) {
            return $this->context->getSource()->getSalesChannelId();
        }

        return null;
    }

    /* required by MailAware */
    public function getMailStruct(): MailRecipientStruct
    {
        return $this->recipients;
    }

    /* required by CustomerAware */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /* required by StockSubscriber Aware */
    public function getStockSubscriberId(): string
    {
        return $this->stockSubscriberId;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('stockSubscriber', new EntityType(StockSubscriberDefinition::class));
    }
}