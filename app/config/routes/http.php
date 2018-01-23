<?php

return [
    '/login[/]' => [
        'controller' => 'Pab\Http\Controller\IndexController',
        'action'     => 'login'
    ],
    '/logout[/]' => [
        'controller' => 'Pab\Http\Controller\IndexController',
        'action'     => 'logout'
    ],
    '/mail[/]' => [
        'controller' => 'Pab\Http\Controller\MailController',
        'action'     => 'index'
    ],
    '/mail/:id' => [
        'controller' => 'Pab\Http\Controller\MailController',
        'action'     => 'view'
    ],
    '/mail/attachments/:id/:i' => [
        'controller' => 'Pab\Http\Controller\MailController',
        'action'     => 'attachments'
    ],
    '/mail/compose[/]' => [
        'controller' => 'Pab\Http\Controller\MailController',
        'action'     => 'compose'
    ],
    '*' => [
        'controller' => 'Pab\Http\Controller\IndexController',
        'action'     => 'error'
    ]
];