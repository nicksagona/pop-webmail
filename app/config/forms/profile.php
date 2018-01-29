<?php

return [
    [
        'username' => [
            'type'       => 'text',
            'required'   => true,
            'label'      => 'Username',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'email' => [
            'type'       => 'email',
            'label'      => 'Email',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'password' => [
            'type'       => 'password',
            'label'      => 'Change Password?',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'password2' => [
            'type'       => 'password',
            'label'      => 'Confirm New Password',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ]
    ],
    [
        'submit' => [
            'type'  => 'submit',
            'value' => 'Save',
            'label' => '&nbsp;',
            'attributes' => [
                'class'  => 'btn btn-lg btn-info btn-block text-uppercase login-btn'
            ]
        ],
        'id' => [
            'type'  => 'hidden',
            'value' => 0,
        ]
    ]
];
