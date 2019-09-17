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

    <form action="<?=$APPLICATION->GetCurPage();?>">
        <input type="hidden" name="lang" value="<?=LANG?>">
        <?php
        echo ( new CAdminMessage([
            "MESSAGE" => Loc::getMessage("PRICEVA_BC_UNINSTALL_DELETE"),
            "TYPE"    => "OK",
        ]) )->Show();
        /**
         * @noinspection PhpUndefinedClassInspection
         * @var priceva_connector $Module
         */
        $deleted_price         = $Module->get_info('deleted_price');
        $deleted_price_priceva = $Module->get_info('deleted_price_priceva');
        if( $deleted_price ){
            echo ( new CAdminMessage($deleted_price) )->Show();
        }
        if( $deleted_price_priceva ){
            echo ( new CAdminMessage($deleted_price_priceva) )->Show();
        } ?>
        <div>
            <input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK");?>">
        </div>
    </form>

<?php
ModuleManager::unRegisterModule($Module->get_info('module_id'));
