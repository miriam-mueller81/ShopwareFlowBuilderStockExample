Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {

    ruleConditionService.upsertGroup('product', {
        id: 'product',
        name: 'sw-settings-rule.detail.groups.product',
    });

    ruleConditionService.addCondition('productCustomField', {
        component: 'sw-condition-product-custom-field',
        label: 'sw-condition.condition.product.productCustomFieldRule',
        scopes: ['product'],
        group: 'product',
    });

    return ruleConditionService;
});