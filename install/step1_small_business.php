<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.01.2019
 * Time: 15:12
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\CommonHelpers;
use Priceva\Connector\Bitrix\OptionsPage;

if( !check_bitrix_sessid() ) return;

Loc::LoadMessages($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/options.php");
Loc::LoadMessages($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/" . CommonHelpers::MODULE_ID . "/options.php");

$filter_js      = [ 'loadTypesInfoblocks', 'check_loadTypesInfoblocks', 'showCatalogsIfOneCatalog', 'check_showCatalogsIfOneCatalog' ];

try{
    $aTab = OptionsPage::get_main_options(true);
    ?>
    <form action="<? echo $APPLICATION->GetCurPage(); ?>" id="options">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="id" value="priceva.connector">
        <input type="hidden" name="install" value="Y">
        <input type="hidden" name="step" value="2">
        <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP1_TEXT_1")?></p>
        <table>
            <?php OptionsPage::generate_table([ 'OPTIONS' => $aTab ], $bVarsFromForm); ?>
        </table>
        <div>
            <input type="submit" name="inst" value="<? echo Loc::getMessage("MOD_INSTALL"); ?>">
        </div>
    </form>
    <script>
        <?php echo OptionsPage::generate_js_script($filter_js); ?>
    </script>
    <?
}catch( Throwable $e ){
    CommonHelpers::write_to_log($e);
} ?>