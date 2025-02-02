import { ACTION, GROUP } from '../../constant/set-product-custom-field-action.constant';

const { Component } = Shopware;
const { mapState, mapGetters } = Component.getComponentHelper();

Component.override('sw-flow-sequence-action', {
    inject: [
        'flowBuilderService',
    ],

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

    created() {
        this.flowBuilderService.$entityAction[ACTION.SET_PRODUCT_CUSTOM_FIELD] = 'product';
    },

    methods: {
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
