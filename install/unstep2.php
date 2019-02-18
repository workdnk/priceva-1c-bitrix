<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.01.2019
 * Time: 15:13
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

if( !check_bitrix_sessid() ) return;

Loc::loadMessages(__FILE__);
?>

    <form action="<? echo $APPLICATION->GetCurPage(); ?>">
        <input type="hidden" name="lang" value="<? echo LANG ?>">
        <?php
        echo ( new CAdminMessage([
            "MESSAGE" => Loc::getMessage("PRICEVA_BC_UNINSTALL_DELETE"),
            "TYPE"    => "OK",
        ]) )->Show();
        /**
         * @noinspection PhpUndefinedClassInspection
         * @var priceva_connector $Module
         */
        $deleted_price = $Module->get_info('deleted_price');
        if( $deleted_price ){
            echo ( new CAdminMessage($deleted_price) )->Show();
        } ?>
        <div>
            <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
        </div>
    </form>

<?php
ModuleManager::unRegisterModule($Module->get_info('module_id'));
