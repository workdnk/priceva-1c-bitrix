<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 17.01.2019
 * Time: 18:08
 */

namespace Priceva\Connector\Bitrix;

require_once __DIR__ . "/../../sdk/vendor/autoload.php";


use Bitrix\Main\Localization\Loc;
use Priceva\Connector\Bitrix\Helpers\{CommonHelpers, OptionsHelpers};
use Priceva\PricevaAPI;
use Priceva\PricevaException;

class PricevaConnector
{
    private $info = [
        "product_not_found_priceva" => 0,
        "product_not_found_bitrix"  => 0,
        "articul_priceva_is_empty"  => 0,
        "articul_bitrix_is_empty"   => 0,
        "price_is_null_priceva"     => 0,
        "product_synced"            => 0,
        "product_not_synced"        => 0,
        "module_errors"             => 0,
    ];

    public function __construct()
    {
        //
    }

    public function AddGlobalMenuItem( &$aGlobalMenu, &$aModuleMenu )
    {
        $aModuleMenu[] = [
            "parent_menu" => "global_menu_custom",
            "icon"        => "default_menu_icon",
            "page_icon"   => "default_page_icon",
            "sort"        => 100,
            "text"        => Loc::getMessage("PRICEVA_BC_MANUAL"),
            "title"       => Loc::getMessage("PRICEVA_BC_MANUAL"),
            "url"         => "/bitrix/admin/priceva_bc.php?lang=" . LANGUAGE_ID,
            "more_url"    => [],
        ];

        $arRes = [
            "global_menu_custom" => [
                "menu_id"      => "priceva",
                "page_icon"    => "services_title_icon",
                "index_icon"   => "services_page_icon",
                "text"         => "Priceva",
                "title"        => "Priceva",
                "sort"         => 150,
                "items_id"     => "global_menu_priceva",
                "help_section" => "custom",
                "items"        => [],
            ],
        ];

        return $arRes;
    }

    public static function agent()
    {
        ( new static() )->run();

        return "\Priceva\Connector\Bitrix\PricevaConnector::agent();";
    }

    public function run()
    {
        try{
            if( !\Bitrix\Main\Loader::includeModule('catalog') ){
                throw new PricevaException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_CATALOG_NOT_INSTALLED"));
            }

            if( !CommonHelpers::check_php_ext() ){
                throw new PricevaException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_PHP_EXT"));
            }

            $api_key          = OptionsHelpers::get_api_key();
            $sync_only_active = OptionsHelpers::get_sync_only_active();

            $this->sync($api_key, $sync_only_active);
        }catch( PricevaException $e ){
            ++$this->info[ 'module_errors' ];
            CommonHelpers::write_to_log($e);
        }catch( \Throwable $e ){
            ++$this->info[ 'module_errors' ];
            CommonHelpers::write_to_log($e);
        }
    }

    /**
     * @param string $api_key
     * @param bool   $sync_only_active
     *
     * @throws PricevaException
     * @throws \Exception
     */
    private function sync( $api_key, $sync_only_active )
    {
        $id_type_of_price = OptionsHelpers::get_type_price_ID();
        $price_recalc     = OptionsHelpers::get_price_recalc();
        $currency         = OptionsHelpers::get_currency();
        $sync_field       = OptionsHelpers::get_sync_field();
        $sync_dominance   = OptionsHelpers::get_sync_dominance();

        switch( $sync_dominance ){
            case "priceva":
                {
                    $this->priceva_to_bitrix($api_key, $id_type_of_price, $price_recalc, $currency, $sync_field, $sync_only_active);
                    break;
                }
            case "bitrix":
                {
                    $this->bitrix_to_priceva($api_key, $id_type_of_price, $price_recalc, $currency, $sync_field, $sync_only_active);
                    break;
                }
            default:
                throw new \Exception("Wrong sync dominance type in module " . CommonHelpers::MODULE_ID);
        }

        CommonHelpers::write_to_log($this->get_last_info_msg(), 'PRICEVA_SYNC');
    }

    /**
     * @noinspection PhpUndefinedClassInspection
     *
     * @param string $api_key
     * @param int    $id_type_of_price
     * @param bool   $price_recalc
     * @param string $currency
     * @param string $sync_field
     * @param bool   $sync_only_active
     *
     * @throws PricevaException
     */
    private function priceva_to_bitrix(
        $api_key,
        $id_type_of_price,
        $price_recalc,
        $currency,
        $sync_field,
        $sync_only_active
    ){
        $api = new PricevaAPI($api_key);

        $filters        = new \Priceva\Params\Filters();
        $product_fields = new \Priceva\Params\ProductFields();

        $filters[ 'limit' ] = OptionsHelpers::get_download_at_time();
        $filters[ 'page' ]  = 1;

        if( $sync_only_active ){
            $filters[ 'active' ] = 1;
        }

        $product_fields[] = 'client_code';
        $product_fields[] = 'articul';

        $reports = $api->report_list($filters, $product_fields);

        $pages_cnt = (int)$reports->get_result()->pagination->pages_cnt;

        $priceva_products = $reports->get_result()->objects;

        $this->process_priceva_products($sync_field, $sync_only_active, $priceva_products, $currency, $id_type_of_price, $price_recalc);

        while( $pages_cnt > 1 ){
            $filters[ 'page' ] = $pages_cnt--;

            $reports = $api->report_list($filters, $product_fields);

            $priceva_products = $reports->get_result()->objects;

            $this->process_priceva_products($sync_field, $sync_only_active, $priceva_products, $currency, $id_type_of_price, $price_recalc);
        }
    }

    /**
     * @param string $sync_field
     * @param bool   $sync_only_active
     * @param array  $priceva_products
     * @param string $currency
     * @param int    $id_type_of_price
     * @param bool   $price_recalc
     */
    private function process_priceva_products(
        $sync_field,
        $sync_only_active,
        $priceva_products,
        $currency,
        $id_type_of_price,
        $price_recalc
    ){
        foreach( $priceva_products as $priceva_product ){
            if( $product = $this->get_bitrix_product($sync_field, $sync_only_active, $priceva_product) ){
                $this->process_bitrix_product($sync_field, $product, [ $priceva_product ], $currency, $id_type_of_price, $price_recalc);
            }else{
                ++$this->info[ 'product_not_found_bitrix' ];
            }
        }
    }

    /**
     * @noinspection PhpUndefinedClassInspection
     *
     * @param string    $sync_field
     * @param bool      $sync_only_active
     * @param \stdClass $priceva_product
     *
     * @return array|null
     */
    private function get_bitrix_product( $sync_field, $sync_only_active, $priceva_product )
    {
        $arFilter = $this->prepare_filter_product($sync_only_active);

        if( $sync_field === "articul" ){
            $articul = $priceva_product->articul;

            if( !$articul ){
                ++$this->info[ 'articul_priceva_is_empty' ];

                return [];
            }

            $arFilter = array_merge($arFilter, [
                '=PROPERTY_ARTNUMBER' => $articul,
            ]);
        }else{
            $client_code             = $priceva_product->client_code;
            $what_use_as_client_code = OptionsHelpers::get_client_code();

            $arFilter = array_merge($arFilter, [
                $what_use_as_client_code => $client_code,
            ]);
        }

        $products = \CIBlockElement::GetList([], $arFilter);

        return $products->getNext();
    }

    /**
     * @param string $api_key
     * @param int    $id_type_of_price
     * @param bool   $price_recalc
     * @param string $currency
     * @param string $sync_field
     * @param bool   $sync_only_active
     *
     * @throws PricevaException
     */
    private function bitrix_to_priceva(
        $api_key,
        $id_type_of_price,
        $price_recalc,
        $currency,
        $sync_field,
        $sync_only_active
    ){
        $reports = $this->get_all_reports($api_key, $sync_only_active);

        $arFilter = $this->prepare_filter_product($sync_only_active);

        $dbProducts = \CIBlockElement::GetList([], $arFilter);

        while( $product = $dbProducts->Fetch() ){
            $this->process_bitrix_product($sync_field, $product, $reports, $currency, $id_type_of_price, $price_recalc);
        }
    }

    /**
     * @param bool $sync_only_active
     *
     * @return array
     */
    private function prepare_filter_product( $sync_only_active )
    {
        $arFilter = [
            'IBLOCK_ID' => 2,
        ];

        if( $sync_only_active ){
            $arFilter = array_merge($arFilter, [
                'ACTIVE'      => 'Y',
                'ACTIVE_DATE' => 'Y',
            ]);
        }

        return $arFilter;
    }

    /**
     * @param string $sync_field
     * @param array  $product
     * @param array  $reports
     * @param string $currency
     * @param int    $id_type_of_price
     * @param bool   $price_recalc
     *
     * @return bool
     */
    private function process_bitrix_product(
        $sync_field,
        $product,
        $reports,
        $currency,
        $id_type_of_price,
        $price_recalc
    ){
        if( $sync_field === "articul" ){
            $bitrix_code = $this->get_bitrix_articul($product[ 'ID' ]);
            if( !$bitrix_code ){
                ++$this->info[ 'articul_bitrix_is_empty' ];

                return false;
            }
        }else{
            $what_use_as_client_code = OptionsHelpers::get_client_code();

            $bitrix_code = $product[ $what_use_as_client_code ];
        }
        if( 0 < $price = $this->get_price($reports, $bitrix_code, $sync_field) ){
            $this->set_price($product[ 'ID' ], $price, $currency, $id_type_of_price, $price_recalc);

            return true;
        }

        return false;
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
            Loc::getMessage("PRICEVA_BC_INFO_TEXT2") . ": {$this->info['product_not_found_bitrix']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT3") . ": {$this->info['price_is_null_priceva']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT4") . ": {$this->info['product_synced']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT5") . ": {$this->info['product_not_synced']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT6") . ": {$this->info['articul_priceva_is_empty']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT7") . ": {$this->info['articul_bitrix_is_empty']}, " .
            Loc::getMessage("PRICEVA_BC_INFO_TEXT8") . ": {$this->info['module_errors']}.";
    }

    private function add_event()
    {

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
     * @param array  $objects
     * @param int    $id
     * @param string $sync_field
     *
     * @return int
     */
    private function get_price( $objects, $id, $sync_field )
    {
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