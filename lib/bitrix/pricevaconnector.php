<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 17.01.2019
 * Time: 18:08
 */

namespace Priceva\Connector\Bitrix;

require_once __DIR__ . "/../../sdk/vendor/autoload.php";


use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\CommonHelpers;
use Priceva\PricevaAPI;
use Priceva\PricevaException;

class PricevaConnector
{
    private $info = [
        "product_not_found_priceva"        => 0,
        "product_not_found_bitrix_articul" => 0,
        "price_is_null_priceva"            => 0,
        "product_synced"                   => 0,
        "product_not_synced"               => 0,
    ];

    public function __construct()
    {
        //
    }

    public function run()
    {
        try{
            if( !\Bitrix\Main\Loader::includeModule('catalog') ){
                throw new LoaderException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_CATALOG_NOT_INSTALLED"));
            }

            if( $dbProducts = \CCatalogProduct::GetList() ){
                $api_key = CommonHelpers::get_api_key();

                $this->sync($api_key, $dbProducts);
            }else{
                throw new \Exception("Cannot get list of products");
            }

        }catch( LoaderException $e ){
            \AddMessage2Log($e->getMessage());
        }catch( PricevaException $e ){
            \AddMessage2Log($e->getMessage());
        }catch( \Throwable $e ){
            \AddMessage2Log($e->getMessage());
        }
    }

    /**
     * @param string     $api_key
     * @param \CDBResult $dbProducts
     *
     * @throws \Exception
     */
    private function sync( $api_key, $dbProducts )
    {
        $id_type_of_price = CommonHelpers::get_type_price_ID();
        $price_recalc     = CommonHelpers::get_price_recalc();
        $currency         = CommonHelpers::get_currency();

        $sync_field = CommonHelpers::get_sync_field();

        $sync_dominance = CommonHelpers::get_sync_dominance();

        switch( $sync_dominance ){
            case "priceva":
                {
                    $this->priceva_to_bitrix($api_key, $dbProducts, $id_type_of_price, $price_recalc, $currency, $sync_field);
                    break;
                }
            case "bitrix":
                {
                    $this->bitrix_to_priceva($api_key, $dbProducts, $id_type_of_price, $price_recalc, $currency, $sync_field);
                    break;
                }
            default:
                throw new \Exception("Wrong sync dominance type in module " . CommonHelpers::MODULE_ID);
        }

        $this->add_event();
    }

    private function priceva_to_bitrix(
        $api_key,
        $dbProducts,
        $id_type_of_price,
        $price_recalc,
        $currency,
        $sync_field
    ){

    }

    /**
     * @param string     $api_key
     * @param \CDBResult $dbProducts
     * @param int        $id_type_of_price
     * @param bool       $price_recalc
     * @param string     $currency
     * @param string     $sync_field
     *
     * @throws PricevaException
     */
    private function bitrix_to_priceva(
        $api_key,
        $dbProducts,
        $id_type_of_price,
        $price_recalc,
        $currency,
        $sync_field
    ){
        $reports = $this->get_reports($api_key);

        while( $product = $dbProducts->Fetch() ){
            if( $sync_field === "articul" ){
                $val = $this->get_bitrix_articul($product[ 'ID' ]);
                if( !$val ){
                    ++$this->info[ 'product_not_found_bitrix_articul' ];
                    continue;
                }
            }else{
                $val = $product[ 'ID' ];
            }
            if( 0 < $price = $this->get_price($reports, $val) ){
                $this->set_price($product[ 'ID' ], $price, $currency, $id_type_of_price, $price_recalc);
            }
        }
    }

    private function get_bitrix_articul( $id )
    {
        $ar_res = \CCatalogProduct::GetByIDEx($id);

        return isset($ar_res[ 'PROPERTIES' ][ 'ARTNUMBER' ][ 'VALUE' ]) ? $ar_res[ 'PROPERTIES' ][ 'ARTNUMBER' ][ 'VALUE' ] : false;
    }

    public function get_last_info_msg()
    {
        return "Не найдено товаров в Priceva: {$this->info[product_not_found_priceva]}, не найдено товаров по артикулам в Bitrix: {$this->info[product_not_found_bitrix_articul]}, нулевых цен в Priceva: {$this->info[price_is_null_priceva]}, успешно обновленных товаров: {$this->info[product_synced]}, ошибок при обновлении товаров: {$this->info[product_not_synced]}";
    }

    private function add_event()
    {
        \CEventLog::Add([
            "SEVERITY"      => "",
            "AUDIT_TYPE_ID" => "PRICEVA_SYNC",
            "MODULE_ID"     => "priceva.connector",
            "ITEM_ID"       => "priceva.connector",

            "DESCRIPTION" => $this->get_last_info_msg(),
        ]);
    }

    /**
     * @param string $api_key
     * @param int    $page
     * @param string $sync_dominance
     *
     * @return array
     * @throws PricevaException
     * @throws \Exception
     */
    private function get_reports( $api_key, $page = 1, $sync_dominance = "bitrix" )
    {
        switch( $sync_dominance ){
            case "bitrix":
                {
                    $api = new PricevaAPI($api_key);

                    $filters        = new \Priceva\Params\Filters();
                    $product_fields = new \Priceva\Params\ProductFields();

                    $filters[ 'limit' ] = 1000;
                    $filters[ 'page' ]  = $page;

                    $product_fields[] = 'client_code';
                    $product_fields[] = 'articul';

                    $reports = $api->report_list($filters, $product_fields);

                    $pages_cnt = (int)$reports->get_result()->pagination->pages_cnt;

                    $objects = $reports->get_result()->objects;

                    while( $pages_cnt > 1 ){
                        $filters[ 'page' ] = $pages_cnt--;

                        $reports = $api->report_list($filters, $product_fields);

                        $objects = array_merge($objects, $reports->get_result()->objects);
                    }

                    return $objects;

                    break;
                }
            default:
                {
                    throw new \Exception("Wrong dominance type");
                }
        }
    }

    /**
     * @param array $objects
     * @param int   $id
     *
     * @return int
     */
    private function get_price( $objects, $id )
    {
        $sync_field = CommonHelpers::get_sync_field();

        $key = array_search($id, array_column($objects, $sync_field));

        if( $key === false ){
            ++$this->info[ 'product_not_found_priceva' ];

            return 0;
        }else{
            if( $objects[ $key ]->recommended_price == 0 ){
                ++$this->info[ 'price_is_null_priceva' ];
            }

            return $objects[ $key ]->recommended_price;
        }
    }

    private function set_price( $product_id, $price, $currency, $id_type_of_price, $price_recalc )
    {
        $arFields = [
            "PRODUCT_ID"       => $product_id,
            "CATALOG_GROUP_ID" => $id_type_of_price,
            "PRICE"            => $price,
            "CURRENCY"         => $currency,
        ];

        if( false === \CPrice::Add($arFields, $price_recalc) ){
            ++$this->info[ 'product_not_synced' ];
        }else{
            ++$this->info[ 'product_synced' ];
        }
    }

    /**
     * @return array
     * @throws LoaderException
     */
    public function get_currencies()
    {
        $arr = [];

        if( !\Bitrix\Main\Loader::includeModule('catalog') ){
            throw new LoaderException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_CATALOG_NOT_INSTALLED"));
        }
        $by           = "currency";
        $order        = "asc";
        $dbCurrencies = \CCurrency::GetList($by, $order);
        while( $dbCurrency = $dbCurrencies->Fetch() ){
            $arr[ $dbCurrency[ 'CURRENCY' ] ] = $dbCurrency[ 'FULL_NAME' ];
        }

        return $arr;
    }
}