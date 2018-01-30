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
    '/profile[/]' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'profile'
    ],
    '[/]' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'index'
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
    '/mail/accounts[/]' => [
        'controller' => 'PopWebmail\Controller\AccountsController',
        'action'     => 'index'
    ],
    '/mail/accounts/create[/]' => [
        'controller' => 'PopWebmail\Controller\AccountsController',
        'action'     => 'create'
    ],
    '/mail/accounts/:id' => [
        'controller' => 'PopWebmail\Controller\AccountsController',
        'action'     => 'update'
    ],
    '/mail/accounts/delete[/]' => [
        'controller' => 'PopWebmail\Controller\AccountsController',
        'action'     => 'delete'
    ],
    '*' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'error'
    ]
];