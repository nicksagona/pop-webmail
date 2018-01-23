<?php

return [
    [
        'username' => [
            'type'       => 'text',
            'required'   => true,
            'attributes' => [
                'placeholder' => 'Username',
                'class'       => 'form-control login-field'
            ]
        ],
        'password' => [
            'type'       => 'password',
            'required'   => true,
            'attributes' => [
                'placeholder' => 'Password',
                'class'       => 'form-control login-field'
            ]
        ]
    ],
    [
        'submit' => [
            'type'  => 'submit',
            'value' => 'Log In',
            'attributes' => [
                'class' => 'btn btn-lg btn-block text-uppercase login-btn'
            ]
        ]
    ]
];
