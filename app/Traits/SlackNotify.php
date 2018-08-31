<?php

namespace App\Traits;

use SlackChat;

trait SlackNotify
{
    public function slackNotify($message, array $parameters = [])
    {
        $driver = config('exception.report.driver');
        $channel = config(
            strtr('exception.report.connections.{driver}.error_channel', [
                '{driver}' => $driver,
            ])
        );
        if (!empty($parameters)) {
            $parameters['{br}'] = "\n";
            $message = strtr($message, $parameters);
        }

        return SlackChat::message($channel, $message);
    }
}
