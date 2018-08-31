<?php

	function upload_url($file_name)
	{
		return asset("uploads/" . $file_name);
	}
	function send_sms($phone, $message)
	{
		$host = "https://api.kotsms.com.tw/kotsmsapi-1.php";

		$url = sprintf("%s?username=jan1112&password=5402&dstaddr=%s&smbody=%s",
					$host, $phone, iconv("UTF-8","big5", $message));

		if (! $getfile = file($url))
		{
        	return false;
    	}
    	$term_tmp = implode ('', $getfile);
    	$term = $term_tmp;
    	return $term;
	}

	function download_remote_file($fromUrl) 
	{
		return false;
	    try 
	    {
	    	if (! is_dir(public_path('uploads')))
	    	{
	    		mkdir(public_path('uploads'));
	    	}

	    	$s_filename = public_path('uploads') . DIRECTORY_SEPARATOR . time();

			$o_file = fopen($s_filename, 'w');
			
	        $client = new \GuzzleHttp\Client();
	        $response = $client->get($fromUrl,
	        						['save_to' => $o_file]);

	        $s_f_filename = md5_file($s_filename);

	        rename ( $s_filename , public_path('uploads') . DIRECTORY_SEPARATOR . $s_f_filename);
	        return $s_f_filename;
	    } 
	    catch (Exception $e) 
	    {
	        return false;
	    }
	}
	function get_json_from_response($response)
	{
		return json_decode($response -> content(), true);
	}
	function convert_code_2_reason($i_code)
	{
		try 
		{

			return array(101 => '這個請求不被允許this request is not allow.',
						 102 => '缺少參數',
						 200 => 'SUCCESS', 

						 //權限
						 300 => '此帳號需要先註冊', 
						 301 => '帳號已存在',
						 302 => '帳號或密碼錯誤',
						 303 => '身分證/居留證號或生日錯誤',
						 304 => '簡訊驗證碼錯誤',
						 305 => '登入資訊過期',
						 306 => '此電話號碼的帳號不存在',
						 307 => '帳號尚未開通',
						 308 => '密碼不正確',
						 
						 501 => 'group not exists.',
						 502 => 'you don\' have this group permission.',
						 503 => 'question is not already.',
						 504 => 'already play this game.',
						 403 => 'token expired.',
						 505 => 'already share.',

						 //DB相關
						 600 => 'can not find data',
						 601 => 'can not find product')[$i_code];
		}
		catch (Exception $e)
		{
			return 'Unexcept Error';
		}
	}
	function make_datatable_response($arr_data, $i_page, $i_total, $i_filter_count = -1)
	{
		$output = [ "draw"            => $i_page,
					"recordsTotal"    => $i_total,
					"recordsFiltered" => $i_filter_count == -1 ? $i_total : $i_filter_count,
  					"data"            => $arr_data
  				  ];
		return $output;
	}
	function wm_response($i_code, $arr_data = null, $s_reason = null)
	{
		$arr_response = ['code'   => $i_code,
					     'reason' => $s_reason == null ? convert_code_2_reason($i_code) : $s_reason, 
					     'data'   => $arr_data];

		return $arr_response;
	}

	 /**
	 *
	 * @package		Debug helpers
	 * @author		Feng 2013
	 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
	 * @since		Version 1.0
	 */
	 
	if ( ! function_exists('debug'))
	{
		/**
		 * Debug
		 *
		 * Lets you output variable to html page
		 *
		 * @param	mixed
		 * @param	string  section name
		 * @param	string  function owner
		 */
		function debug($mixed, $title = null , $owner = null)
		{
			$title = $title != '' ? preg_replace('/:/', '', $title). '::' : '';
			
			print '<div style="border-style:solid;border-color:red;">';
			
			if ($owner != null)
			{
				print '<strong>' . ucfirst($owner). ' Debug Area</strong>';
			}
			print '<br /><strong style=\'color:red\'>' . ucfirst($title) .'</strong><br />';
			print '<br /><pre style=margin-left:30px>' . print_r($mixed, true) . '</pre>';
			print '</div>';
		}
	}
	if ( ! function_exists('feng'))
	{
		function feng($mixed, $title = null, $stop = false)
		{
			debug($mixed, $title, 'feng'); if ($stop) die();
		}
	}

	// if ( ! function_exists('get_user_ip'))
	// {
	//  	function get_user_ip()
	//  	{
	//  	 return config_item('log_has_load_balancer') ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	//  	}
	// } 

	/**
	 * Calculates the great-circle distance between two points, with
	 * the Vincenty formula.
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $latitudeTo Latitude of target point in [deg decimal]
	 * @param float $longitudeTo Longitude of target point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	 */
	function coordinate_to_meter( $latitudeFrom, $longitudeFrom, 
									  $latitudeTo, $longitudeTo, 
									  $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);

	  $lonDelta = $lonTo - $lonFrom;
	  $a = pow(cos($latTo) * sin($lonDelta), 2) +
	    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
	  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

	  $angle = atan2(sqrt($a), $b);
	  return $angle * $earthRadius;
	}
?>