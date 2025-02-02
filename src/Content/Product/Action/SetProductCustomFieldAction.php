<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Action;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Flow\Dispatching\Action\CustomFieldActionTrait;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Event\ProductAware;

class SetProductCustomFieldAction extends FlowAction
{
    use CustomFieldActionTrait;

    public function __construct(
        protected Connection $connection,
        protected EntityRepository $productRepository,
        protected StringTemplateRenderer $templateRenderer
    ) {
    }

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
        if (!$flow->hasData(ProductAware::PRODUCT)) {
            return;
        }

        /** @var ProductEntity $product */
        $product = $flow->getData(ProductAware::PRODUCT);

        $customFields = $this->getCustomFieldForUpdating($product->getCustomfields(), $flow->getConfig());

        if ($customFields === null) {
            return;
        }

        $renderedCustomFields = [];
        foreach ($customFields as $key => $customField) {
            $customFieldName = $this->getCustomFieldNameFromId($flow->getConfig()['customFieldId'], $flow->getConfig()['entity']);
            if ($customFieldName !== $key) {
                continue;
            }
            $renderedValue = $this->templateRenderer->render($customField, $flow->getVars()['data'], $flow->getContext());
            $renderedCustomFields[$key] = $renderedValue;
            $customFields[$key] = $renderedValue;
        }

        $product->setCustomfields($customFields);
        $flow->setData('product', $product);

        $this->productRepository->upsert([
            [
                'id' => $product->getId(),
                'customFields' => $renderedCustomFields,
            ],
        ], $flow->getContext());
    }
}