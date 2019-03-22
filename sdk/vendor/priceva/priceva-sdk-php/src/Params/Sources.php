<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 12.10.2018
 * Time: 15:30
 */

namespace Priceva\Params;


use Priceva\Contracts\Params;

/**
 * Class Sources
 *
 * @package Priceva\Params
 */
class Sources extends Params
{
    protected $flat             = false;
    protected $valid_parameters = [
        'add',
        'add_term',
    ];
}
