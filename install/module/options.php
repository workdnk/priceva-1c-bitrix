<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 14:41
 */

use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\{CommonHelpers, OptionsHelpers};

try{
    global $APPLICATION, $Update, $Apply;

    $MODULE_ID = "priceva.connector";
    $res       = CModule::IncludeModule($MODULE_ID);

    Loc::LoadMessages($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/options.php");
    Loc::loadMessages(__FILE__);
    $common_helpers = CommonHelpers::getInstance();

    $RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);

    if( $RIGHT >= "R" ){
        $bVarsFromForm = false; // пришли ли данные с формы

        $types_of_price = $common_helpers::get_types_of_price();
        $currencies     = $common_helpers::get_currencies();
        $agent_id       = OptionsHelpers::get_agent_id();

        // массив вкладок, свойств
        $aTabs      = [
            [
                "DIV"     => "index",
                "TAB"     => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_MAIN"),
                "ICON"    => "testmodule_settings",
                "TITLE"   => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_MEIN_TITLE"),
                "OPTIONS" => [
                    "API_KEY"          => [ Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_APIKEY"), [ "text", 32 ] ],
                    "ID_TYPE_PRICE"    => [ Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRICE_TYPE"), [ "select", $common_helpers->add_not_selected($types_of_price) ] ],
                    "SYNC_FIELD"       => [
                        Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_SYNC_FIELD"), [
                            "select", $common_helpers->add_not_selected([
                                "client_code" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_CLIENT_CODE"),
                                "articul"     => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_ARTICUL"),
                            ]),
                        ],
                    ],
                    "CLIENT_CODE"      => [
                        Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_CLIENT_CODE_ANALOG"), [
                            "select", $common_helpers->add_not_selected([
                                "ID"   => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRODUCT_ID"),
                                "CODE" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRODUCT_CODE"),
                            ]),
                        ],
                    ],
                    "SYNC_ONLY_ACTIVE" => [
                        Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_ACTIVE_PRODUCT"), [
                            "select", $common_helpers->add_not_selected([
                                "NO"  => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_NO"),
                                "YES" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_YES"),
                            ]),
                        ],
                    ],
                    "SYNC_DOMINANCE"   => [
                        Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_SYNC_DOMINANCE"), [
                            "select", $common_helpers->add_not_selected([
                                "bitrix"  => "Bitrix",
                                "priceva" => "Priceva",
                            ]),
                        ],
                    ],
                    "DOWNLOAD_AT_TIME" => [
                        Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_DOWNLOAD_AT_TIME"), [
                            "select", $common_helpers->add_not_selected([
                                "10"   => "10",
                                "100"  => "100",
                                "1000" => "1000",
                            ]),
                        ],
                    ],
                    "PRICE_RECALC"     => [
                        Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRICE_RECALC"), [
                            "select", $common_helpers->add_not_selected([
                                "NO"  => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_NO"),
                                "YES" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_YES"),
                            ]),
                        ],
                    ],
                    "CURRENCY"         => [ Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_CURRENCY"), [ "select", $common_helpers->add_not_selected($currencies) ] ],
                ],
            ],
            [
                "DIV"     => "rights",
                "TAB"     => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_RIGHTS"),
                "ICON"    => "",
                "TITLE"   => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_OPTIONS_RIGHTS"),
                "OPTIONS" => [],
            ],
        ];
        $tabControl = new CAdminTabControl("tabControl", $aTabs);

        if(
            "POST" === $common_helpers->request_method() &&
            OptionsHelpers::is_save_method() &&
            check_bitrix_sessid()
        ){
            if( OptionsHelpers::is_restore_method() ) // если было выбрано "по умолчанию", то сбрасывает все option'ы
            {
                COption::RemoveOption($MODULE_ID);
            }else{
                // только если у нас не восстановление параметров, запишем текущие значения в БД,
                // иначе мы сначала удаляем значения опций, и потом их сразу же ставим нулевые все,
                // код ниже не возьмет значения по умолчанию
                if( !$bVarsFromForm ){
                    foreach( $aTabs as $i => $aTab ){
                        foreach( $aTab[ "OPTIONS" ] as $name => $arOption ){
                            $disabled = array_key_exists("disabled", $arOption) ? $arOption[ "disabled" ] : "";
                            if( $disabled )
                                continue;

                            $val = $_POST[ $name ];
                            if( $arOption[ 1 ][ 0 ] == "checkbox" && $val != "Y" ){
                                $val = "N";
                            }

                            COption::SetOptionString($MODULE_ID, $name, $val, $arOption[ 0 ]);
                        }
                    }
                }
            }

            ob_start();
            $Update = $Update . $Apply;
            require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/admin/group_rights.php" );
            ob_end_clean();
        }

        $tabControl->Begin();
        ?>
        <form method="post"
              action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"
              id="options">
            <?
            foreach( $aTabs as $caTab => $aTab ){
                $tabControl->BeginNextTab();
                if( $aTab[ "DIV" ] != "rights" ){ // не особаЯ (обычнаЯ) вкладка
                    foreach( $aTab[ "OPTIONS" ] as $name => $arOption ){
                        if( $bVarsFromForm ){
                            $val = $_POST[ $name ];
                        }else{
                            $val = \Bitrix\Main\Config\Option::get($MODULE_ID, $name);
                        }
                        $type     = $arOption[ 1 ];
                        $disabled = array_key_exists("disabled", $arOption) ? $arOption[ "disabled" ] : "";
                        ?>
                        <tr <?
                        if( isset($arOption[ 2 ]) && strlen($arOption[ 2 ]) ) echo 'style="display:none" class="show-for-' . htmlspecialcharsbx($arOption[ 2 ]) . '"' ?>>
                            <td width="40%" <?
                            if( $type[ 0 ] == "textarea" ) echo 'class="adm-detail-valign-top"' ?>>
                                <label for="<?
                                echo htmlspecialcharsbx($name) ?>"><?
                                    echo $arOption[ 0 ] ?>:</label>
                            <td width="30%">
                                <?
                                if( $type[ 0 ] == "checkbox" ){
                                    ?>
                                    <!--suppress HtmlFormInputWithoutLabel -->
                                    <input type="checkbox" name="<?
                                    echo htmlspecialcharsbx($name) ?>" id="<?
                                    echo htmlspecialcharsbx($name) ?>" value="Y"<?
                                    if( $val == "Y" ) echo " checked"; ?><?
                                    if( $disabled ) echo ' disabled="disabled"'; ?>><?
                                    if( $disabled ) echo '<br>' . $disabled; ?>
                                    <?
                                }elseif( $type[ 0 ] == "text" ){
                                    ?>
                                    <!--suppress HtmlFormInputWithoutLabel -->
                                    <input type="text" size="<?
                                    echo $type[ 1 ] ?>" maxlength="255" value="<?
                                    echo htmlspecialcharsbx($val) ?>" name="<?
                                    echo htmlspecialcharsbx($name) ?>">
                                    <?
                                }elseif( $type[ 0 ] == "textarea" ){
                                    ?>
                                    <!--suppress HtmlFormInputWithoutLabel -->
                                    <textarea rows="<?
                                    echo $type[ 1 ] ?>" name="<?
                                    echo htmlspecialcharsbx($name) ?>" style=
                                              "width:100%"><?
                                        echo htmlspecialcharsbx($val) ?></textarea>
                                    <?
                                }elseif( $type[ 0 ] == "select" ){
                                    ?>
                                    <?
                                    if( count($type[ 1 ]) ){
                                        ?>
                                        <!--suppress HtmlFormInputWithoutLabel -->
                                        <select name="<?
                                        echo htmlspecialcharsbx($name) ?>" onchange="doShowAndHide()">
                                            <?
                                            foreach( $type[ 1 ] as $key => $value ){
                                                ?>
                                                <option value="<?
                                                echo htmlspecialcharsbx($key) ?>" <?
                                                if( $val == $key ) echo 'selected="selected"' ?>><?
                                                    echo htmlspecialcharsEx($value) ?></option>
                                                <?
                                            } ?>
                                        </select>
                                        <?
                                    }else{
                                        ?>
                                        <?
                                        echo GetMessage("ZERO_ELEMENT_ERROR"); ?>
                                        <?
                                    } ?>
                                    <?
                                }elseif( $type[ 0 ] == "note" ){
                                    ?>
                                    <?
                                    echo BeginNote(), $type[ 1 ], EndNote(); ?>
                                    <?
                                } ?>
                            </td>
                            <td width="30%">
                                <?
                                if( $arOption[ 3 ] ){
                                    ?>
                                    <p><?
                                        echo $arOption[ 3 ]; ?></p>
                                    <?
                                } ?>
                            </td>
                        </tr>
                        <?
                    }
                }elseif( $aTab[ "DIV" ] == "rights" ){
                    require( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/admin/group_rights.php" );
                }
            } ?>

            <? $tabControl->Buttons(); ?>
            <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>"
                   title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
            <input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>"
                   title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
            <? if( strlen($_REQUEST[ "back_url_settings" ]) > 0 ): ?>
                <input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>"
                       title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>"
                       onclick="window.location='<? echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST[ "back_url_settings" ])) ?>'">
                <input type="hidden" name="back_url_settings"
                       value="<?=htmlspecialcharsbx($_REQUEST[ "back_url_settings" ])?>">
            <? endif ?>
            <input type="submit" name="RestoreDefaults" title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
                   OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
                   value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
            <?=bitrix_sessid_post();?>
            <? $tabControl->End(); ?>
        </form>
        <script>
            function doShowAndHide() {
                var form = BX('options');
                var selects = BX.findChildren(form, {tag: 'select'}, true);
                for (var i = 0; i < selects.length; i++) {
                    var selectedValue = selects[i].value;
                    var trs = BX.findChildren(form, {tag: 'tr'}, true);
                    for (var j = 0; j < trs.length; j++) {
                        if (/show-for-/.test(trs[j].className)) {
                            if (trs[j].className.indexOf(selectedValue) >= 0)
                                trs[j].style.display = 'table-row';
                            else
                                trs[j].style.display = 'none';
                        }
                    }
                }
            }

            function showDownloadsIfPriceva() {
                var form = BX('options');
                var select_SYNC_DOMINANCE = BX.findChildren(form, {
                    tag: 'select',
                    attribute: {name: 'SYNC_DOMINANCE'}
                }, true);
                BX.bind(select_SYNC_DOMINANCE[0], 'bxchange', check_showDownloadsIfPriceva)
            }

            function check_showDownloadsIfPriceva() {
                var form = BX('options'),
                    select_SYNC_DOMINANCE = BX.findChildren(form, {
                        tag: 'select',
                        attribute: {name: 'SYNC_DOMINANCE'}
                    }, true),
                    select_DOWNLOAD_AT_TIME = BX.findChildren(form, {
                        tag: 'select',
                        attribute: {name: 'DOWNLOAD_AT_TIME'}
                    }, true);

                var s = select_DOWNLOAD_AT_TIME[0],
                    d = select_SYNC_DOMINANCE[0];

                if (d.value === "priceva") {
                    BX.adjust(s, {props: {disabled: false}});
                } else {
                    BX.adjust(s, {props: {disabled: true}});
                    s.value = 0;
                }
            }

            function showClientCodeIfClientCode() {
                var form = BX('options');
                var select_SYNC_FIELD = BX.findChildren(form, {
                    tag: 'select',
                    attribute: {name: 'SYNC_FIELD'}
                }, true);
                BX.bind(select_SYNC_FIELD[0], 'bxchange', check_showClientCodeIfClientCode)
            }

            function check_showClientCodeIfClientCode() {
                var form = BX('options'),
                    select_SYNC_FIELD = BX.findChildren(form, {
                        tag: 'select',
                        attribute: {name: 'SYNC_FIELD'}
                    }, true),
                    select_CLIENT_CODE = BX.findChildren(form, {
                        tag: 'select',
                        attribute: {name: 'CLIENT_CODE'}
                    }, true);

                var s = select_CLIENT_CODE[0],
                    d = select_SYNC_FIELD[0];

                if (d.value === "client_code") {
                    BX.adjust(s, {props: {disabled: false}});
                } else {
                    BX.adjust(s, {props: {disabled: true}});
                    s.value = 0;
                }
            }

            BX.ready(doShowAndHide);
            BX.ready(showDownloadsIfPriceva);
            BX.ready(check_showDownloadsIfPriceva);
            BX.ready(showClientCodeIfClientCode);
            BX.ready(check_showClientCodeIfClientCode);
        </script>
    <? }
}catch( Exception $e ){
    error_log($e);
}