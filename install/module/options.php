<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 14:41
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\{CommonHelpers, OptionsHelpers};
use Priceva\Connector\Bitrix\PricevaOptions;

try{
    global $APPLICATION, $Update, $Apply;

    $MODULE_ID = "priceva.connector";

    CModule::IncludeModule($MODULE_ID);

    $priceva_options = new PricevaOptions();

    Loc::LoadMessages($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/options.php");
    Loc::loadMessages(__FILE__);

    $RIGHT = $priceva_options->common_helpers->APPLICATION->GetGroupRight($MODULE_ID);

    if( $RIGHT >= "R" ){
        $bVarsFromForm = false; // пришли ли данные с формы

        // массив вкладок, свойств
        $aTabs = $priceva_options->generate_options_tabs();

        $tabControl = new CAdminTabControl("tabControl", $aTabs);

        if(
            $priceva_options->common_helpers->is_post() &&
            OptionsHelpers::is_save_method() &&
            check_bitrix_sessid()
        ){
            $priceva_options->process_save_form($bVarsFromForm, $aTabs);

            ob_start();
            $Update = $Update . $Apply;
            require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/admin/group_rights.php" );
            ob_end_clean();
        }

        $tabControl->Begin();
        $form_action = $priceva_options->common_helpers->APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . LANGUAGE_ID;
        ?>
        <form method="post" action="<?=$form_action?>" id="options">
            <?
            foreach( $aTabs as $caTab => $aTab ){
                $tabControl->BeginNextTab();

                if( $aTab[ "DIV" ] != "rights" ){
                    $priceva_options->generate_table($aTab, $bVarsFromForm);
                }elseif( $aTab[ "DIV" ] == "rights" ){
                    require( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/admin/group_rights.php" );
                }
            }
            $tabControl->Buttons();
            $priceva_options->generate_buttons();
            echo bitrix_sessid_post();
            $tabControl->End(); ?>
        </form>
        <script>
            <?= $priceva_options->generate_js_script()?>
        </script>
    <? }
}catch( Exception $e ){
    CommonHelpers::write_to_log($e);
    CommonHelpers::getInstance()->APPLICATION->ThrowException($e->getMessage());
}