<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 11.10.2018
 * Time: 12:37
 */

namespace Priceva\Contracts;


/**
 * Class ParamsIterator
 *
 * @package Priceva\Contracts
 */
class ParamsIterator implements \Iterator
{
    private $container;


    /**
     * ParamsIterator constructor.
     *
     * @param $container
     */
    public function __construct( $container )
    {
        $this->container = $container;
    }

    public function rewind()
    {
        reset($this->container);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $var = current($this->container);

        return $var;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = key($this->container);
        $var = ( $key !== null && $key !== false );

        return $var;
    }

    /**
     * @return int|mixed|null|string
     */
    public function key()
    {
        $var = key($this->container);

        return $var;
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $var = next($this->container);

        return $var;
    }
}
