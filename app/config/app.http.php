<?php
/**
 * Pop Webmail Configuration File
 */

return [
    'routes'         => include 'routes/http.php',
    'database'       => include 'database/sqlite.php',
    'forms'          => include 'forms.php',
    'editor'         => 'https://cdn.ckeditor.com/4.8.0/full/ckeditor.js',
    'pagination'     => 25,
    'services'       => [
        'session'    => 'Pop\Session\Session::getInstance',
        'cookie'     => [
            'call'   => 'Pop\Cookie\Cookie::getInstance',
            'params' => ['options' => ['path' => '/']]
        ]
    ]
];