<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 25.01.2019
 * Time: 20:19
 */

namespace Priceva\Connector\Bitrix\Helpers;


use Bitrix\Main\Localization\Loc;

class OptionsHelpers
{
    private static $instance;

    /**
     * @return \Priceva\Connector\Bitrix\Helpers\OptionsHelpers
     */
    public static function getInstance()
    {
        if( null === static::$instance ){
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return bool
     */
    public static function is_save_method()
    {
        global $Update, $Apply, $RestoreDefaults;

        return isset($Update) || isset($Apply) || isset($RestoreDefaults);
    }

    public static function is_restore_method()
    {
        global $RestoreDefaults;

        return isset($RestoreDefaults);
    }

    /**
     * @return bool|string|null
     */
    public static function get_agent_id()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'AGENT_ID');
    }

    /**
     * @return bool|string|null
     */
    public static function get_api_key()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'API_KEY');
    }

    /**
     * @return bool|string|null
     */
    public static function get_type_price_ID()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'ID_TYPE_PRICE');
    }

    /**
     * @return bool|string|null
     */
    public static function get_sync_dominance()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_DOMINANCE');
    }

    /**
     * @return bool|string|null
     */
    public static function get_sync_field()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_FIELD');
    }

    /**
     * @return bool|string|null
     */
    public static function get_currency()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'CURRENCY');
    }

    /**
     * @return bool
     */
    public static function get_price_recalc()
    {
        return CommonHelpers::convert_to_bool(\COption::GetOptionString(CommonHelpers::MODULE_ID, 'PRICE_RECALC'));
    }

    /**
     * @return bool
     */
    public static function get_sync_only_active()
    {
        return CommonHelpers::convert_to_bool(\COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_ONLY_ACTIVE'));
    }

    /**
     * @return bool|string|null
     */
    public static function get_download_at_time()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'DOWNLOAD_AT_TIME');
    }

    /**
     * @return bool|string|null
     */
    public static function get_client_code()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'CLIENT_CODE');
    }

    /**
     * @return bool
     */
    public static function get_trade_offers()
    {
        return CommonHelpers::convert_to_bool(\COption::GetOptionString(CommonHelpers::MODULE_ID, 'TRADE_OFFERS'));
    }

    /**
     * @return bool
     */
    public static function get_debug()
    {
        return CommonHelpers::convert_to_bool(\COption::GetOptionString(CommonHelpers::MODULE_ID, 'DEBUG'));
    }

    /**
     * @return bool
     */
    public static function type_price_is_base()
    {
        $type_price = \CCatalogGroup::GetByID(static::get_type_price_ID());

        if( false === $type_price ){
            return false;
        }else{
            return $type_price[ 'BASE' ] === 'Y';
        }
    }

    /**
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function generate_options_tabs()
    {
        return [
            [
                "DIV"     => "index",
                "TAB"     => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_MAIN"),
                "ICON"    => "testmodule_settings",
                "TITLE"   => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_MAIN_TITLE"),
                "OPTIONS" => self::get_main_options(),
            ],
            [
                "DIV"     => "rights",
                "TAB"     => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_RIGHTS"),
                "ICON"    => "",
                "TITLE"   => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_OPTIONS_RIGHTS"),
                "OPTIONS" => [],
            ],
        ];
    }

    /**
     * @param array $filter
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function get_main_options( $filter = [] )
    {
        $types_of_price = CommonHelpers::get_types_of_price();
        $currencies     = CommonHelpers::get_currencies();

        // $agent_id       = OptionsHelpers::get_agent_id();

        $options = [
            "HEADING0"         => [ Loc::getMessage("PRICEVA_BC_OPTIONS_HEADING_MAIN_PARAMS"), "heading" ],
            "API_KEY"          => [ Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_APIKEY"), [ "text", 32 ] ],
            "DEBUG"            => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_DEBUG"), [
                    "select", CommonHelpers::add_not_selected([
                        "YES" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_ON"),
                        "NO"  => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_OFF"),
                    ]),
                ],
            ],
            "HEADING1"         => [ Loc::getMessage("PRICEVA_BC_OPTIONS_HEADING_SYNC"), "heading" ],
            "SYNC_FIELD"       => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_SYNC_FIELD"), [
                    "select", CommonHelpers::add_not_selected([
                        "client_code" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_CLIENT_CODE"),
                        "articul"     => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_ARTICUL"),
                    ]),
                ],
            ],
            "CLIENT_CODE"      => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_CLIENT_CODE_ANALOG"), [
                    "select", CommonHelpers::add_not_selected([
                        "ID"   => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRODUCT_ID"),
                        "CODE" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRODUCT_CODE"),
                    ]),
                ],
            ],
            "SYNC_ONLY_ACTIVE" => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_ACTIVE_PRODUCT"), [
                    "select", CommonHelpers::add_not_selected([
                        "NO"  => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_NO"),
                        "YES" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_YES"),
                    ]),
                ],
            ],
            "SYNC_DOMINANCE"   => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_SYNC_DOMINANCE"), [
                    "select", CommonHelpers::add_not_selected([
                        "bitrix"  => "Bitrix",
                        "priceva" => "Priceva",
                    ]),
                ],
            ],
            "DOWNLOAD_AT_TIME" => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_DOWNLOAD_AT_TIME"), [
                    "select", CommonHelpers::add_not_selected([
                        "10"   => "10",
                        "100"  => "100",
                        "1000" => "1000",
                    ]),
                ],
            ],
            "HEADING2"         => [ Loc::getMessage("PRICEVA_BC_OPTIONS_HEADING_WORK_PRICE"), "heading" ],
            "ID_TYPE_PRICE"    => [ Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRICE_TYPE"), [ "select", CommonHelpers::add_not_selected($types_of_price) ] ],
            "PRICE_RECALC"     => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_PRICE_RECALC"), [
                    "select", CommonHelpers::add_not_selected([
                        "NO"  => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_NO"),
                        "YES" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_YES"),
                    ]),
                ],
            ],
            "CURRENCY"         => [ Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_CURRENCY"), [ "select", CommonHelpers::add_not_selected($currencies) ] ],
            "TRADE_OFFERS"     => [
                Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_TRADE_OFFERS"), [
                    "select", CommonHelpers::add_not_selected([
                        "NO"  => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_NO"),
                        "YES" => Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_YES"),
                    ]),
                ],
            ],
        ];

        return array_diff_key($options, array_flip($filter));
    }

    public static function generate_buttons()
    {
        ?>
        <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>"
               title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
        <input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>"
               title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
        <? if( strlen($_REQUEST[ "back_url_settings" ]) > 0 ): ?>
        <input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>"
               title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>"
               onclick="window.location='<? echo htmlspecialcharsbx(\CUtil::addslashes($_REQUEST[ "back_url_settings" ])) ?>'">
        <input type="hidden" name="back_url_settings"
               value="<?=htmlspecialcharsbx($_REQUEST[ "back_url_settings" ])?>">
    <? endif ?>
        <input type="submit" name="RestoreDefaults" title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
               OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
               value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
        <?
    }

    private static function generate_tr_heading( $name )
    {
        ?>
        <tr class="heading">
            <td colspan="2"><b><?=$name?></b></td>
        </tr>
        <?php
    }

    /**
     * @param $element
     * @param $element_text
     * @param $option_name
     * @param $option_val
     */
    private static function generate_tr( $element, $element_text, $option_name, $option_val )
    {
        ?>
        <tr>
            <td class="adm-detail-content-cell-l"
                width="40%" <? if( $element[ 0 ] == "textarea" ) echo 'class="adm-detail-valign-top"' ?>>
                <label for="<?=htmlspecialcharsbx($option_name)?>"><?=$element_text?>:</label>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <?php
                switch( $element[ 0 ] ){
                    case 'textarea':
                        {
                            ?>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <textarea rows="<?
                            echo $element[ 1 ] ?>" name="<?
                            echo htmlspecialcharsbx($option_name) ?>" style="width:100%"><?
                                echo htmlspecialcharsbx($option_val) ?></textarea>
                            <?
                            break;
                        }
                    case 'checkbox':
                        {
                            ?>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input type="checkbox"
                                   name="<?=htmlspecialcharsbx($option_name)?>"
                                   id="<?=htmlspecialcharsbx($option_name)?>"
                                   value="Y"
                                <? if( $option_val == "Y" ) echo " checked"; ?>
                            >
                            <?
                            break;
                        }
                    case 'text':
                        {
                            ?>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input type="text"
                                   size="<?=$element[ 1 ]?>"
                                   maxlength="255" value="<?=htmlspecialcharsbx($option_val)?>"
                                   name="<?=htmlspecialcharsbx($option_name)?>"
                            >
                            <?
                            break;
                        }
                    case 'select':
                        {
                            if( count($element[ 1 ]) ){
                                ?>
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <select name="<?=htmlspecialcharsbx($option_name)?>">
                                    <?
                                    foreach( $element[ 1 ] as $key => $value ){
                                        ?>
                                        <option
                                                value="<?=htmlspecialcharsbx($key)?>"
                                            <? if( $option_val == $key ) echo 'selected="selected"' ?>
                                        >
                                            <?=htmlspecialcharsEx($value)?>
                                        </option>
                                        <?
                                    } ?>
                                </select>
                                <?
                            }
                            break;
                        }
                    case 'note':
                        {
                            echo BeginNote(), $element[ 1 ], EndNote();
                            break;
                        }
                } ?>
            </td>
        </tr>
        <?php
    }

    /**
     * @param $aTab
     * @param $bVarsFromForm
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function generate_table( $aTab, $bVarsFromForm )
    {
        foreach( $aTab[ "OPTIONS" ] as $option_name => $option ){
            if( $bVarsFromForm ){
                $option_val = $_POST[ $option_name ];
            }else{
                $option_val = \Bitrix\Main\Config\Option::get(CommonHelpers::MODULE_ID, $option_name);
            }
            $element_text = $option[ 0 ];
            $element      = $option[ 1 ];

            if( $element === 'heading' ){
                static::generate_tr_heading($element_text);
            }else{
                static::generate_tr($element, $element_text, $option_name, $option_val);
            }
        }
    }

    /**
     * @param bool  $bVarsFromForm
     * @param array $aTabs
     */
    public static function process_save_form( $bVarsFromForm, $aTabs )
    {
        if( OptionsHelpers::is_restore_method() ) // если было выбрано "по умолчанию", то сбрасывает все option'ы
        {
            \COption::RemoveOption(CommonHelpers::MODULE_ID);
        }else{
            // только если у нас не восстановление параметров, запишем текущие значения в БД,
            // иначе мы сначала удалим значения опций (если без else просто добавить код ниже к коду выше),
            // и потом их сразу же ставим нулевые все, код ниже не возьмет значения по умолчанию
            if( !$bVarsFromForm ){
                foreach( $aTabs as $i => $aTab ){
                    foreach( $aTab[ "OPTIONS" ] as $name => $arOption ){
                        $disabled = array_key_exists("disabled", $arOption) ? $arOption[ "disabled" ] : "";
                        if( $disabled )
                            continue;

                        $request = CommonHelpers::getInstance()->app->getContext()->getRequest();
                        $val     = $request->get($name);
                        if( $arOption[ 1 ][ 0 ] == "checkbox" && $val != "Y" ){
                            $val = "N";
                        }

                        \COption::SetOptionString(CommonHelpers::MODULE_ID, $name, $val, $arOption[ 0 ]);
                    }
                }
            }
        }
    }

    public static function generate_js_script( $filter = [] )
    {
        $functions = [
            'showDownloadsIfPriceva'           => "function showDownloadsIfPriceva() {
                var form = BX('options');
                var select_SYNC_DOMINANCE = BX.findChildren(form, {
                    tag: 'select',
                    attribute: {name: 'SYNC_DOMINANCE'}
                }, true);
                BX.bind(select_SYNC_DOMINANCE[0], 'bxchange', check_showDownloadsIfPriceva)
            }",
            'check_showDownloadsIfPriceva'     => "function check_showDownloadsIfPriceva() {
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

                if (d.value === 'priceva') {
                    BX.adjust(s, {props: {disabled: false}});
                } else {
                    BX.adjust(s, {props: {disabled: true}});
                    s.value = 0;
                }
            }",
            'showClientCodeIfClientCode'       => "function showClientCodeIfClientCode() {
                var form = BX('options');
                var select_SYNC_FIELD = BX.findChildren(form, {
                    tag: 'select',
                    attribute: {name: 'SYNC_FIELD'}
                }, true);
                BX.bind(select_SYNC_FIELD[0], 'bxchange', check_showClientCodeIfClientCode)
            }",
            'check_showClientCodeIfClientCode' => "function check_showClientCodeIfClientCode() {
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

                if (d.value === 'client_code') {
                    BX.adjust(s, {props: {disabled: false}});
                } else {
                    BX.adjust(s, {props: {disabled: true}});
                    s.value = 0;
                }
            }",
        ];

        $needed = array_diff_key($functions, array_flip($filter));

        $script = '';

        foreach( $needed as $func_name => $function ){
            $script .= $function . " BX.ready($func_name);\n";
        }

        return $script;
    }

    public static function get_default_options()
    {
        return [
            "API_KEY"          => "",
            "ID_TYPE_PRICE"    => "0",
            "SYNC_FIELD"       => "articul",
            "CLIENT_CODE"      => "ID",
            "SYNC_DOMINANCE"   => "priceva",
            "SYNC_ONLY_ACTIVE" => "YES",
            "DOWNLOAD_AT_TIME" => "1000",
            "PRICE_RECALC"     => "NO",
            "CURRENCY"         => "RUB",
            "ID_AGENT"         => "",
            "ID_ARICUL_IBLOCK" => "",
            "TRADE_OFFERS"     => "NO",
            "DEBUG"            => "NO",
        ];
    }
}