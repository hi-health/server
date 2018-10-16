<?php

namespace App\Traits;

use AWS;
use Exception;
use Log;

trait AWSSNS
{
    private $sns_handler = null;

    protected function initSNSHandler()
    {
        if (!$this->sns_handler) {
            $this->sns_handler = AWS::createClient('sns');
        }
    }

    protected function addToSNS($application_arn, $device_token)
    {
        try {
            $this->initSNSHandler();
            $attributes = [
                'CustomUserData' => '',
                'Enabled' => 'true',
                'Token' => $device_token,
            ];
            $parameter = ['PlatformApplicationArn' => $application_arn,
                'Token' => $device_token,
                'CustomUserData' => null,
                'Attributes' => $attributes,
            ];
            Log::alert($application_arn);
            Log::alert(gettype($this->sns_handler->listEndpointsByPlatformApplication(['PlatformApplicationArn' => $application_arn])));
            Log::alert('ADD SNS in 2-1');
            $endpoints = $this->sns_handler->listEndpointsByPlatformApplication(['PlatformApplicationArn' => $application_arn]);
            Log::alert('ADD SNS in 2-4');
            if (is_array($endpoints['Endpoints']) && count($endpoints['Endpoints']) > 0) {
                foreach ($endpoints['Endpoints'] as $endpoint) {
                    if ($endpoint['Attributes']['Token'] === $device_token) {
                        $endpoint_arn = $endpoint['EndpointArn'];
                        if ($endpoint['Attributes']['Enabled'] !== 'True') {
                            $this->sns_handler->setEndpointAttributes([
                                'EndpointArn' => $endpoint_arn,
                                'Attributes' => $attributes,
                            ]);
                        }
                        Log::alert('ADD SNS in 3');
                        return $endpoint_arn;
                    }
                }
            }
            $res = $this->sns_handler->createPlatformEndpoint($parameter);
            Log::alert('ADD SNS success');

            return $res['EndpointArn'];
        } catch (Exception $exception) {
            if (preg_match('~Reason: Endpoint (.+) already exists with the same Token~', $ex->getMessage(), $matches)) {
                $res = $this->sns_handler->deleteEndpoint(['EndpointArn' => $matches[1]]);
                $res = $this->sns_handler->createPlatformEndpoint($parameter);

                return $res['EndpointArn'];
            }
//            throw new Exception('Add to AWS SNS Error:'.$exception->getMessage());
            return false;
        }
    }

    protected function pushToSNS($device_arn, $message, array $extra_data = [])
    {
        try {
            $this->initSNSHandler();
            $payload = ['aps' => [
                'alert' => $message,
                'badge' => '1',
                'sound' => 'default',
            ]];

            foreach ($extra_data as $key => $value) {
                $payload[$key] = $value;
            }

            $s_mode = env('amazon_push_service_mode', 'APNS');
            $push_parameter = [
                'Message' => json_encode([
                    $s_mode => json_encode($payload),
                    'GCM' => json_encode([
                        'data' => [
                            'message' => $message,
                            'extra' => $extra_data
                        ],
                    ]),
                ]),
                'TargetArn' => $device_arn,
                'MessageStructure' => 'json',
            ];
            $this->sns_handler->publish($push_parameter);
            return true;
        } catch (Exception $exception) {
//            throw new Exception('SNS Push Error Device ARN: '.$device_arn);
            Log::alert($exception);
            return false;
        }
    }

    protected function pushMultipleToSNS($devices_arn, $message, array $extra_data = [])
    {
        foreach ($devices_arn as $device_arn) {
            $this->pushToSNS($device_arn, $message, $extra_data);
        }
    }
}
