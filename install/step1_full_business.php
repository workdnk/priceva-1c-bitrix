<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 18.01.2019
 * Time: 15:12
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\{CommonHelpers, OptionsHelpers};

if( !check_bitrix_sessid() ) return;

Loc::LoadMessages($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/options.php");
Loc::LoadMessages($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/" . CommonHelpers::MODULE_ID . "/options.php");

$filter_options = [ 'DEBUG', 'HEADING0', 'HEADING1', 'IBLOCK_TYPE_ID', 'CATALOG_MODE', 'CATALOG_ID', 'HEADING2', 'HEADING3' ];
$filter_js      = [ 'loadTypesInfoblocks', 'check_loadTypesInfoblocks', 'showCatalogsIfOneCatalog', 'check_showCatalogsIfOneCatalog' ];

try{
    $aTab = OptionsHelpers::get_main_options($filter_options);
    ?>
    <form action="<? echo $APPLICATION->GetCurPage(); ?>" id="options">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="id" value="priceva.connector">
        <input type="hidden" name="install" value="Y">
        <input type="hidden" name="step" value="2">
        <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP1_TEXT_1")?></p>
        <table>
            <?php OptionsHelpers::generate_table([ 'OPTIONS' => $aTab ], $bVarsFromForm); ?>
        </table>
        <p><?=Loc::getMessage("PRICEVA_BC_INSTALL_STEP1_TEXT_2")?></p>
        <div>
            <input type="submit" name="inst" value="<? echo Loc::getMessage("MOD_INSTALL"); ?>">
        </div>
    </form>
    <script>
        <?php echo OptionsHelpers::generate_js_script($filter_js); ?>
    </script>
    <?
}catch( Throwable $e ){
    CommonHelpers::write_to_log($e);
} ?>