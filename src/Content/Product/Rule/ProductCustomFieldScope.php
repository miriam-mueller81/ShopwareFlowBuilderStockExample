<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Rule;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ProductCustomFieldScope extends RuleScope
{
    public function __construct(
        protected ProductEntity $product,
        protected SalesChannelContext $salesChannelContext,
    ) {
    }

    public function getProduct(): ProductEntity
    {
        return $this->product;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }
}