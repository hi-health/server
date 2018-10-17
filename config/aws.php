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
        // 正式new by Jeff
        'key' => 'AKIAJAQXAID4ZPLEENEQ',
        'secret' => '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',

        //測試 by aleiku
        //'key' => 'AKIAJZ275NZX7K2JJT5A',
        //'secret' => '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',
    ],
    'region' => env('AWS_REGION', 'us-west-2'),
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/'.AwsServiceProvider::VERSION,
    ],
    'arns' => [
        // 正式new by Jeff
        'member-gcm' => 'arn:aws:sns:us-west-2:912431903284:app/GCM/hi-health-client-android',
        'member-apn' => 'arn:aws:sns:us-west-2:573411361750:app/APNS/ApplePushService_com.hihealth.client',
        'doctor-gcm' => 'arn:aws:sns:us-west-2:912431903284:app/GCM/hi-health-doctor-android',
        'doctor-apn' => 'arn:aws:sns:us-west-2:573411361750:app/APNS/ApplePushService_com.hihealth.doctor',

        // 測試 by aleiku
        //'member-gcm' => 'arn:aws:sns:us-west-2:912431903284:app/GCM/hi-health-client-android',
        //'member-apn' => 'arn:aws:sns:us-west-2:912431903284:app/APNS/hi-health-client-ios-dev',
        //'doctor-gcm' => 'arn:aws:sns:us-west-2:912431903284:app/GCM/hi-health-doctor-android',
        //'doctor-apn' => 'arn:aws:sns:us-west-2:912431903284:app/APNS/hi-health-doctor-ios-dev',
    ],
];
