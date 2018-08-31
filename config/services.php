<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'smexpress' => [
        'host' => env('SMEXPRESS_HOST'),
        'username' => env('SMEXPRESS_USERNAME'),
        'password' => env('SMEXPRESS_PASSWORD'),
    ],

    'pay2go' => [
        'merchant_id' => env('PAY2GO_MERCHANT_ID'),
        'hash_key' => env('PAY2GO_HASH_KEY'),
        'hash_iv' => env('PAY2GO_HASH_IV'),
        'mpg' => [
            'action' => env('PAY2GO_MPG_ACTION'),
            'version' => env('PAY2GO_MPG_VERSION'),
        ],
        'cancel' => [
            'action' => env('PAY2GO_CANCEL_ACTION'),
            'version' => env('PAY2GO_CANCEL_VERSION')
        ],
        'invoice' => [
            'action' => env('PAY2GO_INVOICE_ACTION'),
            'version' => env('PAY2GO_INVOICE_VERSION')
        ]
    ],
    'slack' => [
        'token' => 'xoxb-188816011079-HRJ5FE46WElbXDaFhxjidrLB',
    ],
];
