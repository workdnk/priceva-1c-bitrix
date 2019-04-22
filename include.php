<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 17:07
 */

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

try{
    Loader::registerAutoLoadClasses('priceva.connector', [
        "\\Priceva\\Connector\\Bitrix\\Helpers\\CommonHelpers" => "lib/bitrix/helpers/commonhelpers.php",
    ]);
    Loader::registerAutoLoadClasses('priceva.connector', [
        "\\Priceva\\Connector\\Bitrix\\Helpers\\OptionsHelpers" => "lib/bitrix/helpers/optionshelpers.php",
    ]);
    Loader::registerAutoLoadClasses('priceva.connector', [
        "\\Priceva\\Connector\\Bitrix\\PricevaModuleException" => "lib/bitrix/pricevamoduleexception.php",
    ]);
    Loader::registerAutoLoadClasses('priceva.connector', [
        "\\Priceva\\Connector\\Bitrix\\Ajax" => "lib/bitrix/ajax.php",
    ]);
}catch( LoaderException $e ){
    error_log($e);
}