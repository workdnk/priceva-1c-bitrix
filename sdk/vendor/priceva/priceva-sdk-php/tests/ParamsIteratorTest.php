<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 12.10.2018
 * Time: 9:09
 */

namespace Priceva;


use Priceva\Params\Filters;

class ParamsIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filters $Filter
     */
    private $Params;

    protected function setUp()
    {
        $this->Params = new Filters();

        $this->Params[ 'page' ]        = 1;
        $this->Params[ 'limit' ]       = 2;
        $this->Params[ 'category_id' ] = 3;
    }

    protected function tearDown()
    {
        unset($this->Params);
    }

    public function testRewind()
    {
        foreach( $this->Params as $param ){
            $first = $param;
            $this->assertEquals($first, 1);

            return;
        }

        $this->fail('testRewind failed.');
    }

    public function test__construct()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testValid()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testKey()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testNext()
    {
        $i = 0;
        foreach( $this->Params as $param ){
            $i++;

            if( $i === 2 ){
                $second = $param;
                $this->assertEquals($second, 2);

                return;
            }
        }

        $this->fail('testNext failed.');
    }

    public function testCurrent()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
