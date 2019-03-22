<?php
/**
 * Created by PhpStorm.
 * S.Belichenko, email: stanislav@priceva.com
 * Date: 08.10.2018
 * Time: 17:57
 */

namespace Priceva;


use Exception;

/**
 * Class Exception
 *
 * @package Priceva
 */
class PricevaException extends Exception
{
    private $errors = [
        '400' => 'Your request caused an error. You can not get the result.',
        '401' => 'Authorization failed.',
        '418' => 'The response from the server cannot be converted to an array, json or other entity.',
        '500' => 'Internal Server Error',
    ];

    /**
     * Exception constructor.
     *
     * @param null $message
     * @param int  $code
     */
    public function __construct( $message = null, $code = 0 )
    {
        if( isset($this->errors[ $code ]) && empty($message) ){
            $message = $this->errors[ $code ];
        }

        parent::__construct($message, $code);
    }
}
