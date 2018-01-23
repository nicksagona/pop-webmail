<?php

return [
    '/login[/]' => [
        'controller' => 'Pab\Controller\IndexController',
        'action'     => 'login'
    ],
    '/logout[/]' => [
        'controller' => 'Pab\Controller\IndexController',
        'action'     => 'logout'
    ],
    '/mail[/]' => [
        'controller' => 'Pab\Controller\MailController',
        'action'     => 'index'
    ],
    '/mail/:id' => [
        'controller' => 'Pab\Controller\MailController',
        'action'     => 'view'
    ],
    '/mail/attachments/:id/:i' => [
        'controller' => 'Pab\Controller\MailController',
        'action'     => 'attachments'
    ],
    '/mail/compose[/]' => [
        'controller' => 'Pab\Controller\MailController',
        'action'     => 'compose'
    ],
    '*' => [
        'controller' => 'Pab\Controller\IndexController',
        'action'     => 'error'
    ]
];