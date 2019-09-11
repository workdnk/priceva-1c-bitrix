<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 30.04.2019
 * Time: 18:46
 */

namespace Priceva\Connector\Bitrix\Helpers;


class PathHelpers
{
    /**
     * @return string
     */
    public static function document_root()
    {
        return $_SERVER[ "DOCUMENT_ROOT" ];
    }

    /**
     * @return string
     */
    public static function bitrix_root()
    {
        return self::document_root() . '/bitrix';
    }

    /**
     * @return string
     */
    public static function group_rights()
    {
        return self::bitrix_root() . "/modules/main/admin/group_rights.php";
    }

    /**
     * @param bool $notDocumentRoot
     *
     * @return string
     */
    static public function current_path( $notDocumentRoot = false )
    {
        if( $notDocumentRoot )
            return str_ireplace(self::document_root(), '', dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    /**
     * @param string $nesting
     *
     * @return string
     */
    public static function module_root( $nesting = '/../../' )
    {
        return self::current_path() . $nesting;
    }

    /**
     * @return string
     */
    public static function lang_main_options()
    {
        return self::bitrix_root() . "/modules/main/options.php";
    }

    /**
     * @return string
     */
    public static function prolog_admin()
    {
        return self::bitrix_root() . "/modules/main/include/prolog_admin.php";
    }

    public static function prolog_admin_after()
    {
        return self::bitrix_root() . "/modules/main/include/prolog_admin_after.php";
    }

    /**
     * @param string $nesting
     *
     * @return string
     */
    public static function prolog_module( $nesting = '/../' )
    {
        return self::module_root($nesting) . "prolog.php";
    }

    public static function epilog_admin()
    {
        return self::bitrix_root() . "/modules/main/include/epilog_admin.php";
    }
}