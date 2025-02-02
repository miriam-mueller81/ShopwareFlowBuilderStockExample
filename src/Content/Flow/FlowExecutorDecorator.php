<?php

declare(strict_types=1);

namespace ShopwareFlowBuilderStockExample\Content\Flow;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Cart\AbstractRuleLoader;
use Shopware\Core\Content\Flow\Dispatching\FlowExecutor;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Content\Flow\Dispatching\Struct\IfSequence;
use Shopware\Core\Content\Flow\Rule\FlowRuleScopeBuilder;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\App\Flow\Action\AppFlowActionProvider;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\ProductAware;
use Shopware\Core\Framework\Extensions\ExtensionDispatcher;
use Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use ShopwareFlowBuilderStockExample\Content\Product\Event\ProductStockChangedFlowEvent;
use ShopwareFlowBuilderStockExample\Content\Product\Rule\ProductCustomFieldScope;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FlowExecutorDecorator extends FlowExecutor
{
    public function __construct(
        protected EventDispatcherInterface $dispatcher,
        protected AppFlowActionProvider $appFlowActionProvider,
        protected AbstractRuleLoader $ruleLoader,
        protected FlowRuleScopeBuilder $scopeBuilder,
        protected Connection $connection,
        protected ExtensionDispatcher $extensions,
        $actions,
        protected SystemConfigService $systemConfigService,
        protected CachedSalesChannelContextFactory $salesChannelContextFactory,
        protected FlowExecutor $originalService,
    ) {
        parent::__construct($dispatcher, $appFlowActionProvider, $ruleLoader, $scopeBuilder, $this->connection, $this->extensions, $actions);
    }

    public function executeIf(IfSequence $sequence, StorableFlow $event): void
    {
        if ($event->getName() !== ProductStockChangedFlowEvent::EVENT_NAME) {
            $this->originalService->executeIf($sequence, $event);
        }

        if ($this->sequenceRuleMatches($event, $sequence->ruleId)) {
            $this->executeSequence($sequence->trueCase, $event);

            return;
        }

        $this->executeSequence($sequence->falseCase, $event);
    }

    private function sequenceRuleMatches(StorableFlow $event, string $ruleId): bool
    {
        if (!$event->hasData(ProductAware::PRODUCT)) {
            return \in_array($ruleId, $event->getContext()->getRuleIds(), true);
        }

        /** @var ProductEntity $product */
        $product = $event->getData(ProductAware::PRODUCT);

        if (!$product instanceof ProductEntity) {
            return \in_array($ruleId, $event->getContext()->getRuleIds(), true);
        }

        $rule = $this->ruleLoader->load($event->getContext())->filterForFlow()->get($ruleId);

        $saleschannelId = $this->systemConfigService->get('ShopwareFlowBuilderStockExample.config.saleschannel');
        $saleschannelContext = $this->salesChannelContextFactory->create('', $saleschannelId);

        $isRuleValid = $rule->getPayload()->match(new ProductCustomFieldScope($product, $saleschannelContext));

        return true;
    }
}