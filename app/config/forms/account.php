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
            'type'       => 'password',
            'label'      => 'IMAP Password <span class="normal small">(<a href="#" onclick="return pop.showPassword(this, \'imap\');">Show</a>)</span>',
            'attributes' => [
                'class'  => 'form-control'
            ],
            'render'     => true
        ],
        'imap_flags' => [
            'type'       => 'text',
            'label'      => 'IMAP Flags <span class="normal">(/ssl, /tls, etc.)</span>',
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
            'type'       => 'password',
            'label'      => 'SMTP Password <span class="normal small">(<a href="#" onclick="return pop.showPassword(this, \'smtp\');">Show</a>)</span>',
            'attributes' => [
                'class'  => 'form-control'
            ],
            'render'     => true
        ],
        'smtp_security' => [
            'type'       => 'text',
            'label'      => 'SMTP Security <span class="normal">(tls, ssl, etc.)</span>',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ]
    ],
    'Signature Settings' => [
        'html_signature' => [
            'type'       => 'textarea',
            'label'      => 'HTML Signature',
            'attributes' => [
                'class'  => 'form-control',
                'rows'   => 6,
                'cols'   => 20
            ]
        ],
        'text_signature' => [
            'type'       => 'textarea',
            'label'      => 'Text Signature',
            'attributes' => [
                'class'  => 'form-control',
                'rows'   => 6,
                'cols'   => 20
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
        'signature_on_all' => [
            'type'   => 'checkbox',
            'label'  => '&nbsp;',
            'values' => [
                1 => 'Signature on Replies/Forwards?'
            ]
        ],
        'id' => [
            'type'  => 'hidden',
            'value' => 0,
        ],
        'submit' => [
            'type'  => 'submit',
            'value' => 'Save',
            'attributes' => [
                'class'  => 'btn btn-md btn-info btn-block text-uppercase login-btn'
            ]
        ],
    ]
];
