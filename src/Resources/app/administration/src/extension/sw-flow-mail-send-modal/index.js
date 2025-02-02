const { Component } = Shopware;

Component.override('sw-flow-mail-send-modal', {
    computed: {
        recipientOptions() {
            const recipientOptions = this.$super('recipientOptions');

            if (this.triggerEvent.name === 'product.changed.stock') {
                return [
                    ...recipientOptions,
                    {
                        value: 'stockSubscriber',
                        label: this.$tc('sw-flow.modals.mail.labelStockSubscriberMail'),
                    }
                ];
            }

            return recipientOptions;
        }
    }
});