# Priceva SDK

SDK for Priceva API (PHP)

# Getting Started

## Requirements

* PHP >= 5.4
* cURL library
* JSON library

## Installing

### Via Composer

Go to the project root directory and run:
````bash
php composer require priceva/priceva-sdk-php
````
or add this string in `require` section of your `composer.json`:
````
"priceva/priceva-sdk-php": "dev-master"
````
and run `composer install`.

### Without Composer

1. Download our library.
2. Include files in your php root file:
    ````php
    include_once '/path/to/lib/PricevaAPI.php';
    include_once '/path/to/lib/PricevaException.php';
    include_once '/path/to/lib/Request.php';
    include_once '/path/to/lib/Result.php';
    // ...and other our files if it needed
    ````
    
## Use

Simplest example:

````php
use Priceva\PricevaAPI;


try{
    // or include our files directly, if you don't want to use Composer
    require_once __DIR__ . "/../vendor/autoload.php";

    $api = new PricevaAPI('your_api_key');

    $result = $api->main_ping();

}catch( \Exception $e ){
    // error handler
}
````

Get a list of products:

````php
try{
    // or include our files directly, if you don't want to use Composer
    require_once __DIR__ . "/../vendor/autoload.php";

    $api = new PricevaAPI('your_api_key');

    $filters = new \Priceva\Params\Filters();
    $sources = new \Priceva\Params\Sources();

    $filters[ 'page' ]      = 1;
    $filters[ 'region_id' ] = 'a';

    $sources[ 'add' ]      = true;
    $sources[ 'add_term' ] = true;

    $products = $api->product_list($filters, $sources);
}catch( \Exception $e ){
    // error handler
}
````

Get a list of reports:

````php
try{
    // or include our files directly, if you don't want to use Composer
    require_once __DIR__ . "/../vendor/autoload.php";

    $api = new PricevaAPI('your_api_key');

    $filters        = new \Priceva\Params\Filters();
    $product_fields = new \Priceva\Params\ProductFields();

    $filters[ 'page' ]      = 1;
    $filters[ 'region_id' ] = 'a';
    
    // we use 'flat model' of parameters here
    $product_fields[] = 'client_code';
    $product_fields[] = 'articul';

    $reports = $api->report_list($filters, $product_fields);
}catch( \Exception $e ){
    // error handler
}
````

Work with pagination:

````php
$api = new PricevaAPI($api_key);

$filters        = new \Priceva\Params\Filters();
$product_fields = new \Priceva\Params\ProductFields();

$filters[ 'limit' ] = '1000'; // for example
$filters[ 'page' ]  = 1; // strong

... // some filters

$product_fields[] = 'client_code'; // if we need it field in answer
$product_fields[] = 'articul';  // if we need it field in answer

... // some product fields

$reports = $api->report_list($filters, $product_fields);

$pages_cnt = (int)$reports->get_result()->pagination->pages_cnt;

$priceva_products = $reports->get_result()->objects;

process_products($priceva_products); // client function for product processing

while( $pages_cnt > 1 ){
  $filters[ 'page' ] = $pages_cnt--;

  $reports = $api->report_list($filters, $product_fields);

  $priceva_products = $reports->get_result()->objects;

  process_products($priceva_products); // client function for product processing
}
````

## API actions

* main/ping
* main/demo
* product/list
* report/list

## Request parameters

### Filters

Applicable in methods: `product/list`, `report/list`. Possible options:

* page
* limit
* category_id
* brand_id
* company_id
* region_id
* active
* name
* articul
* client_code

### Sources

Applicable in methods: `product/list`. Possible options:

* add
* add_term

### Product fields

Applicable in methods: `report/list`. Possible options:

* client_code
* articul
* name
* active
* default_price
* default_available
* default_discount_type
* default_discount
* repricing_min
* default_currency

## Additional information

Read more about our API [here](https://priceva.docs.apiary.io/#introduction).
