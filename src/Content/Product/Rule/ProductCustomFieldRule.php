<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Product\Rule;

use Shopware\Core\Framework\Rule\CustomFieldRule;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;

class ProductCustomFieldRule extends Rule
{
    final public const RULE_NAME = 'productCustomField';

    protected array|string|int|bool|float|null $renderedFieldValue = null;

    protected ?string $selectedField = null;

    protected ?string $selectedFieldSet = null;

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected array $renderedField = [],
    ) {
        parent::__construct();
    }

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof ProductCustomFieldScope) {
            return false;
        }

        $productCustomFields = $scope->getProduct()->getCustomFields() ?? [];

        return CustomFieldRule::match($this->renderedField, $this->renderedFieldValue, $this->operator, $productCustomFields);
    }

    public function getConstraints(): array
    {
        return CustomFieldRule::getConstraints($this->renderedField);
    }
}