<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberEntity;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StockSubscriberService
{
    public function __construct(
        protected EntityRepository $stockSubscriberRepository,
        protected EntityRepository $customerRepository,
        protected EntityRepository $productRepository,
    ) {
    }

    public function validateData(RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): void
    {
        if (!$dataBag->has('customerId')) {
            throw new BadRequestException('Missing customerId');
        }

        if (!$dataBag->get('productId')) {
            throw new BadRequestException('Missing productId');
        }

        if (!$this->customerExists((string) $dataBag->get('customerId'), $salesChannelContext)) {
            throw new NotFoundHttpException('Customer not found');
        }

        if (!$this->productExists((string) $dataBag->get('productId'), $salesChannelContext)) {
            throw new NotFoundHttpException('Product not found');
        }
    }

    protected function customerExists(string $customerId, SalesChannelContext $salesChannelContext): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $customerId));
        $customer = $this->customerRepository->search($criteria, $salesChannelContext->getContext())->first();

        return $customer !== null;
    }

    protected function productExists(string $productId, SalesChannelContext $salesChannelContext): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $productId));
        $product = $this->productRepository->search($criteria, $salesChannelContext->getContext())->first();

        return $product !== null;
    }

    public function findStockSubscriber(string $customerId, string $productId, SalesChannelContext $salesChannelContext): ?StockSubscriberEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        return $this->stockSubscriberRepository->search($criteria, $salesChannelContext->getContext())->first();
    }

    public function createStockSubscriber(string $customerId, string $productId, bool $active, SalesChannelContext $salesChannelContext): void
    {
        $this->stockSubscriberRepository->create([
            [
                'customerId' => $customerId,
                'productId' => $productId,
                'active' => $active,
            ]
        ], $salesChannelContext->getContext());
    }

    public function updateStockSubscriber(string $id, bool $active, SalesChannelContext $salesChannelContext): void
    {
        $this->stockSubscriberRepository->update([
            [
                'id' => $id,
                'active' => $active,
            ]
        ], $salesChannelContext->getContext());
    }
}