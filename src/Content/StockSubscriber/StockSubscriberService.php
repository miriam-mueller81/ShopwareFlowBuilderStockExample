<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\StockSubscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberCollection;
use ShopwareFlowBuilderStockExample\Content\StockSubscriber\Entity\StockSubscriberDefinition;
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

    public function findStockSubscriberById(string $id, Context $context): ?StockSubscriberEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $id));
        $criteria->addAssociation('customer');
        $criteria->addAssociation('product');

        return $this->stockSubscriberRepository->search($criteria, $context)->first();
    }

    public function findActiveStockSubscriberForProduct(string $productId, Context $context): StockSubscriberCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addAssociation('customer');

        return $this->stockSubscriberRepository->search($criteria, $context)->getEntities();
    }

    public function createStockSubscriber(string $customerId, string $productId, bool $active, SalesChannelContext $salesChannelContext): ?StockSubscriberEntity
    {
        $createdIds = $this->stockSubscriberRepository->create([
            [
                'customerId' => $customerId,
                'productId' => $productId,
                'active' => $active,
            ]
        ], $salesChannelContext->getContext())->getPrimaryKeys(StockSubscriberDefinition::ENTITY_NAME);

        if (empty($createdIds)) {
            return null;
        }

        return $stockSubscriber = $this->stockSubscriberRepository->search(
            (new Criteria($createdIds))
                ->addAssociation('customer')
                ->addAssociation('product'),
            $salesChannelContext->getContext()
        )->first();
    }

    public function updateStockSubscriber(string $id, bool $active, SalesChannelContext $salesChannelContext): ?StockSubscriberEntity
    {
        $updatedIds = $this->stockSubscriberRepository->update([
            [
                'id' => $id,
                'active' => $active,
            ]
        ], $salesChannelContext->getContext())->getPrimaryKeys(StockSubscriberDefinition::ENTITY_NAME);

        if (empty($updatedIds)) {
            return null;
        }

        return $stockSubscriber = $this->stockSubscriberRepository->search(
            (new Criteria($updatedIds))
                ->addAssociation('customer')
                ->addAssociation('product'),
            $salesChannelContext->getContext()
        )->first();
    }
}