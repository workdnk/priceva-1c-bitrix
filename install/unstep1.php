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

echo ( new CAdminMessage([
    "MESSAGE" => Loc::getMessage("PRICEVA_BC_UNINSTALL_DELETE"),
    "TYPE"    => "OK",
]) )->Show(); ?>

<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <?php
    /**
     * @noinspection PhpUndefinedClassInspection
     * @var priceva_connector $Module
     */
    echo ( new CAdminMessage($Module->get_info('deleted_price')) )->Show() ?>
    <div>
        <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
    </div>
</form>

<?php
ModuleManager::unRegisterModule($Module->get_info('module_id'));
