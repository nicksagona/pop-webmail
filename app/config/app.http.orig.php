<?php
/**
 * Pop Webmail Configuration File
 */

return [
    'routes'         => include 'routes/http.php',
    'database'       => include 'database/sqlite.php',
    'forms'          => include 'forms.php',
    'editor'         => null, //'https://cdn.ckeditor.com/4.8.0/full/ckeditor.js',
    'pagination'     => 25,
    'services'       => [
        'session'    => 'Pop\Session\Session::getInstance',
        'cookie'     => [
            'call'   => 'Pop\Cookie\Cookie::getInstance',
            'params' => ['options' => ['path' => '/']]
        ],
        'cache_adapter' => [
            'call'      => 'Pop\Cache\Adapter\File',
            'params'    => ['dir' => __DIR__ . '/../../data/cache', 'ttl' => 1800]
        ],
        'cache'         => [
            'call'      => function() {
                return new \Pop\Cache\Cache(\Pop\Service\Container::get('default')['cache_adapter']);
            }
        ]
    ]
];