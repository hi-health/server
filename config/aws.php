<?php

use Aws\Laravel\AwsServiceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */
    'credentials' => [
        'key' => 'AKIAJK2THS3V4RBNXYKQ',
        'secret' => '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',
    ],
    'region' => env('AWS_REGION', 'us-west-2'),
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/'.AwsServiceProvider::VERSION,
    ],
    'arns' => [
        'member-gcm' => 'arn:aws:sns:us-west-2:912431903284:app/GCM/hi-health-client-android',
        'member-apn' => 'arn:aws:sns:us-west-2:912431903284:app/APNS/hi-health-client-ios',
        'doctor-gcm' => 'arn:aws:sns:us-west-2:912431903284:app/GCM/hi-health-doctor-android',
        'doctor-apn' => 'arn:aws:sns:us-west-2:912431903284:app/APNS/hi-health-doctor-ios',
    ],
];
