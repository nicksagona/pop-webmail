<?php

return [
    '/login[/]' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'login'
    ],
    '/logout[/]' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'logout'
    ],
    '[/]' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'index'
    ],
    '/:id' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'view'
    ],
    '/attachments/:id/:i' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'attachments'
    ],
    '/compose[/]' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'compose'
    ],
    '*' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'error'
    ]
];