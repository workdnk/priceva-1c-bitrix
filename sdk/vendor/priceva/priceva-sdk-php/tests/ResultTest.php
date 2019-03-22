<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 11.10.2018
 * Time: 11:25
 */

namespace Priceva;


class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Result $Result
     */
    private $Result;

    /**
     * @throws PricevaException
     */
    protected function setUp()
    {
        $PricevaAPI = new PricevaAPI('wrong_key');

        $this->Result = $PricevaAPI->main_ping();
    }

    protected function tearDown()
    {
        unset($this->PricevaAPI);
    }

    /**
     * @expectedException \Priceva\PricevaException
     * @expectedExceptionMessage Your request caused an error. You can not get the result.
     *
     * @throws PricevaException
     */
    public function testGet_result()
    {
        $this->Result->get_result();
    }

    public function testGet_info()
    {
        $info = $this->Result->get_info();

        $this->assertTrue(
            array_key_exists('timestamp', $info) &&
            array_key_exists('date', $info) &&
            array_key_exists('time_execution_sec', $info)
        );
    }

    public function testGet_errors()
    {
        $errors      = $this->Result->get_errors();
        $first_error = $errors[ 0 ];

        $this->assertTrue(
            array_key_exists('code', $first_error) && array_key_exists('message', $first_error)
        );
    }

    public function testError()
    {
        $answer = $this->Result->error();

        $this->assertTrue($answer);
    }

    /**
     * @expectedException \Priceva\PricevaException
     * @expectedExceptionMessage Your request caused an error. You can not get the result.
     *
     * @throws PricevaException
     */
    public function testGet_raw()
    {
        $this->Result->get_raw();
    }
}
