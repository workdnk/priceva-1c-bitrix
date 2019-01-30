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

    /**
     * @return bool|string|null
     */
    public static function get_agent_id()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'AGENT_ID');
    }

    /**
     * @return bool|string|null
     */
    public static function get_api_key()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'API_KEY');
    }

    /**
     * @return bool|string|null
     */
    public static function get_sync_dominance()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_DOMINANCE');
    }

    /**
     * @return bool|string|null
     */
    public static function get_sync_field()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_FIELD');
    }

    /**
     * @return bool|string|null
     */
    public static function get_currency()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'CURRENCY');
    }

    /**
     * @return bool
     */
    public static function get_price_recalc()
    {
        return CommonHelpers::convert_to_bool(\COption::GetOptionString(CommonHelpers::MODULE_ID, 'PRICE_RECALC'));
    }

    /**
     * @return bool
     */
    public static function get_sync_only_active()
    {
        return CommonHelpers::convert_to_bool(\COption::GetOptionString(CommonHelpers::MODULE_ID, 'SYNC_ONLY_ACTIVE'));
    }

    /**
     * @return bool|string|null
     */
    public static function get_download_at_time()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'DOWNLOAD_AT_TIME');
    }

    /**
     * @return bool|string|null
     */
    public static function get_client_code()
    {
        return \COption::GetOptionString(CommonHelpers::MODULE_ID, 'CLIENT_CODE');
    }
}