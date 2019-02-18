<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.02.2019
 * Time: 14:01
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\{OptionsHelpers};

if( !check_bitrix_sessid() ) return;

$base = OptionsHelpers::type_price_is_base();

try{
    ?>
    <form action="<? echo $APPLICATION->GetCurPage(); ?>" id="options">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="id" value="priceva.connector">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
        <table>
            <?php
            if( !$base ){ ?>
                <tr>
                    <td><label for="type_price"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_TEXT1")?></label></td>
                    <td><select name="type_price" id="type_price">
                            <option value="NO"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_NOTING")?></option>
                            <option value="YES"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_DELETE")?></option>
                        </select></td>
                </tr>
            <?php } ?>
            <tr>
                <td><label for="options"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_TEXT2")?></label></td>
                <td><select name="options" id="options">
                        <option value="NO"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_NOTING")?></option>
                        <option value="YES"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_DELETE")?></option>
                    </select></td>
            </tr>
        </table>
        <div>
            <input type="submit" name="inst" value="<? echo Loc::getMessage("MOD_UNINST_DEL"); ?>">
        </div>
    </form>
    <?
}catch( \Throwable $e ){
    \Priceva\Connector\Bitrix\Helpers\CommonHelpers::write_to_log($e);
} ?>