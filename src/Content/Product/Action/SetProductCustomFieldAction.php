<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Action;

use Shopware\Core\Content\Flow\Dispatching\Action\CustomFieldActionTrait;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\Event\OrderAware;
use Shopware\Core\Framework\Event\ProductAware;
use ShopwareFlowBuilderStockExample\Content\Product\Event\ProductStockAware;

class SetProductCustomFieldAction extends FlowAction
{
    use CustomFieldActionTrait;

    public static function getName(): string
    {
        return 'action.set.product.custom.field';
    }

    public function requirements(): array
    {
        // return []; => available for all events
        return [ProductAware::class]; // => only allow specific events
    }

    public function handleFlow(StorableFlow $flow): void
    {
        if (!$flow->hasData(ProductAware::PRODUCT_ID)) {
            return;
        }
    }
}