<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 29.01.2019
 * Time: 11:40
 */

$module_id = 'priceva.connector';

if( IsModuleInstalled($module_id) ){

    if( is_dir(dirname(__FILE__) . '/install/admin') ){
        /**
         * @var CUpdater $updater
         */
        $updater->CopyFiles("install/admin", "admin");
    }

    if( is_dir(dirname(__FILE__) . '/assets/js') ){

        $bitrix_root = $_SERVER[ "DOCUMENT_ROOT" ] . $updater->kernelPath;

        if( !is_dir($bitrix_root . "/js/$module_id") ){
            mkdir($bitrix_root . "/js/$module_id", 0755);
        }

        $updater->CopyFiles("assets/js", "js/$module_id");
    }

    $trade_offers = COption::GetOptionString($module_id, 'TRADE_OFFERS');

    if( $trade_offers ){
        COption::RemoveOption($module_id, 'TRADE_OFFERS');
        COption::SetOptionString($module_id, 'TRADE_OFFERS_ENABLE', $trade_offers);
    }
}
