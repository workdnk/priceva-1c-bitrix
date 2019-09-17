<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 14:55
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\CommonHelpers;
use Priceva\Connector\Bitrix\PricevaConnector;

$MODULE_ID = "priceva.connector";

require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/include/prolog_admin.php" );
require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/" . $MODULE_ID . "/prolog.php" );

CModule::IncludeModule($MODULE_ID);

Loc::loadMessages(__FILE__);

$common_helpers = CommonHelpers::getInstance();

$FORM_RIGHT = $common_helpers->APPLICATION->GetGroupRight($MODULE_ID);
if( $FORM_RIGHT <= "D" ) $common_helpers->APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/include/prolog_admin_after.php" );

$info = null;

if( "POST" === $common_helpers->request_method() ){
    if( $common_helpers->app->getContext()->getRequest()->getPost('run') ){
        $connector = new PricevaConnector();
        $connector->run();
        $info = $connector->get_last_info_msg();
    }
}
function priceva_get_info( $infos )
{
    $res = "<ul>";
    foreach( $infos as $info ){
        $res .= "<li>" . $info . "</li>";
    }
    $res .= "</ul>";

    return $res;
}

if( $info ){ ?>
    <div id='info'>
        <p style="color: red">
            <b><?=Loc::getMessage("PRICEVA_BC_INFO_ERRORS")?></b><br/><?=priceva_get_info($info[ 'errors' ])?></p>
        <p style="color: orange">
            <b><?=Loc::getMessage("PRICEVA_BC_INFO_WARN")?></b><br/><?=priceva_get_info($info[ 'warnings' ])?></p>
        <p style="color: green">
            <b><?=Loc::getMessage("PRICEVA_BC_INFO_SUCCESS")?></b><br/><?=priceva_get_info($info[ 'success' ])?></p>
    </div>
<?php } ?>
    <form method="post" action="<?=$common_helpers->APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>"
          id="priceva_bc">
        <input type="submit" name="run" value="<?=Loc::getMessage("PRICEVA_BC_ADMIN_PAGE_RUN")?>"
               title="<?=Loc::getMessage("PRICEVA_BC_ADMIN_PAGE_RUN")?>">
    </form>

<?php
require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/include/epilog_admin.php" );
?>