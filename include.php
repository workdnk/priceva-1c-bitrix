<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 17:07
 */

try{
    \Bitrix\Main\Loader::registerAutoLoadClasses('priceva.connector', [
        "\\Priceva\\Connector\\Bitrix\\Helpers\\CommonHelpers" => "lib/bitrix/helpers/commonhelpers.php",
    ]);
    \Bitrix\Main\Loader::registerAutoLoadClasses('priceva.connector', [
        "\\Priceva\\Connector\\Bitrix\\Helpers\\OptionsHelpers" => "lib/bitrix/helpers/optionshelpers.php",
    ]);
}catch( \Bitrix\Main\LoaderException $e ){
    error_log($e);
}