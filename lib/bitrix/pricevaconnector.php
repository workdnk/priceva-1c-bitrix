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
use Priceva\Connector\Bitrix\Helpers\{CommonHelpers, OptionsHelpers};
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

            $api_key          = OptionsHelpers::get_api_key();
            $sync_only_active = OptionsHelpers::get_sync_only_active();

            $arFilter = [
                'IBLOCK_ID' => 2,
            ];

            if( $sync_only_active ){
                $arFilter = array_merge($arFilter, [
                    'ACTIVE'      => 'Y',
                    'ACTIVE_DATE' => 'Y',
                ]);
            }

            if( $dbProducts = \CIBlockElement::GetList($arFilter) ){
                $this->sync($api_key, $dbProducts, $sync_only_active);
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
     * @noinspection PhpUndefinedClassInspection
     *
     * @param string     $api_key
     * @param \CDBResult $dbProducts
     * @param bool       $sync_only_active
     *
     * @throws PricevaException
     * @throws \Exception
     */
    private function sync( $api_key, $dbProducts, $sync_only_active )
    {
        $id_type_of_price = CommonHelpers::get_type_price_ID();
        $price_recalc     = OptionsHelpers::get_price_recalc();
        $currency         = OptionsHelpers::get_currency();
        $sync_field       = OptionsHelpers::get_sync_field();
        $sync_dominance   = OptionsHelpers::get_sync_dominance();

        switch( $sync_dominance ){
            case "priceva":
                {
                    $this->priceva_to_bitrix($api_key, $dbProducts, $id_type_of_price, $price_recalc, $currency, $sync_field, $sync_only_active);
                    break;
                }
            case "bitrix":
                {
                    $this->bitrix_to_priceva($api_key, $dbProducts, $id_type_of_price, $price_recalc, $currency, $sync_field, $sync_only_active);
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
        $sync_field,
        $sync_only_active
    ){

    }

    /**
     * @noinspection PhpUndefinedClassInspection
     *
     * @param string     $api_key
     * @param \CDBResult $dbProducts
     * @param int        $id_type_of_price
     * @param bool       $price_recalc
     * @param string     $currency
     * @param string     $sync_field
     * @param bool       $sync_only_active
     *
     * @throws PricevaException
     */
    private function bitrix_to_priceva(
        $api_key,
        $dbProducts,
        $id_type_of_price,
        $price_recalc,
        $currency,
        $sync_field,
        $sync_only_active
    ){
        $reports = $this->get_all_reports($api_key, $sync_only_active);

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
        return
            Loc::getMessage("PRICEVA_BC_INFO_TEXT1") . ": {$this->info['product_not_found_priceva']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT2") . ": {$this->info['product_not_found_bitrix_articul']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT3") . ": {$this->info['price_is_null_priceva']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT4") . ": {$this->info['product_synced']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT5") . ": {$this->info['product_not_synced']}";
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
     * @param bool   $sync_only_active
     *
     * @return array
     * @throws PricevaException
     */
    private function get_all_reports( $api_key, $sync_only_active )
    {
        $api = new PricevaAPI($api_key);

        $filters        = new \Priceva\Params\Filters();
        $product_fields = new \Priceva\Params\ProductFields();

        $filters[ 'limit' ] = 1000;
        $filters[ 'page' ]  = 1;

        if( $sync_only_active ){
            $filters[ 'active' ] = 1;
        }

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
    }

    /**
     * @param array $objects
     * @param int   $id
     *
     * @return int
     */
    private function get_price( $objects, $id )
    {
        $sync_field = OptionsHelpers::get_sync_field();

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
}