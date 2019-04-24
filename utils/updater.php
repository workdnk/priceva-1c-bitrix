<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 29.01.2019
 * Time: 11:40
 */

$module_id = '{MODULE_ID}';

if( IsModuleInstalled($module_id) ){

    $updater = new CUpdater();

    if( is_dir(dirname(__FILE__) . '/install/admin') ){
        $updater->CopyFiles("install/admin", "admin");
    }
    if( is_dir(dirname(__FILE__) . '/install/module') ){
        $updater->CopyFiles("install/module", "");
    }

    $trade_offers = COption::GetOptionString($module_id, 'TRADE_OFFERS');

    if( $trade_offers ){
        COption::RemoveOption($module_id, 'TRADE_OFFERS');
        COption::SetOptionString($module_id, 'TRADE_OFFERS_ENABLE', $trade_offers);
    }
}
