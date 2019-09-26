<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 14:42
 */


$DEFAULT_VALUES = [
    "API_KEY"                     => [ 'type' => 'string', 'value' => "" ],
    "DEBUG"                       => [ 'type' => 'bool', 'value' => "NO" ],
    "SIMPLE_PRODUCT_ENABLE"       => [ 'type' => 'bool', 'value' => "YES" ],
    "IBLOCK_TYPE_ID"              => [ 'type' => 'string', 'value' => "catalog" ],
    "IBLOCK_MODE"                 => [ 'type' => 'string', 'value' => "2" ],
    "IBLOCK_ID"                   => [ 'type' => 'int', 'value' => "0" ],
    "TRADE_OFFERS_ENABLE"         => [ 'type' => 'bool', 'value' => "NO" ],
    "TRADE_OFFERS_IBLOCK_TYPE_ID" => [ 'type' => 'string', 'value' => "0" ],
    "TRADE_OFFERS_IBLOCK_MODE"    => [ 'type' => 'string', 'value' => "0" ],
    "TRADE_OFFERS_IBLOCK_ID"      => [ 'type' => 'int', 'value' => "0" ],
    "SYNC_FIELD"                  => [ 'type' => 'string', 'value' => "articul" ],
    "CLIENT_CODE"                 => [ 'type' => 'string', 'value' => "ID" ],
    "SYNC_DOMINANCE"              => [ 'type' => 'string', 'value' => "priceva" ],
    "SYNC_ONLY_ACTIVE"            => [ 'type' => 'bool', 'value' => "YES" ],
    "DOWNLOAD_AT_TIME"            => [ 'type' => 'int', 'value' => "1000" ],
    "ID_TYPE_PRICE"               => [ 'type' => 'int', 'value' => "0" ],
    "ID_TYPE_PRICE_PRICEVA"       => [ 'type' => 'int', 'value' => "0" ],
    "PRICE_RECALC"                => [ 'type' => 'bool', 'value' => "NO" ],
    "CURRENCY"                    => [ 'type' => 'string', 'value' => "RUB" ],
    "ID_AGENT"                    => [ 'type' => 'int', 'value' => "0" ],
    "ID_ARICUL_IBLOCK"            => [ 'type' => 'int', 'value' => "0" ],
];

$priceva_connector_default_option = array_combine(array_keys($DEFAULT_VALUES), array_column($DEFAULT_VALUES, 'value'));