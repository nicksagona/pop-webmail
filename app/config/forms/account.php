<?php

return [
    [
        'name' => [
            'type'       => 'text',
            'required'   => true,
            'label'      => 'Account Name',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ]
    ],
    'Incoming IMAP Settings' => [
        'imap_host' => [
            'type'       => 'text',
            'label'      => 'IMAP Host',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'imap_port' => [
            'type'       => 'text',
            'label'      => 'IMAP Port',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'imap_username' => [
            'type'       => 'text',
            'label'      => 'IMAP Username',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'imap_password' => [
            'type'       => 'text',
            'label'      => 'IMAP Password',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ]
    ],
    'Outgoing SMTP Settings' => [
        'smtp_host' => [
            'type'       => 'text',
            'label'      => 'SMTP Host',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'smtp_port' => [
            'type'       => 'text',
            'label'      => 'SMTP Port',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'smtp_username' => [
            'type'       => 'text',
            'label'      => 'SMTP Username',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'smtp_password' => [
            'type'       => 'text',
            'label'      => 'SMTP Password',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'smtp_security' => [
            'type'       => 'text',
            'label'      => 'SMTP Security',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ]
    ],
    [
        'default' => [
            'type'   => 'checkbox',
            'label'  => '&nbsp;',
            'values' => [
                1 => 'Default Account?'
            ]
        ],
        'id' => [
            'type'  => 'hidden',
            'value' => 0,
        ],
        'submit' => [
            'type'  => 'submit',
            'value' => 'Save',
            'label' => '&nbsp;',
            'attributes' => [
                'class'  => 'btn btn-md btn-info btn-block text-uppercase login-btn'
            ]
        ],
    ]
];
