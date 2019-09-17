<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 24.04.2019
 * Time: 17:01
 */

namespace Priceva\Connector\Bitrix;


use COption;
use Priceva\Connector\Bitrix\Helpers\CommonHelpers;

/**
 * Class Options
 *
 * @package Priceva\Connector\Bitrix
 *
 * @property string API_KEY
 * @property bool   $DEBUG
 * @property bool   $SIMPLE_PRODUCT_ENABLE
 * @property string $IBLOCK_TYPE_ID
 * @property string $IBLOCK_MODE
 * @property int    $IBLOCK_ID
 * @property bool   $TRADE_OFFERS_ENABLE
 * @property string $TRADE_OFFERS_IBLOCK_TYPE_ID
 * @property string $TRADE_OFFERS_IBLOCK_MODE
 * @property int    $TRADE_OFFERS_IBLOCK_ID
 * @property string $SYNC_FIELD
 * @property string $CLIENT_CODE
 * @property string $SYNC_DOMINANCE
 * @property bool   $SYNC_ONLY_ACTIVE
 * @property int    $DOWNLOAD_AT_TIME
 * @property int    $ID_TYPE_PRICE
 * @property int    $ID_TYPE_PRICE_PRICEVA
 * @property bool   $PRICE_RECALC
 * @property string $CURRENCY
 * @property int    $ID_AGENT
 * @property int    $ID_ARICUL_IBLOCK
 */
class Options
{
    const DEFAULT_VALUES = [
        "API_KEY"                     => [ 'type' => 'string', 'value' => "" ],
        "DEBUG"                       => [ 'type' => 'bool', 'value' => "NO" ],
        "SIMPLE_PRODUCT_ENABLE"       => [ 'type' => 'bool', 'value' => "YES" ],
        "IBLOCK_TYPE_ID"              => [ 'type' => 'string', 'value' => "catalog" ],
        "IBLOCK_MODE"                 => [ 'type' => 'string', 'value' => "2" ],
        "IBLOCK_ID"                   => [ 'type' => 'int', 'value' => "0" ],
        "TRADE_OFFERS_ENABLE"         => [ 'type' => 'bool', 'value' => "NO" ],
        "TRADE_OFFERS_IBLOCK_TYPE_ID" => [ 'type' => 'string', 'value' => "0" ],
        "TRADE_OFFERS_IBLOCK_MODE"    => [ 'type' => 'string', 'value' => "0" ],
        "TRADE_OFFERS_IBLOCK_ID"      => [ 'type' => 'int', 'value' => "0" ],
        "SYNC_FIELD"                  => [ 'type' => 'string', 'value' => "articul" ],
        "CLIENT_CODE"                 => [ 'type' => 'string', 'value' => "ID" ],
        "SYNC_DOMINANCE"              => [ 'type' => 'string', 'value' => "priceva" ],
        "SYNC_ONLY_ACTIVE"            => [ 'type' => 'bool', 'value' => "YES" ],
        "DOWNLOAD_AT_TIME"            => [ 'type' => 'int', 'value' => "1000" ],
        "ID_TYPE_PRICE"               => [ 'type' => 'int', 'value' => "0" ],
        "ID_TYPE_PRICE_PRICEVA"       => [ 'type' => 'int', 'value' => "0" ],
        "PRICE_RECALC"                => [ 'type' => 'bool', 'value' => "NO" ],
        "CURRENCY"                    => [ 'type' => 'string', 'value' => "RUB" ],
        "ID_AGENT"                    => [ 'type' => 'int', 'value' => "0" ],
        "ID_ARICUL_IBLOCK"            => [ 'type' => 'int', 'value' => "0" ],
    ];

    private static $instance = null;

    /**
     * @return Options
     */
    public static function getInstance()
    {
        if( null === static::$instance ){
            static::$instance = new Options();
        }

        return static::$instance;
    }

    /**
     * @return array
     */
    public static function default_options()
    {
        return array_combine(array_keys(self::DEFAULT_VALUES), array_column(self::DEFAULT_VALUES, 'value'));
    }

    /**
     * @param string $name
     *
     * @return bool|string|int|null
     * @throws PricevaModuleException
     */
    public function __get( $name )
    {
        if( key_exists($name, self::DEFAULT_VALUES) ){
            switch( self::DEFAULT_VALUES[ $name ][ 'type' ] ){
                case 'int':
                    {
                        return COption::GetOptionInt(CommonHelpers::MODULE_ID, $name);
                        break;
                    }
                case 'bool':
                    {
                        return CommonHelpers::convert_to_bool(COption::GetOptionString(CommonHelpers::MODULE_ID, $name));

                        break;
                    }
                case 'string':
                    {
                        return COption::GetOptionString(CommonHelpers::MODULE_ID, $name);

                        break;
                    }
                default:
                    {
                        $type = self::DEFAULT_VALUES[ $name ][ 'type' ];
                        throw new PricevaModuleException("Module dont have option type '$type'.");

                        break;
                    }
            }
        }else{
            throw new PricevaModuleException("Module dont have option '$name'.");
        }
    }

    /**
     * @return bool|string|null
     */
    public static function agent_id()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'AGENT_ID');
    }

    /**
     * @return bool|string|null
     */
    public static function api_key()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'API_KEY');
    }

    /**
     * @return bool|string|null
     */
    public static function iblock_type_id()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'IBLOCK_TYPE_ID');
    }

    /**
     * @return bool|string|null
     */
    public static function iblock_mode()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'IBLOCK_MODE');
    }

    /**
     * @return bool|string|null
     */
    public static function iblock_id()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'IBLOCK_ID');
    }

    /**
     * @return bool|string|null
     */
    public static function type_price_ID()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'ID_TYPE_PRICE');
    }

    /**
     * @return bool|string|null
     */
    public static function type_price_priceva_ID()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'ID_TYPE_PRICE_PRICEVA');
    }

    /**
     * @return bool|string|null
     */
    public static function sync_dominance()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_DOMINANCE');
    }

    /**
     * @return bool|string|null
     */
    public static function sync_field()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_FIELD');
    }

    /**
     * @return bool|string|null
     */
    public static function currency()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'CURRENCY');
    }

    /**
     * @return bool
     */
    public static function price_recalc()
    {
        return CommonHelpers::convert_to_bool(COption::GetOptionString(CommonHelpers::MODULE_ID, 'PRICE_RECALC'));
    }

    /**
     * @return bool
     */
    public static function sync_only_active()
    {
        return CommonHelpers::convert_to_bool(COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_ONLY_ACTIVE'));
    }

    /**
     * @return bool|string|null
     */
    public static function download_at_time()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'DOWNLOAD_AT_TIME');
    }

    /**
     * @return bool|string|null
     */
    public static function client_code()
    {
        return COption::GetOptionString(CommonHelpers::MODULE_ID, 'CLIENT_CODE');
    }

    /**
     * @return bool
     */
    public static function trade_offers_enable()
    {
        return CommonHelpers::convert_to_bool(COption::GetOptionString(CommonHelpers::MODULE_ID, 'TRADE_OFFERS_ENABLE'));
    }

    /**
     * @return bool
     */
    public static function simple_product_enable()
    {
        return CommonHelpers::convert_to_bool(COption::GetOptionString(CommonHelpers::MODULE_ID, 'SIMPLE_PRODUCT_ENABLE'));
    }

    /**
     * @return bool
     */
    public static function debug()
    {
        return CommonHelpers::convert_to_bool(COption::GetOptionString(CommonHelpers::MODULE_ID, 'DEBUG'));
    }
}