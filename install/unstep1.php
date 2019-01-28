<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.01.2019
 * Time: 15:13
 */

use Bitrix\Main\Localization\Loc;

if( !check_bitrix_sessid() ) return;

Loc::loadMessages(__FILE__);

CAdminMessage::ShowNote(Loc::getMessage("PRICEVA_BC_UNINSTALL_DELETE")); ?>

<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <?php CAdminMessage::ShowMessage(Loc::getMessage("PRICEVA_BC_UNINSTALL_DELETE_1")) ?>
    <div>
        <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
    </div>
</form>
