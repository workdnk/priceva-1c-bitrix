<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 12:48
 */

use Bitrix\Main\Localization\Loc;

if( !check_bitrix_sessid() ) return;

Loc::loadMessages(__FILE__);

/**
 * @var $Module priceva_connector
 */
$errors = $Module->get_errors();

$errors_str = '';

foreach( $errors as $error ){
    $errors_str .= "<li>" . $error . "</li>";
}

if( $errors_str ){
    $errors_str = Loc::getMessage("PRICEVA_BC_ERRORS_TEMPLATE_LIST_ERRORS") . $errors_str . "</ul>";
}

( new CAdminMessage(Loc::getMessage("PRICEVA_BC_INSTALL_ERRORS_TITLE")) )->Show(); ?>

<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <?=$errors_str?>
    <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
</form>