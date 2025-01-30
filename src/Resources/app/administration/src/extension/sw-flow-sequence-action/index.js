import {ACTION, GROUP} from '../../constant/set-product-custom-field-action.constant';
import {
    ActionContext
} from "../../../../../../../../../shopware/administration/Resources/app/administration/src/module/sw-flow/service/flow-builder.service";

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {
        groups() { // only required if new group
            this.actionGroups.unshift(GROUP);

            return this.$super('groups');
        },

        modalName() {
            if (this.selectedAction === ACTION.SET_PRODUCT_CUSTOM_FIELD) {
                return 'sw-flow-set-entity-custom-field-modal';
            }

            return this.$super('modalName');
        },
    },

    methods: {
        getActionDescriptions(sequence) {
            console.log('SEQUENCE');
            /*const context: ActionContext = { data, sequence, translator };

            if(sequence.actionName === ACTION.SET_PRODUCT_CUSTOM_FIELD){
                this.$super('getCustomFieldDescription', context);
            }
            return this.$super('getActionDescriptions', sequence)*/

        },

        getActionTitle(actionName) {
            if (actionName === ACTION.SET_PRODUCT_CUSTOM_FIELD) {
                return {
                    value: actionName,
                    icon: 'regular-file-signature',
                    label: this.$tc('set-product-custom-field-action.title'),
                    group: GROUP,
                }
            }

            return this.$super('getActionTitle', actionName);
        },
    },
});
