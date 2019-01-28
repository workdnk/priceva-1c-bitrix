<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 25.01.2019
 * Time: 20:19
 */

namespace Priceva\Connector\Bitrix\Helpers;


class OptionsHelpers
{
    /**
     * @return bool
     */
    public static function is_save_method()
    {
        global $Update, $Apply, $RestoreDefaults;

        return isset($Update) || isset($Apply) || isset($RestoreDefaults);
    }

    public static function is_restore_method()
    {
        global $RestoreDefaults;

        return isset($RestoreDefaults);
    }
}