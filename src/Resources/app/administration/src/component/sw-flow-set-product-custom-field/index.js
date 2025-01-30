import template from './sw-flow-set-product-custom-field.html.twig';

const { Component, Context } = Shopware;

Component.register('sw-flow-set-product-custom-field-modal', {
    template,

    inject: [
        'repositoryFactory',
    ],

    computed: {

    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {

        },

        onClose() {
            this.$emit('modal-close');
        },
    }
});