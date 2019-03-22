<?php
/**
 * Created by PhpStorm.
 * S.Belichenko, email: stanislav@priceva.com
 * Date: 09.10.2018
 * Time: 10:09
 */

namespace Priceva;


use Priceva\Params\Filters;
use Priceva\Params\ProductFields;
use Priceva\Params\Sources;

class PricevaAPITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PricevaAPI
     */
    private $PricevaAPI;

    protected function setUp()
    {
        $this->PricevaAPI = new PricevaAPI('wrong_key');
    }

    protected function tearDown()
    {
        unset($this->PricevaAPI);
    }

    /**
     * @return array
     */
    public function data_products()
    {
        $filters_empty = new Filters();
        $sources_empty = new Sources();

        $filters_fully           = new Filters();
        $filters_fully[ 'page' ] = 1;

        $sources_fully          = new Sources();
        $sources_fully[ 'add' ] = true;

        return [
            [
                'FILTERS' => [],
                'SOURCES' => [],
            ],
            [
                'FILTERS' => [ 'page' => 1 ],
                'SOURCES' => [ 'client_code' => 1 ],
            ],
            [
                'FILTERS' => $filters_empty,
                'SOURCES' => $sources_empty,
            ],
            [
                'FILTERS' => $filters_fully,
                'SOURCES' => $sources_fully,
            ],
        ];
    }

    /**
     * @return array
     */
    public function data_reports()
    {
        $filters_empty        = new Filters();
        $product_fields_empty = new ProductFields();

        $filters_fully           = new Filters();
        $filters_fully[ 'page' ] = 1;

        $product_fields_fully   = new ProductFields();
        $product_fields_fully[] = 'client_code';

        return [
            [
                'FILTERS'        => [],
                'PRODUCT_FIELDS' => [],
            ],
            [
                'FILTERS'        => [ 'page' => 1 ],
                'PRODUCT_FIELDS' => [ 'client_code' => 1 ],
            ],
            [
                'FILTERS'        => $filters_empty,
                'PRODUCT_FIELDS' => $product_fields_empty,
            ],
            [
                'FILTERS'        => $filters_fully,
                'PRODUCT_FIELDS' => $product_fields_fully,
            ],
        ];
    }

    public function test__construct()
    {
        $this->assertInstanceOf('Priceva\PricevaAPI', $this->PricevaAPI);
    }

    /**
     * @throws PricevaException
     */
    public function testSet_filtersObject()
    {
        $filters_obj           = new Filters();
        $filters_obj[ 'page' ] = 1;
        $this->PricevaAPI->set_filters($filters_obj);
        $this->AssertEquals($this->PricevaAPI->get_filters()->get_array(), [
            'page' => 1,
        ]);
    }

    /**
     * @throws PricevaException
     */
    public function testSet_filtersArray()
    {
        $filters_arr[ 'page' ] = 1;
        $this->PricevaAPI->set_filters($filters_arr);
        $this->AssertEquals($this->PricevaAPI->get_filters()->get_array(), [
            'page' => 1,
        ]);
    }

    /**
     * @throws PricevaException
     */
    public function testSet_filtersAgain()
    {
        $filters_arr            = [];
        $filters_arr[ 'limit' ] = 1;
        $this->PricevaAPI->set_filters($filters_arr);

        $filters_obj           = new Filters();
        $filters_obj[ 'page' ] = 1;
        $this->PricevaAPI->set_filters($filters_obj);

        $this->AssertEquals($this->PricevaAPI->get_filters()->get_array(), [
            'page'  => 1,
            'limit' => 1,
        ]);
    }

    /**
     * @throws PricevaException
     */
    public function testSet_sourcesObject()
    {
        $sources_obj          = new Sources();
        $sources_obj[ 'add' ] = 1;

        $this->PricevaAPI->set_sources($sources_obj);
        $this->AssertEquals($this->PricevaAPI->get_sources()->get_array(), [
            'add' => 1,
        ]);
    }

    /**
     * @throws PricevaException
     */
    public function testSet_sourcesArray()
    {
        $sources_arr[ 'add' ] = 1;

        $this->PricevaAPI->set_sources($sources_arr);
        $this->AssertEquals($this->PricevaAPI->get_sources()->get_array(), [
            'add' => 1,
        ]);
    }

    /**
     * @throws PricevaException
     */
    public function testSet_sourcesAgain()
    {
        $sources_arr          = [];
        $sources_arr[ 'add' ] = 1;
        $this->PricevaAPI->set_sources($sources_arr);

        $sources_obj               = new Sources();
        $sources_obj[ 'add_term' ] = 1;
        $this->PricevaAPI->set_sources($sources_obj);

        $this->AssertEquals($this->PricevaAPI->get_sources()->get_array(), [
            'add'      => 1,
            'add_term' => 1,
        ]);
    }

    /**
     * @expectedException \Priceva\PricevaException
     * @expectedExceptionMessage Params must be an array or an an object extending from the class
     *                           Priceva\Contracts\Params.
     */
    public function testSet_filtersThrowException()
    {
        $filters_arr = 'page';
        /** @noinspection PhpParamsInspection */
        $this->PricevaAPI->set_filters($filters_arr);
    }

    /**
     * @expectedException \Priceva\PricevaException
     * @expectedExceptionMessage Params must be an array or an an object extending from the class
     *                           Priceva\Contracts\Params.
     */
    public function testSet_sourcesThrowException()
    {
        $sources_arr = 'wrong_param';
        /** @noinspection PhpParamsInspection */
        $this->PricevaAPI->set_sources($sources_arr);
    }

    /**
     * @expectedException \Priceva\PricevaException
     * @expectedExceptionMessage Params must be an array or an an object extending from the class
     *                           Priceva\Contracts\Params.
     */
    public function testSet_productFieldsThrowException()
    {
        $product_fields_arr = 'wrong_param';
        /** @noinspection PhpParamsInspection */
        $this->PricevaAPI->set_sources($product_fields_arr);
    }

    /**
     * @throws PricevaException
     */
    public function testMain_demo()
    {
        $result = $this->PricevaAPI->main_demo();

        $this->assertInstanceOf('Priceva\Result', $result);
    }

    /**
     * @throws PricevaException
     */
    public function testMain_ping()
    {
        $result = $this->PricevaAPI->main_ping();

        $this->assertInstanceOf('Priceva\Result', $result);
    }

    /**
     * @dataProvider data_products
     *
     * @param Filters $filters
     * @param Sources $sources
     *
     * @throws PricevaException
     */
    public function testProduct_list( $filters, $sources )
    {
        $result = $this->PricevaAPI->product_list($filters, $sources);

        $this->assertInstanceOf('Priceva\Result', $result);
    }

    /**
     * @dataProvider data_reports
     *
     * @param Filters       $filters
     * @param ProductFields $product_fields
     *
     * @throws PricevaException
     */
    public function testReport_list( $filters, $product_fields )
    {
        $result = $this->PricevaAPI->report_list($filters, $product_fields);
        $this->assertInstanceOf('Priceva\Result', $result);
    }
}
