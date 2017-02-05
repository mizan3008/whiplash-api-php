Whiplash API
=============

Super-simple, minimum abstraction Whiplash API v1 wrapper for PHP/Laravel.

Requires PHP 5.3 and a pulse.

Whiplash relies on 3 tables for the majority of calls: Items, Orders, and Order Items. Items are specific inventory items, Orders are requests for a shipment to be sent, and Order Items are what that order should contain.

Installation
------------

You can install the whiplash-api using Composer:

```
composer require whiplash/whiplash-api-php
```

Examples
--------

use this on top of your class:

```
use Whiplash\WhiplashApi;
```

Initialize the Whiplash API with your Whiplash API key:

```
$apiKey = 'YOUR API KEY';
$apiVersion = 'v1'; // OPTIONAL: Leave this blank to use the most recent API
$mode = true; // OPTIONAL: If mode is true, this will use your sandbox account
$api = new WhiplashApi($apiKey, $apiVersion, $mode);
```

Create item:
```
$item = $api->create_item(array('sku' => 'NEW_SKU_123', 'name' => 'My Test Item', 'description' => 'My item description' ));
```

Get item list:
```
$items = $api->get_items();
print_r($items);
```

Get item by id:
```
$item = $api->get_item(ITEM_ID);
print_r($item);
```

*More documentation coming soon...
