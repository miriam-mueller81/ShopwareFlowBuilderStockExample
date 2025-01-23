<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Event;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\ProductAware;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStockAlteredFlowEvent extends Event implements ProductAware, FlowEventAware
{
    public const EVENT_NAME = 'product.stock.altered';

    public function __construct(protected Context $context, protected ProductEntity $product)
    {
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

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('product', new EntityType(ProductDefinition::class));
    }
}