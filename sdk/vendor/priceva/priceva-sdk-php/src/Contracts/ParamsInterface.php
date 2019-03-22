<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 12.10.2018
 * Time: 12:03
 */

namespace Priceva\Contracts;


use Priceva\PricevaException;

interface ParamsInterface
{
    /**
     * @return array
     */
    public function get_array();

    /**
     * @param array|Params $array
     *
     * @throws PricevaException
     */
    public function merge( $array );

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @throws PricevaException
     */
    public function offsetSet( $offset, $value );

    /**
     * @return ParamsIterator
     */
    public function getIterator();

    /**
     * @return int
     */
    public function count();

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists( $offset );

    /**
     * @param string $offset
     */
    public function offsetUnset( $offset );

    /**
     * @param string $offset
     *
     * @return string|null
     */
    public function offsetGet( $offset );

    /**
     * @return array
     */
    public function jsonSerialize();
}
