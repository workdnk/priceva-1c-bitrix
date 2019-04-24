<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 25.01.2019
 * Time: 20:19
 */

namespace Priceva\Connector\Bitrix\Helpers;


use Bitrix\Main\Localization\Loc;
use CAdminMessage;
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

    /**
     * @param string $name
     * @param mixed  $val
     * @param array  $list_options
     *
     * @return bool
     */
    public static function check_option( $name, $val, $list_options )
    {
        $options_dependencies = [
            'IBLOCK_ID'   => [ 'IBLOCK_MODE' => [ '0', 'ALL' ] ],
            'CLIENT_CODE' => [ 'SYNC_FIELD' => [ '0', 'articul' ], ],
        ];

        $options_inverse_dependencies = [
            'TRADE_OFFERS_ENABLE' => [ 'IBLOCK_TYPE_ID' => [ 'catalog' ], ],
        ];

        $result = true;
        // если значени пустое
        if( empty($val) ){
            // если есть какая-то зависимость для текущей опции
            if( $options_dependencies[ $name ] || $options_inverse_dependencies[ $name ] ){
                // переберем все прямые зависимости (их может быть больше одной)
                foreach( $options_dependencies[ $name ] as $key => $dependency ){
                    // проверим,  есть ли в массиве прямых зависимостей значение текущей зависимости,
                    // разрешающее для текущего проверяемого поля пустое значение
                    if( !in_array($list_options[ $key ], $dependency, true) ){
                        $result = false;
                        break;
                    }
                }
                // переберем все обратные зависимости (их может быть больше одной)
                foreach( $options_inverse_dependencies[ $name ] as $key => $dependency ){
                    // проверим,  есть ли в массиве обратных зависимостей значение текущей зависимости,
                    // запрещающее для текущего проверяемого поля пустое значение
                    if( in_array($list_options[ $key ], $dependency, true) ){
                        $result = false;
                        break;
                    }
                }
            }else{
                // если никакой зависимости(ей) нет и поле просто пустое, то это не ОК
                $result = false;
            }

            if( !$result ){
                echo ( new CAdminMessage(Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_FILL_FIELD") . Loc::getMessage("PRICEVA_BC_OPTIONS_TEXT_" . $name)) )->Show();
            }
        }

        return $result;
    }
}