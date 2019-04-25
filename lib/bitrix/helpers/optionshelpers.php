<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 25.01.2019
 * Time: 20:19
 */

namespace Priceva\Connector\Bitrix\Helpers;


use CCatalogGroup;
use Priceva\Connector\Bitrix\Options;

class OptionsHelpers
{
    private static $instance;

    /**
     * @return OptionsHelpers
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
    public static function type_price_is_base()
    {
        $type_price = CCatalogGroup::GetByID(Options::type_price_ID());

        if( false === $type_price ){
            return false;
        }else{
            return $type_price[ 'BASE' ] === 'Y';
        }
    }

    /**
     * @return int|bool
     */
    public static function get_base_price_type()
    {
        $price_types = CCatalogGroup::GetList([], [ 'BASE' => 'Y' ]);

        if( $base_price_type = $price_types->Fetch() ){
            return $base_price_type[ 'ID' ];
        }

        return false;
    }

    /**
     * @return int|bool
     */
    public static function find_price_type_priceva_id()
    {
        $price_types = CCatalogGroup::GetList([], [ 'NAME' => 'PRICEVA' ]);

        if( $price_type_priceva = $price_types->Fetch() ){
            return $price_type_priceva[ 'ID' ];
        }

        return false;
    }
}