<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 17.01.2019
 * Time: 18:10
 */

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

Class priceva_connector extends CModule
{
    var $MODULE_ID = 'priceva.connector';

    var $PARTNER_NAME = '';
    var $PARTNER_URI  = '';

    var $MODULE_VERSION      = '';
    var $MODULE_VERSION_DATE = '';
    var $MODULE_NAME         = '';
    var $MODULE_DESCRIPTION  = '';

    /**
     * @var $common_helpers  Priceva\Connector\Bitrix\Helpers\CommonHelpers
     */
    private $common_helpers;
    /**
     * @var $options_helpers Priceva\Connector\Bitrix\Helpers\OptionsHelpers
     */
    private $options_helpers;

    private $need_save_unroll = false;

    private $unroll_methods = [];
    private $errors         = [];
    private $info           = [];

    function __construct()
    {
        $arModuleVersion = [];

        include( __DIR__ . "/version.php" );

        $this->MODULE_VERSION      = $arModuleVersion[ "VERSION" ];
        $this->MODULE_VERSION_DATE = $arModuleVersion[ "VERSION_DATE" ];
        $this->MODULE_NAME         = Loc::getMessage("PRICEVA_BC_MODULE_NAME");
        $this->MODULE_DESCRIPTION  = Loc::getMessage("PRICEVA_BC_MODULE_DESC");

        $this->PARTNER_NAME = "Priceva";
        $this->PARTNER_URI  = "https://priceva.ru";
    }

    static public function GetPatch( $notDocumentRoot = false )
    {
        if( $notDocumentRoot )
            return str_ireplace($_SERVER[ "DOCUMENT_ROOT" ], '', dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    /**
     * @throws Exception
     */
    private function check_system()
    {
        if( !$this->common_helpers::check_php_ver() ){
            throw new Exception(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_PHP_VER"));
        }

        if( !$this->common_helpers::check_php_ext() ){
            throw new Exception(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_PHP_EXT"));
        }

        if( !$this->common_helpers::bitrix_d7() ){
            throw new Exception(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_VERSION"));
        }

        if( IsModuleInstalled($this->common_helpers::MODULE_ID) ){
            throw new Exception(Loc::getMessage("PRICEVA_BC_INSTALL_INSTALL"));
        }
    }

    /**
     * @return bool
     */
    function DoInstall()
    {
        global $step;

        try{
            $this->autoload_helpers();
            $this->check_system();

            $step = IntVal($step);

            if( $step < 2 ){
                $this->common_helpers->APPLICATION->IncludeAdminFile(
                    Loc::getMessage("PRICEVA_BC_INSTALL_TITLE_1"),
                    self::GetPatch() . "/install/step1.php"
                );

            }elseif( $step == 2 ){
                $this->need_save_unroll = true;

                $this->InstallFiles();
                $this->InstallTasks();
                $this->InstallEvents();
                $id_type_price = $this->InstallDB();
                COption::SetOptionString($this->common_helpers::MODULE_ID, 'ID_TYPE_PRICE', $id_type_price);
                $id_agent = $this->InstallAgents();
                COption::SetOptionString($this->common_helpers::MODULE_ID, 'ID_AGENT', $id_agent);

                $this->need_save_unroll = false;

                if( $this->errors ){

                    foreach( $this->unroll_methods as $method ){
                        $this->$method();
                    }

                    $this->common_helpers->APPLICATION->IncludeAdminFile(
                        Loc::getMessage("PRICEVA_BC_INSTALL_TITLE_1"),
                        self::GetPatch() . "/install/errors.php"
                    );
                }else{
                    ModuleManager::registerModule($this->common_helpers::MODULE_ID);

                    $aTabs = \Priceva\Connector\Bitrix\Helpers\OptionsHelpers::get_main_options([ 'DEBUG' ]);
                    \Priceva\Connector\Bitrix\Helpers\OptionsHelpers::process_save_form(false, [ [ 'OPTIONS' => $aTabs ] ]);

                    $this->common_helpers->APPLICATION->IncludeAdminFile(
                        Loc::getMessage("PRICEVA_BC_INSTALL_TITLE_1"),
                        self::GetPatch() . "/install/step2.php"
                    );
                }
            }

        }catch( Exception $e ){
            $this->common_helpers::write_to_log($e);
            $this->common_helpers->APPLICATION->ThrowException($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    function DoUninstall()
    {

        try{
            $this->autoload_helpers();

            $this->need_save_unroll = true;

            $this->UnInstallAgents();
            $this->UnInstallEvents();
            $this->UnInstallTasks();
            $this->UnInstallDB();
            $this->UnInstallFiles();

            $this->need_save_unroll = false;

            if( $this->errors ){

                foreach( $this->unroll_methods as $method ){
                    $this->$method();
                }

                $this->common_helpers->APPLICATION->IncludeAdminFile(
                    Loc::getMessage("PRICEVA_BC_UNINSTALL_TITLE_1"),
                    self::GetPatch() . "/install/errors.php"
                );
            }else{
                $this->info[ 'module_id' ] = $this->common_helpers::MODULE_ID;
                $this->common_helpers->APPLICATION->IncludeAdminFile(
                    Loc::getMessage("PRICEVA_BC_INSTALL_TITLE_1"),
                    self::GetPatch() . "/install/unstep1.php"
                );
            }
        }catch( Exception $e ){
            $this->common_helpers::write_to_log($e);
            $this->common_helpers->APPLICATION->ThrowException($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @return bool|int
     */
    function InstallDB()
    {
        parent::InstallDB();

        $r = $this->add_price_type();

        $this->save_unroll($r, "UnInstallDB");

        return $r;
    }

    /**
     * @return string
     */
    function UnInstallDB()
    {
        parent::UnInstallDB();

        $type_price = $this->delete_price_type();

        $this->save_unroll($type_price, "InstallDB");
        $this->info[ 'deleted_price' ] = $type_price;

        return $type_price;
    }

    function InstallFiles()
    {
        parent::InstallFiles();

        $r1 = CopyDirFiles(self::GetPatch() . "/install/admin/", $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/admin/", true, true);
        $r2 = CopyDirFiles(self::GetPatch() . "/install/module/", $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/" . $this->common_helpers::MODULE_ID, true, true);

        $r3 = CopyDirFiles(self::GetPatch() . "/lang/", $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/" . $this->common_helpers::MODULE_ID . "/lang/", true, true);
        $r4 = CopyDirFiles(self::GetPatch() . "/admin/", $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/" . $this->common_helpers::MODULE_ID . "/admin/", true, true);

        $this->save_unroll($r1 && $r2 && $r3 && $r4, "UnInstallFiles");
    }

    function UnInstallFiles()
    {
        parent::UnInstallFiles();

        Bitrix\Main\IO\Directory::deleteDirectory($_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/admin/priceva_bc.php");

        $this->save_unroll(true, "InstallFiles");
    }

    function InstallEvents()
    {
        parent::InstallEvents();

        EventManager::getInstance()->registerEventHandler('main', 'OnBuildGlobalMenu', $this->common_helpers::MODULE_ID, 'Priceva\Connector\Bitrix\PricevaConnector', 'AddGlobalMenuItem');

        $this->save_unroll(true, "UnInstallEvents");
    }

    function InstallTasks()
    {
        parent::InstallTasks();

        $this->save_unroll(true, "UnInstallTasks");
    }

    function UnInstallTasks()
    {
        parent::UnInstallTasks();

        $this->save_unroll(true, "InstallTasks");
    }

    function UnInstallEvents()
    {
        parent::UnInstallEvents();

        EventManager::getInstance()->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->common_helpers::MODULE_ID, 'Priceva\Connector\Bitrix\PricevaConnector', 'AddGlobalMenuItem');

        $this->save_unroll(true, "InstallEvents");
    }

    /**
     * @return int|bool
     */
    private function InstallAgents()
    {
        $this->common_helpers->APPLICATION->ResetException();

        $date = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime('+1 day'));

        $id = CAgent::AddAgent(
            "\Priceva\Connector\Bitrix\PricevaConnector::agent();",
            "priceva.connector",
            "N",
            86400,
            '',
            "Y",
            $date->toString(),
            30
        );

        if( $id === false ){
            $this->errors[] = Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_ADD_AGENT") . ": " . $this->common_helpers->APPLICATION->GetException();

            return false;
        }else{
            $this->save_unroll(true, "UnInstallAgents");

            return $id;
        }
    }

    private function UnInstallAgents()
    {
        CAgent::RemoveAgent(
            "\Priceva\Connector\Bitrix\PricevaConnector::run();",
            "priceva.connector"
        );

        $this->save_unroll(true, "InstallAgents");
    }

    private function price_type_exist()
    {
        try{
            $dbPriceType = \CCatalogGroup::GetList([], [ "NAME" => $this->common_helpers::NAME_PRICE_TYPE ]);
            while( $arPriceType = $dbPriceType->Fetch() ){
                return $arPriceType[ 'ID' ];
            }

            return false;

        }catch( \Throwable $e ){
            $this->add_error($e);

            return false;
        }
    }

    private function delete_price_type()
    {
        try{
            if( !\Bitrix\Main\Loader::includeModule('catalog') ){
                throw new \Bitrix\Main\LoaderException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_MODULE_CATALOG_NOT_INSTALLED"));
            }

            $type_price_ID = $this->options_helpers::get_type_price_ID();

            $this->common_helpers->APPLICATION->ResetException();

            if( false == $type_price_name = \CCatalogGroup::GetByID($type_price_ID)[ 'NAME' ] ){
                if( $error = $this->common_helpers->APPLICATION->GetException() ){
                    throw new \Exception(Loc::getMessage("PRICEVA_BC_ERROR_DELETE_PRICE_TYPE") . " " . $error);
                }else{
                    return Loc::getMessage("PRICEVA_BC_INSTALL_PRICE_TYPE_DELETED_EARLIER");
                }
            }

            if( false === ( new \CCatalogGroup )->Delete($type_price_ID) ){
                if( $error = $this->common_helpers->APPLICATION->GetException() ){
                    throw new \Exception(Loc::getMessage("PRICEVA_BC_ERROR_DELETE_PRICE_TYPE") . " " . $error);
                }else{
                    throw new \Exception(Loc::getMessage("PRICEVA_BC_INSTALL_UNEXPECTED_SITUATION"));
                }
            }

            return Loc::getMessage("PRICEVA_BC_INSTALL_SUCCESS_DELETE_PRICE_TYPE") . $type_price_name . "(id=" . $type_price_ID . ")";
        }catch( \Throwable $e ){
            $this->add_error($e);
        }

        return false;
    }

    /**
     * @return bool|int
     */
    private function add_price_type()
    {
        try{
            \Bitrix\Main\Loader::includeModule('catalog');

            if( false !== $id = self::price_type_exist() ){
                throw new \Exception(Loc::getMessage("PRICEVA_BC_INSTALL_PRICE_TYPE_EXIST") . $this->common_helpers::NAME_PRICE_TYPE . " (id=" . $id . ")");
            }
            $arFields = [
                "NAME"           => $this->common_helpers::NAME_PRICE_TYPE,
                "BASE"           => "N",
                "SORT"           => 100,
                "USER_GROUP"     => [ 1 ],   // видят Администраторы
                "USER_GROUP_BUY" => [ 1 ],  // покупают по этой цене Администраторы
                // только члены группы 2
                "USER_LANG"      => [
                    "ru" => $this->common_helpers::NAME_PRICE_TYPE,
                    "en" => $this->common_helpers::NAME_PRICE_TYPE,
                ],
            ];

            $ID = ( new \CCatalogGroup )->Add($arFields);
            if( $ID <= 0 ){
                throw new \Bitrix\Main\SystemException(Loc::getMessage("PRICEVA_BC_INSTALL_ERROR_ADD_PRICE_TYPE"));
            }else{
                return $ID;
            }
        }catch( \Throwable $e ){
            $this->add_error($e);
        }

        return false;
    }

    /**
     * @param bool   $condition
     * @param string $func
     */
    private function save_unroll( $condition, $func )
    {
        if( ( $this->need_save_unroll ) && ( $condition !== false ) ){
            $this->unroll_methods[] = $func;
        }
    }

    /**
     * @return array
     */
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * @param string $param
     *
     * @return array|string
     */
    public function get_info( $param = '' )
    {
        if( $param ){
            return $this->info[ $param ];
        }else{
            return $this->info;
        }
    }

    /**
     * @param Throwable $error
     */
    private function add_error( $error )
    {
        $this->errors[] = $error->getMessage();
    }

    private function autoload_helpers()
    {
        if(
            !class_exists('\Priceva\Connector\Bitrix\Helpers\CommonHelpers') ||
            !class_exists('\Priceva\Connector\Bitrix\Helpers\OptionsHelpers')
        ){
            CopyDirFiles(self::GetPatch() . "/lib/bitrix/helpers/", $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/$this->MODULE_ID/lib/bitrix/helpers/", true, true);
            require_once( self::GetPatch() . "/include.php" );
        }

        $this->common_helpers  = \Priceva\Connector\Bitrix\Helpers\CommonHelpers::getInstance();
        $this->options_helpers = \Priceva\Connector\Bitrix\Helpers\OptionsHelpers::getInstance();
    }
}