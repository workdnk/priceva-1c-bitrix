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
 * @var $Module \priceva_bitrix_connector
 */
$errors = $Module->get_errors();

CAdminMessage::ShowOldStyleError(Loc::getMessage("PRICEVA_BC_INSTALL_ERRORS_TITLE")); ?>

<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>">
    <?="<pre>" . print_r($errors, true) . "</pre>";?><br/>
    <input type="submit" name="" value="<? echo Loc::getMessage("MOD_BACK"); ?>">
</form>