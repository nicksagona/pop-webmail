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
    '/mail/cc/:id' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'cc'
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
    '/mail/box/:id' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'box'
    ],
    '/mail/upload' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'upload'
    ],
    '/mail/clean' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'clean'
    ],
    '/mail/folder/add' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'addFolder'
    ],
    '/mail/folder/rename' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'renameFolder'
    ],
    '/mail/folder/remove' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'removeFolder'
    ],
    '/mail/process' => [
        'controller' => 'PopWebmail\Controller\MailController',
        'action'     => 'process'
    ],
    '*' => [
        'controller' => 'PopWebmail\Controller\IndexController',
        'action'     => 'error'
    ]
];