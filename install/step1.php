<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.01.2019
 * Time: 15:12
 */

use Bitrix\Main\Localization\Loc;

if( !check_bitrix_sessid() ) return;

?>

<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP1_TEXT_1")?></p>
    <div>
        <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
    </div>
</form>
