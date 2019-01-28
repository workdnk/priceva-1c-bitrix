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
                $sync_method = CommonHelpers::get_sync_method();
                $api_key     = CommonHelpers::get_api_key();

                switch( $sync_method ){
                    case "all_to_all":
                        {
                            $this->sync_all_to_all($api_key, $dbProducts);
                            break;
                        }
                    case "one_to_one":
                        {
                            $this->sync_one_to_one($api_key, $dbProducts);
                            break;
                        }
                    default:
                        throw new \Exception("Wrong sync dominance type in module " . CommonHelpers::MODULE_ID);
                }
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
     */
    private function sync_one_to_one( $api_key, $dbProducts )
    {

    }

    /**
     * @param string     $api_key
     * @param \CDBResult $dbProducts
     *
     * @throws PricevaException
     */
    private function sync_all_to_all( $api_key, $dbProducts )
    {
        $reports = $this->get_reports($api_key);

        $id_type_of_price = CommonHelpers::get_type_price_ID();
        $price_recalc     = CommonHelpers::get_price_recalc();
        $currency         = CommonHelpers::get_currency();

        $sync_field = CommonHelpers::get_sync_field();

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

        $this->add_event();
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
     *
     * @return array
     * @throws PricevaException
     */
    private function get_reports( $api_key )
    {
        $api = new PricevaAPI($api_key);

        $filters        = new \Priceva\Params\Filters();
        $product_fields = new \Priceva\Params\ProductFields();

        $filters[ 'limit' ] = 1000;

        $product_fields[] = 'client_code';
        $product_fields[] = 'articul';

        $reports = $api->report_list($filters, $product_fields);

        return $reports->get_result()->objects;
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