<?php

return [
    'report' => [
        'driver' => env('EXCEPTION_REPORT_DRIVER', 'slack'),
        'enabled' => env('EXCEPTION_REPORT_ENABLED', true), 
        'connections' => [
            'slack' => [
                'driver' => 'slack',
                'error_channel' => env('EXCEPTION_REPORT_SLACK_ERROR_CHANNEL', 'logs-apgo')
            ]
        ]
    ],
];
