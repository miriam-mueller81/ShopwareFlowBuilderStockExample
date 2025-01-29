<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Event;

use Monolog\Level;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\ProductAware;
use Shopware\Core\Framework\Log\LogAware;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStockChangedFlowEvent extends Event implements ProductAware, MailAware, LogAware, FlowEventAware
{
    public const EVENT_NAME = 'product.changed.stock';

    public function __construct(
        protected Context $context,
        protected ProductEntity $product,
        protected int $stockBefore,
        protected int $stockAfter,
        protected Level $logLevel,
        protected MailRecipientStruct $recipients,
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getProduct(): ProductEntity
    {
        return $this->product;
    }

    public function getProductId(): string
    {
        return $this->product->getId();
    }

    public function getSalesChannelId(): ?string
    {
        if (method_exists($this->context->getSource(), 'getSalesChannelId')) {
            return $this->context->getSource()->getSalesChannelId();
        }

        return null;
    }

    public function getLogData(): array
    {
        return [
            'stock_before' => $this->stockBefore,
            'stock_after' => $this->stockAfter,
        ];
    }

    public function getLogLevel(): Level
    {
        return $this->logLevel;
    }

    public function getMailStruct(): MailRecipientStruct
    {
        return $this->recipients;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('product', new EntityType(ProductDefinition::class));
    }
}