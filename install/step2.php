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

<form action="<?=$APPLICATION->GetCurPage();?>">
    <?=bitrix_sessid_post()?>
    <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP2_TEXT1")?></p>
    <div>
        <input type="submit" name="inst" value="<?=Loc::getMessage("MOD_BACK");?>">
    </div>
</form>
