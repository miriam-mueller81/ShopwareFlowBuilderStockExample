# Shopware flowbuilder stock example

## Install plugin

* Install plugin in administration or via CLI
```
bin/console plugin:refresh && bin/console plugin:install -ac ShopwareFlowBuilderStockExample
```
* Set default saleschannel to plugin configuration

## API endpoints for subscriber list

**Subscribe to list**
* Method: POST
* Request url: /store-api/stock-subscriber/subscribe
* Request body:
```json
{
    "customerId": "019493b442f9739ba2b01c1586f6a68d",
    "productId": "019493b461d87207a53ddf81bd00c86c"
}
```

**Unsubscribe to list**
* Method: POST
* Request url: /store-api/stock-subscriber/unsubscribe
* Request body:
```json
{
    "customerId": "019493b442f9739ba2b01c1586f6a68d",
    "productId": "019493b461d87207a53ddf81bd00c86c"
}
```