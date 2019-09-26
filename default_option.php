<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 14:42
 */

use Priceva\Connector\Bitrix\Options;


$MODULE_ID = 'priceva.connector';


try{
    if( IsModuleInstalled($MODULE_ID) ){
        $result = CModule::IncludeModule($MODULE_ID);

        if( $result ){
            $priceva_connector_default_option = Options::default_options();
        }else{
            throw new Exception('priceva.connector: Cannt load default options, module not included.');
        }
    }else{
        throw new Exception('priceva.connector: Cannt load default options, module not installed');
    }
}catch( Exception $e ){
    global $APPLICATION;

    $APPLICATION->ThrowException($e->getMessage());
}
