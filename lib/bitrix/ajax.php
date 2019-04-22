<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 22.04.2019
 * Time: 14:37
 */

namespace Priceva\Connector\Bitrix;


use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Controller;
use Priceva\Connector\Bitrix\Helpers\CommonHelpers;

class Ajax extends Controller
{
    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'test' => [
                'prefilters'  => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        [ ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST ]
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => [],
            ],
        ];
    }

    public function getIblocksAction()
    {
        $request = $this->getRequest();

        $iblocks = CommonHelpers::get_iblocks($request[ 'iblock_type_id' ]);

        return [ 'iblocks' => $iblocks ];
    }
}