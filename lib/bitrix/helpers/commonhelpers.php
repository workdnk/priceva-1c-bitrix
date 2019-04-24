<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 21.01.2019
 * Time: 16:03
 */

namespace Priceva\Connector\Bitrix\Helpers;


use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\IO\File;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CAllMain;
use CBXFeatures;
use CCatalogGroup;
use CCurrency;
use CEventLog;
use CMain;
use Exception;

class CommonHelpers
{
    const MODULE_ID = 'priceva.connector';

    const NAME_PRICE_TYPE = 'PRICEVA';

    const NEEDED_BITRIX_VER = '17.05.10';

    private static $instance;

    /**
     * @var Application|bool
     */
    public $app;
    /**
     * @var bool|CAllMain|CMain
     */
    public $APPLICATION;

    public function __construct()
    {
        $this->app         = $this->get_app();
        $this->APPLICATION = $this->get_application();
    }

    public function get_current_path( $notDocumentRoot = false )
    {
        return ( $notDocumentRoot )
            ? str_ireplace($this->app::getDocumentRoot(), '', dirname(__DIR__))
            : dirname(__DIR__);
    }

    /**
     * @param string $val
     *
     * @return bool
     */
    public static function convert_to_bool( $val )
    {
        return $val === "YES";
    }

    /**
     * @return Application|bool
     */
    private function get_app()
    {
        global $APPLICATION;
        try{
            return Application::getInstance();
        }catch( SystemException $e ){
            $APPLICATION->ThrowException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_BITRIX_VERSION"));

            return false;
        }
    }

    private function get_application()
    {
        global $APPLICATION;

        if( $APPLICATION === null ){
            return false;
        }else{
            return $APPLICATION;
        }
    }

    /**
     * @return CommonHelpers
     */
    public static function getInstance()
    {
        if( null === static::$instance ){
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param array $select_values
     *
     * @return array
     */
    public static function add_not_selected( $select_values )
    {
        return [ '0' => Loc::getMessage("PRICEVA_BC_COMMON_HELPERS_NOT_SELECTED") ] + $select_values;
    }

    /**
     * @return string|null
     */
    public function request_method()
    {
        return $this->app->getContext()->getRequest()->getRequestMethod();
    }

    /**
     * @return string|null
     */
    public function is_post()
    {
        return "POST" === $this->app->getContext()->getRequest()->getRequestMethod();
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function get_post_param( $name )
    {
        return $this->app->getContext()->getRequest()->getPost($name);
    }

    /**
     * @return array
     * @throws LoaderException
     */
    public static function get_types_of_price()
    {
        $arr = [];

        if( !Loader::includeModule('catalog') ){
            throw new LoaderException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_CATALOG_NOT_INSTALLED"));
        }

        $dbPriceType = CCatalogGroup::GetList();
        while( $arPriceType = $dbPriceType->Fetch() ){
            $arr[ $arPriceType[ 'ID' ] ] = $arPriceType[ 'NAME' ];
        }

        return $arr;
    }

    /**
     * @return array
     * @throws LoaderException
     */
    public static function get_currencies()
    {
        $arr = [];

        if( !Loader::includeModule('catalog') ){
            throw new LoaderException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_CATALOG_NOT_INSTALLED"));
        }
        $by           = "currency";
        $order        = "asc";
        $dbCurrencies = CCurrency::GetList($by, $order);
        while( $dbCurrency = $dbCurrencies->Fetch() ){
            $arr[ $dbCurrency[ 'CURRENCY' ] ] = $dbCurrency[ 'FULL_NAME' ];
        }

        return $arr;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function get_types_iblocks()
    {
        $iblockTypes = [];
        $result      = TypeTable::getList([
            'select' => [
                'ID',
                'NAME' => 'LANG_MESSAGE.NAME',
            ],
            'filter' => [ '=LANG_MESSAGE.LANGUAGE_ID' => 'ru' ],
        ]);
        while( $row = $result->fetch() ){
            $iblockTypes[ $row[ 'ID' ] ] = $row[ 'NAME' ];
        }

        return $iblockTypes;
    }

    /**
     * @param string $iblock_type_id
     *
     * @return array|bool
     */
    public static function get_iblocks( $iblock_type_id )
    {
        try{
            Loader::includeModule('catalog');

            $result = IblockTable::getList([
                'select' => [ 'ID', 'NAME' ],
                'filter' => [
                    'IBLOCK_TYPE_ID' => $iblock_type_id,
                ],
            ]);

            $catalogs = [];

            while( $catalog = $result->Fetch() ){
                $catalogs[ $catalog[ 'ID' ] ] = $catalog[ 'NAME' ];
            }

            return $catalogs;
        }catch( ObjectPropertyException $e ){
            self::write_to_log($e);
        }catch( ArgumentException $e ){
            self::write_to_log($e);
        }catch( SystemException $e ){
            self::write_to_log($e);
        }catch( LoaderException $e ){
            self::write_to_log($e);
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function bitrix_d7()
    {
        return CheckVersion(SM_VERSION, self::NEEDED_BITRIX_VER);
    }

    /**
     * @return bool
     */
    public static function bitrix_full_business()
    {
        return CBXFeatures::IsFeatureEnabled('CatMultiPrice');
    }

    /**
     * @return bool
     */
    public static function check_php_ext()
    {
        return extension_loaded('json') && extension_loaded('curl');
    }

    /**
     * @return bool
     */
    public static function check_php_ver()
    {
        return version_compare(phpversion(), '7.1', '>');
    }

    /**
     * @param string|Exception $message
     * @param string           $type
     */
    public static function write_to_log( $message, $type = 'PRICEVA_ERROR' )
    {
        if( is_object($message) ){
            CEventLog::Add([
                "SEVERITY"      => "",
                "AUDIT_TYPE_ID" => $type,
                "MODULE_ID"     => "priceva.connector",
                "ITEM_ID"       => "priceva.connector",

                "DESCRIPTION" =>
                    "Error: " . $message->getMessage() . "; " .
                    "file: " . $message->getFile() . "; " .
                    "line: " . $message->getLine(),
            ]);
        }else{
            CEventLog::Add([
                "SEVERITY"      => "",
                "AUDIT_TYPE_ID" => $type,
                "MODULE_ID"     => "priceva.connector",
                "ITEM_ID"       => "priceva.connector",

                "DESCRIPTION" => $message,
            ]);
        }

        if( OptionsHelpers::get_debug() ){
            $message = date("d.m.y H:i:s") . ": " . $message;
            Debug::writeToFile($message, "", "priceva.log");
        }
    }

    public static function delete_debug_log()
    {
        File::deleteFile($_SERVER[ 'DOCUMENT_ROOT' ] . "/priceva.log");
    }
}