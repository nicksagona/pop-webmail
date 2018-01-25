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
    '/mail[/]' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'index'
    ],
    '/mail/:id' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'view'
    ],
    '/mail/attachments/:id/:i' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'attachments'
    ],
    '/mail/compose[/]' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'compose'
    ],
    '*' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'error'
    ]
];