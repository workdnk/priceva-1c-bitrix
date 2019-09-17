<?php
/**
 * Created by PhpStorm.
 * User: S.Belichenko, email: stanislav@priceva.com
 * Date: 22.04.2019
 * Time: 14:33
 */

return [
    'controllers' => [
        'value'    => [
            'namespaces' => [
                // Ключ - неймспейс для ajax-классов
                // api - приставка экшенов
                '\\Priceva\\Connector\\Bitrix' => 'api',
            ],
        ],
        'readonly' => true,
    ],
];