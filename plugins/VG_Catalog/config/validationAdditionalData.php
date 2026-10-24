<?php

global $orderConfig;
$orderConfig = [
    'required' => [
        'name',
        'phone',
    ],
    'additional' => [
        'required' => [
            'email'
        ],
    ]
];