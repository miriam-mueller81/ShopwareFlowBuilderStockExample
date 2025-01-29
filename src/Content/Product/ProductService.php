<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductService
{
    public function __construct(
        protected EntityRepository $productRepository,
    ) {
    }

    public function findProductById(string $id, Context $context): ?ProductEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $id));

        return $this->productRepository->search($criteria, $context)->first();
    }
}