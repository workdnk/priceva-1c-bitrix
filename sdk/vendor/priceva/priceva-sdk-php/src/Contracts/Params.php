<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 12.10.2018
 * Time: 12:03
 */

namespace Priceva\Contracts;


use Priceva\PricevaException;

/**
 * Class Params
 *
 * @package Priceva\Contracts
 */
abstract class Params extends \ArrayObject implements ParamsInterface, \JsonSerializable
{
    protected $container = [];
    protected $valid_parameters;
    protected $flat;

    /**
     * @return array
     */
    public function get_array()
    {
        return $this->container;
    }

    /**
     * @param array|Params $params
     *
     * @throws PricevaException
     */
    public function merge( $params )
    {
        if( is_array($params) ){
            $this->container = array_merge($this->container, $params);
        }elseif( gettype($params) === 'object' and is_a($params, 'Priceva\Contracts\Params') ){
            $this->container = array_merge($this->container, $params->get_array());
        }else{
            throw new PricevaException('Params must be an array or an an object extending from the class Priceva\Contracts\Params.');
        }
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @throws PricevaException
     */
    public function offsetSet( $offset, $value )
    {
        if( $this->flat ){
            if( $offset ){
                throw new PricevaException('You cannot add a named option in flat parameter.');
            }elseif( is_null($value) ){
                throw new PricevaException('You cannot add a empty option in flat parameter.');
            }else{
                if( in_array($value, $this->valid_parameters) ){
                    $this->container[] = $value;
                }else{
                    throw new PricevaException('You can use only valid options in flat parameter.');
                }
            }
        }else{
            if( is_null($offset) ){
                throw new PricevaException('You cannot add a nameless filters parameter.');
            }else{
                if( in_array($offset, $this->valid_parameters) ){
                    $this->container[ $offset ] = $value;
                }else{
                    throw new PricevaException('You can use only valid parameter names.');
                }
            }
        }

    }

    /**
     * @return ParamsIterator
     */
    public function getIterator()
    {
        return new ParamsIterator($this->container);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->container);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists( $offset )
    {
        return isset($this->container[ $offset ]);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset( $offset )
    {
        unset($this->container[ $offset ]);
    }

    /**
     * @param string $offset
     *
     * @return string|null
     */
    public function offsetGet( $offset )
    {
        return isset($this->container[ $offset ]) ? $this->container[ $offset ] : null;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->container;
    }
}
