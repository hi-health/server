<?php

namespace App\Services;

use GuzzleHttp\Client;

class Pay2GoCancel extends Pay2Go
{
    protected $action;

    protected $version;

    protected $order_number;

    protected $amount;

    private $client;

    public function __construct()
    {
        $this->merchant_id = config('services.pay2go.merchant_id');
        $this->hash_key = config('services.pay2go.hash_key');
        $this->hash_iv = config('services.pay2go.hash_iv');
        $this->action = config('services.pay2go.cancel.action');
        $this->version = config('services.pay2go.cancel.version');
        $this->client = new Client([
            'base_uri' => $this->action,
        ]);
    }

    public function setOrderNumber($order_number)
    {
        $this->order_number = $order_number;

        return $this;
    }

    public function setAmount($amount)
    {
        if ($amount > 0) {
            $this->amount = $amount;
        }

        return $this;
    }

    public function send()
    {
        $post_data = $this->getPostData();
        $response = $this->client->post($this->action, [
            'form_params' => [
                'MerchantID_' => $this->merchant_id,
                'PostData_' => $post_data,
            ]
        ]);
        $contents = $response
            ->getBody()
            ->getContents();
//        $contents = '{"Status":"SUCCESS","Message":"\u653e\u68c4\u6388\u6b0a\u6210\u529f","Result":{"MerchantID":"MS31690861","Amt":1,"MerchantOrderNo":"TS17052500005","TradeNo":"17052622535236222","CheckCode":"5FF93CF071BE1D700A37396590D22252572F66CA9ED56271050C669562746A3D"}}';
        $json_data = json_decode($contents);
        $result = $json_data->Result;
        $check_data = [
            'Amt' => $result->Amt,
            'MerchantID' => $result->MerchantID,
            'MerchantOrderNo' => $result->MerchantOrderNo,
            'TradeNo' => $result->TradeNo,
        ];
        ksort($check_data);
        $check_string = 'HashIV='.$this->hash_iv.'&'.http_build_query($check_data).'&HashKey='.$this->hash_key;
        $check_code = strtoupper(
            hash('sha256', $check_string)
        );
        if ($check_code !== $result->CheckCode) {
            return false;
        }

        return (object) [
            'success' => $json_data->Status === 'SUCCESS',
            'order_number' => $result->MerchantOrderNo,
            'rawdata' => $contents,
        ];
    }

    protected function getPostData()
    {
        $query_string = http_build_query([
            'RespondType' => 'JSON',
            'Version' => $this->version,
            'Amt' => $this->amount,
            'MerchantOrderNo' => $this->order_number,
            'IndexType' => 1,
            'TimeStamp' => time(),
        ]);
        $data = $this->addPadding($query_string);

        return trim(
            bin2hex(
                openssl_encrypt($data, 'AES-256-CBC', $this->hash_key, OPENSSL_NO_PADDING, $this->hash_iv)
            )
        );
    }

    protected function addPadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);

        return $string;
    }
}
