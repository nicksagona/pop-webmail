<?php

return [
    [
        'subject' => [
            'type'       => 'text',
            'label'      => 'Subject:',
            'required'   => true,
            'attributes' => [
                'class'  => 'form-control'
            ]
        ]
    ],
    [
        'to' => [
            'type'       => 'text',
            'label'      => 'To:',
            'required'   => true,
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'cc' => [
            'type'       => 'text',
            'label'      => 'CC:',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'bcc' => [
            'type'       => 'text',
            'label'      => 'BCC:',
            'attributes' => [
                'class'  => 'form-control'
            ]
        ],
        'attachments' => [
            'type'       => 'hidden',
            'label'      => '<i class="fa fa-paperclip fa-lg"></i>',
            'value'      => ''
        ]
    ],
    [
        'message' => [
            'type'       => 'textarea',
            'required'   => true,
            'attributes' => [
                'rows'   => 12,
                'cols'   => 60,
                'class'  => 'form-control'
            ]
        ]
    ],
    [
        'cancel' => [
            'type'  => 'button',
            'value' => 'Cancel',
            'attributes' => [
                'class'   => 'btn btn-md btn-block btn-danger text-uppercase mail-cancel-btn',
                'onclick' => 'pop.closeMail(); return false;'
            ]
        ],
        'submit' => [
            'type'  => 'button',
            'value' => 'Send',
            'attributes' => [
                'class' => 'btn btn-md btn-block btn-save text-uppercase mail-send-btn'
            ]
        ],
        'folder' => [
            'type'  => 'hidden',
            'value' => '',
        ],
        'html' => [
            'type'  => 'hidden',
            'value' => '0',
        ],
        'mid' => [
            'type'  => 'hidden',
            'value' => '0',
        ]
    ]
];
