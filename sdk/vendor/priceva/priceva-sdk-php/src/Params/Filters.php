<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 10.10.2018
 * Time: 10:24
 */

namespace Priceva\Params;


use Priceva\Contracts\Params;

/**
 * Class Filters
 *
 * @package Priceva\Params
 */
class Filters extends Params
{
    protected $flat             = false;
    protected $valid_parameters = [
        'page',
        'limit',
        'category_id',
        'brand_id',
        'company_id',
        'region_id',
        'active',
        'name',
        'articul',
        'client_code',
    ];
}
