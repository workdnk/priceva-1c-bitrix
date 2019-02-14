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

<form action="<? echo $APPLICATION->GetCurPage(); ?>" name="step1">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="id" value="priceva.connector">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">
    <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP1_TEXT_1")?></p>
    <p>Тут в дальнейшем будут настраиваемые при установке опции</p>
    <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP1_TEXT_2")?></p>
    <div>
        <input type="submit" name="inst" value="<? echo Loc::getMessage("MOD_INSTALL"); ?>">
    </div>
</form>
