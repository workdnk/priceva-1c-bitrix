<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.02.2019
 * Time: 14:01
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\{CommonHelpers, OptionsHelpers};
use Priceva\Connector\Bitrix\Options;

if( !check_bitrix_sessid() ) return;

$price_type = Options::type_price_ID();
$base       = OptionsHelpers::type_price_is_base();
$different  = Options::type_price_ID() !== Options::type_price_priceva_ID();

try{
    ?>
    <form action="<? echo $APPLICATION->GetCurPage(); ?>" id="options">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="id" value="priceva.connector">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
        <table>
            <!-- delete options -->
            <tr>
                <td><label for="options"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_TEXT2")?></label></td>
                <td><select name="options" id="options">
                        <option value="NO"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_NOTING")?></option>
                        <option value="YES"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_DELETE")?></option>
                    </select></td>
            </tr>
            <?php
            if( !$base && !empty($price_type) ){ ?>
                <!-- delete selected price type -->
                <tr>
                    <td><label for="type_price"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_TEXT1")?></label></td>
                    <td><select name="type_price" id="type_price">
                            <option value="NO"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_NOTING")?></option>
                            <option value="YES"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_DELETE")?></option>
                        </select></td>
                </tr>
                <?php
            }
            if( $different ){ ?>
                <!-- delete created price type -->
                <tr>
                    <td><label for="price_type_priceva"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_TEXT3")?></label>
                    </td>
                    <td><select name="price_type_priceva" id="price_type_priceva">
                            <option value="NO"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_NOTING")?></option>
                            <option value="YES"><?=Loc::getMessage("PRICEVA_BC_UNINSTALL_STEP1_DELETE")?></option>
                        </select></td>
                </tr>
            <?php } ?>
        </table>
        <div>
            <input type="submit" name="inst" value="<? echo Loc::getMessage("MOD_UNINST_DEL"); ?>">
        </div>
    </form>
    <?
}catch( Throwable $e ){
    CommonHelpers::write_to_log($e);
} ?>