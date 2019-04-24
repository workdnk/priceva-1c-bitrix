<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 17:07
 */

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

$MODULE_ID = 'priceva.connector';

try{
    Loader::registerAutoLoadClasses($MODULE_ID, [
        "\\Priceva\\Connector\\Bitrix\\Ajax"                    => "lib/bitrix/ajax.php",
        "\\Priceva\\Connector\\Bitrix\\Options"                 => "lib/bitrix/options.php",
        "\\Priceva\\Connector\\Bitrix\\Helpers\\CommonHelpers"  => "lib/bitrix/helpers/commonhelpers.php",
        "\\Priceva\\Connector\\Bitrix\\Helpers\\OptionsHelpers" => "lib/bitrix/helpers/optionshelpers.php",
        "\\Priceva\\Connector\\Bitrix\\PricevaModuleException"  => "lib/bitrix/pricevamoduleexception.php",
    ]);

    $arJsConfig = [
        $MODULE_ID => [
            'js'  => "/bitrix/js/$MODULE_ID/script.js",
            'css' => false,
            'rel' => false,
        ],
    ];

    foreach( $arJsConfig as $ext => $arExt ){
        CJSCore::RegisterExt($ext, $arExt);
    }
}catch( LoaderException $e ){
    error_log($e);
}