<?php
/**
 * Pop Webmail Configuration File
 */

return [
    'routes' => include 'routes/http.php',
    'mail'   => include 'mail.php',
    'forms'  => include 'forms.php',
    'pagination' => 25,
    'services'       => [
        'session'    => 'Pop\Session\Session::getInstance',
        'cookie'     => [
            'call'   => 'Pop\Cookie\Cookie::getInstance',
            'params' => ['options' => ['path' => '/']]
        ]
    ]
];