<?php

namespace App\Services;

use App\Traits\SlackNotify;
use GuzzleHttp\Client;

class MitakeSmexpress
{
    use SlackNotify;

    protected $client;

    protected $username;

    protected $password;

    protected $host;

    public function __construct()
    {
        $this->host = config('services.smexpress.host');
        $this->username = config('services.smexpress.username');
        $this->password = config('services.smexpress.password');
        $this->client = new Client([
            'base_uri' => $this->host,
        ]);
    }

    public function send($phone, $name, $message, $client_id = null)
    {
        $response = $this->client->get('SmSendGet.asp', [
            'query' => [
                'username' => $this->username,
                'password' => $this->password,
                'dstaddr' => $phone, // 受訊方手機號碼(必填)
                'destname' => $name, // 收訊人名稱, 可填入來源系統所產生的Key值, 以對應回來源資料庫
                'dlvtime' => '', // 簡訊預約時間, 空白則為即時簡訊
                'vldtime' => '', // 簡訊有效期限
                'encoding' => 'UTF-8',
                'smbody' => $message, // 簡訊內容(必填)
                'response' => '', // 狀態回報網址
                'clientid' => $client_id, // 客戶簡訊ID, 依該ID判斷是否發送過, 若有直接回覆之前的回覆值, 並加上Duplicate=Y; 只留存12小時
                'CharsetURL' => '', // url encode前, smbody及DestName的編碼方式
            ],
        ]);
        $contents = $response->getBody()->getContents();
        $result = explode("\r\n", $contents);
        if (isset($result[2]) and $result[2] === 'statuscode=1') {
            $this->slackNotify('發送簡訊給{phone}，結果成功{br}，內容是：{br}{message}', [
                '{phone}' => $phone,
                '{message}' => $message,
            ]);
            return true;
        }
        $this->slackNotify('發送簡訊給{phone}，結果失敗{br}，原因是：{br}{result}', [
            '{phone}' => $phone,
            '{result}' => iconv('BIG-5', 'UTF-8', $contents),
        ]);

        return false;
    }
}
