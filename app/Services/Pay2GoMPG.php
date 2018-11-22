<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;

class Pay2GoMPG extends Pay2Go
{
    protected $action;

    protected $version;

    protected $return_url;

    protected $order_comment;

    public function __construct()
    {
        parent::__construct();
        $this->action = config('services.pay2go.mpg.action');
        $this->version = config('services.pay2go.mpg.version');
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

    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;

        return $this;
    }

    public function setOrderComment($order_comment)
    {
        $this->order_comment = $order_comment;

        return $this;
    }

    public function getPurchaseForm()
    {
        if (empty($this->order_number)) {
            $this->exception('Order number is empty');
        }
        if (empty($this->amount)) {
            $this->exception('Amount is empty');
        }
        if (empty($this->return_url)) {
            $this->exception('Return URL is empty');
        }
        $parameters = [
            'MerchantID' => $this->merchant_id,
            'TimeStamp' => time(),
            'Version' => $this->version,
            'MerchantOrderNo' => $this->order_number,
            'Amt' => $this->amount,
        ];
        ksort($parameters);
        $query_string = 'HashKey='.$this->hash_key.'&'.http_build_query($parameters).'&HashIV='.$this->hash_iv;
        $check_value = strtoupper(hash('sha256', $query_string));
        $parameters = array_merge($parameters, [
            'RespondType' => 'JSON',
            'LangType' => 'zh-tw',
            'ItemDesc' => 'Service Charge',
            'ReturnURL' => $this->return_url,
            'LoginType' => '0',
            'CREDIT' => '1',
            'OrderComment' => $this->order_comment,
            'CheckValue' => $check_value,
            'UNIONPAY' => 0, //正式版才能用（商店後台也需要啟用）
        ]);

        return view('purchase_form', [
            'action' => $this->action,
            'parameters' => $parameters,
            'order_number' => $this->order_number,
            'amount' => $this->amount,
        ])->render();
    }

    public function isReturnSuccess(Request $request)
    {
        $json_data = json_decode($request->input('JSONData'));
        if ($json_data->Status === 'SUCCESS') {
            return true;
        }

        return false;
    }

    public function checkNotify(Request $request)
    {
        $json_data = json_decode($request->input('JSONData'));
        $result = json_decode($json_data->Result);
        $check_data = [
            'MerchantID' => $result->MerchantID,
            'Amt' => $result->Amt,
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
        ];
    }
}
