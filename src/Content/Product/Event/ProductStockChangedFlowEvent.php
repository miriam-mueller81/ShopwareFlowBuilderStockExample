<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Event;

use Monolog\Level;
use Shopware\Core\Content\Flow\Dispatching\Aware\ScalarValuesAware;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\EventData\ScalarValueType;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\ProductAware;
use Shopware\Core\Framework\Log\LogAware;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStockChangedFlowEvent extends Event implements FlowEventAware, ProductAware, MailAware, LogAware, ScalarValuesAware
{
    public const EVENT_NAME = 'product.changed.stock';

    public function __construct(
        protected Context $context,
        protected string $productId,
        protected int $stockBefore,
        protected int $stockAfter,
        protected Level $logLevel,
        protected MailRecipientStruct $recipients,
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

    /* required by ProductAware */
    public function getProductId(): string
    {
        return $this->productId;
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

    /* required by LogAware */
    public function getLogData(): array
    {
        return [
            'stock_before' => $this->stockBefore,
            'stock_after' => $this->stockAfter,
        ];
    }

    /* required by LogAware */
    public function getLogLevel(): Level
    {
        return $this->logLevel;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('product', new EntityType(ProductDefinition::class))
            ->add('stockBefore', new ScalarValueType('int'))
            ->add('stockAfter', new ScalarValueType('int'));
    }

    public function getValues(): array
    {
        return [
            'stockBefore' => $this->stockBefore,
            'stockAfter' => $this->stockAfter,
        ];
    }
}