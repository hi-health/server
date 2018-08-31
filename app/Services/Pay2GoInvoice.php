<?php
namespace App\Services;

use Exception;
use Illuminate\Http\Request;

class Pay2GoInvoice  extends Pay2Go{
    protected $action;

    protected $version;

    public function __construct()
    {
        $this->merchant_id = config('services.pay2go.merchant_id');
        $this->hash_key = config('services.pay2go.hash_key');
        $this->hash_iv = config('services.pay2go.hash_iv');
        $this->action = config('services.pay2go.invoice.action');
        $this->version = config('services.pay2go.invoice.version');
    }

	//====以下為副程式====
	private function addpadding($string, $blocksize = 32)
	{
		$len = strlen($string);
		$pad = $blocksize - ($len % $blocksize);
		$string .= str_repeat(chr($pad), $pad);
		return $string;
	}
	private function curl_work($url = '', $parameter = '')
	{
		$curl_options = array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => 'Google Bot',
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSL_VERIFYHOST => FALSE,
			CURLOPT_POST => '1',
			CURLOPT_POSTFIELDS => $parameter
		);
		$ch = curl_init();
		curl_setopt_array($ch, $curl_options);
		$result = curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_error = curl_errno($ch);
		curl_close($ch);
		$return_info = array(
			'url' => $url,
			'sent_parameter' => $parameter,
			'http_status' => $retcode,
			'curl_error_no' => $curl_error,
			'web_info' => $result
		);
		return $return_info;
	}
	//====以上為副程式====

	public function sendInvoiceRequest(){
		$post_data_array = array(
		 //post_data 欄位資料
			'RespondType' => 'JSON',
			'Version' => $this->version,
			'TimeStamp' => time(), //請以 time() 格式
			'TransNum' => '',
			'MerchantOrderNo' => '201409170000003',
			'BuyerName' => '王大品',
			'BuyerUBN' => '54352706',
			'BuyerAddress' => '台北市南港區南港路二段 97 號 8 樓',
			'BuyerEmail' => '54352706@pay2go.com',
			'Category' => 'B2B',
			'TaxType' => '1',
			'TaxRate' => '5',
			'Amt' => '490',
			'TaxAmt' => '10',
			'TotalAmt' => '500',
			'CarrierType' => '',
			'CarrierNum' => rawurlencode(''),
			'LoveCode' => '',
			'PrintFlag' => 'Y',
			'ItemName' => '商品一|商品二', //多項商品時，以「|」分開
			'ItemCount' => '1|2', //多項商品時，以「|」分開
			'ItemUnit' => '個|個', //多項商品時，以「|」分開
			'ItemPrice' => '300|100', //多項商品時，以「|」分開
			'ItemAmt' => '300|200', //多項商品時，以「|」分開
			'Comment' => '備註',
			'CreateStatusTime' => '',
			'Status' => '1' //1=立即開立，0=待開立，3=延遲開立
		);
		$post_data_str = http_build_query($post_data_array); //轉成字串排列
		$key = $this->hash_key; //商店專屬串接金鑰 HashKey 值
		$iv = $this->hash_iv; //商店專屬串接金鑰 HashIV 值
		if (phpversion() > 7) {
			$post_data = trim(bin2hex(openssl_encrypt($this->addpadding($post_data_str),
			'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));
			//php 7 以上版本加密
		} else {
			$post_data = trim(bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
			$this->addpadding($post_data_str), MCRYPT_MODE_CBC, $iv))); //php 7 之前版本加密
		}

		$url = $this->action;
		$MerchantID = $this->merchant_id; //商店代號
		$transaction_data_array = array(//送出欄位
		 'MerchantID_' => $MerchantID,
		 'PostData_' => $post_data
		);
		$transaction_data_str = http_build_query($transaction_data_array);
		$result = $this->curl_work($url, $transaction_data_str); //背景送出
		return $result; //印出結果

	}
}
?>