<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 29.01.2019
 * Time: 11:40
 */

if( IsModuleInstalled('{MODULE_ID}') ){

    $updater = new CUpdater();

    if( is_dir(dirname(__FILE__) . '/install/admin') ){
        $updater->CopyFiles("install/admin", "admin");
    }
    if( is_dir(dirname(__FILE__) . '/install/module') ){
        $updater->CopyFiles("install/module", "");
    }
}
