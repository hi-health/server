<?php
namespace App\AI;
use Exception;
use Log;

class RepeatMultiDirectionAIv3_1 extends AI{



	public function __construct($template, $test, $param)
	{
		parent::__construct($template, $test, $param);
		$this->template_repeat_time = count($this->templateData['acc_x']);
	}

	protected function calMajorLevelForTemplate()
	{
		$filler = [
			'pos' => 0,
			'neg' => 0,
		];
		$sum_6axis = array_fill(   0,
									$this->template_repeat_time,
									[   'acc_x' => $filler,
										'acc_y' => $filler,
										'acc_z' => $filler,
										'acc' => $filler,
										'roll' => $filler,
										'yaw' => $filler,
										'pitch' => $filler,
										'gyro' => $filler
									]
								);

		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}

		foreach ($templateData as $axis_key => $value) {
			foreach ($value as $key1 => $v1) {
				$posArr = array_filter($v1, "_pos");
				$negArr = array_filter($v1, "_neg");

				$sum_6axis[$key1][$axis_key]['pos'] = array_sum($posArr);
				$sum_6axis[$key1][$axis_key]['neg'] = array_sum($negArr);
			}
		}

		$majorLevel_6axis = [   'acc_x' => $filler,
								'acc_y' => $filler,
								'acc_z' => $filler,
								'roll' => $filler,
								'yaw' => $filler,
								'pitch' => $filler,
							];
		
		foreach (['pos','neg'] as $sign_key) {
			for ($i=0; $i < $this->template_repeat_time; $i++) { 
				//acce
				if(	$sum_6axis[$i]['acc_x'][$sign_key] >= $sum_6axis[$i]['acc_y'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['acc_x'][$sign_key] >= $sum_6axis[$i]['acc_z'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis['acc_x'][$sign_key] += 2;
				}
				elseif(	$sum_6axis[$i]['acc_y'][$sign_key] >= $sum_6axis[$i]['acc_x'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['acc_y'][$sign_key] >= $sum_6axis[$i]['acc_z'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis['acc_y'][$sign_key] += 2;
				}
				elseif(	$sum_6axis[$i]['acc_z'][$sign_key] >= $sum_6axis[$i]['acc_x'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['acc_z'][$sign_key] >= $sum_6axis[$i]['acc_y'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis['acc_z'][$sign_key] += 2;
				}
				elseif(	$sum_6axis[$i]['acc_x'][$sign_key] <= $sum_6axis[$i]['acc_y'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['acc_x'][$sign_key] <= $sum_6axis[$i]['acc_z'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis['acc_y'][$sign_key] += 1;
					$majorLevel_6axis['acc_z'][$sign_key] += 1;
				}
				elseif(	$sum_6axis[$i]['acc_y'][$sign_key] <= $sum_6axis[$i]['acc_x'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['acc_y'][$sign_key] <= $sum_6axis[$i]['acc_z'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis['acc_x'][$sign_key] += 1;
					$majorLevel_6axis['acc_z'][$sign_key] += 1;
				}
				elseif(	$sum_6axis[$i]['acc_z'][$sign_key] <= $sum_6axis[$i]['acc_x'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['acc_z'][$sign_key] <= $sum_6axis[$i]['acc_y'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis['acc_x'][$sign_key] += 1;
					$majorLevel_6axis['acc_y'][$sign_key] += 1;
				}

				//gyro
				if(	$sum_6axis[$i]['roll'][$sign_key] >= $sum_6axis[$i]['yaw'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['roll'][$sign_key] >= $sum_6axis[$i]['pitch'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis['roll'][$sign_key] += 2;
				}
				elseif(	$sum_6axis[$i]['yaw'][$sign_key] >= $sum_6axis[$i]['roll'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['yaw'][$sign_key] >= $sum_6axis[$i]['pitch'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis['yaw'][$sign_key] += 2;
				}
				elseif(	$sum_6axis[$i]['pitch'][$sign_key] >= $sum_6axis[$i]['roll'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['pitch'][$sign_key] >= $sum_6axis[$i]['yaw'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis['pitch'][$sign_key] += 2;
				}
				elseif(	$sum_6axis[$i]['roll'][$sign_key] <= $sum_6axis[$i]['yaw'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['roll'][$sign_key] <= $sum_6axis[$i]['pitch'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis['yaw'][$sign_key] += 1;
					$majorLevel_6axis['pitch'][$sign_key] += 1;
				}
				elseif(	$sum_6axis[$i]['yaw'][$sign_key] <= $sum_6axis[$i]['roll'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['yaw'][$sign_key] <= $sum_6axis[$i]['pitch'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis['roll'][$sign_key] += 1;
					$majorLevel_6axis['pitch'][$sign_key] += 1;
				}
				elseif(	$sum_6axis[$i]['pitch'][$sign_key] <= $sum_6axis[$i]['roll'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['pitch'][$sign_key] <= $sum_6axis[$i]['yaw'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis['roll'][$sign_key] += 1;
					$majorLevel_6axis['yaw'][$sign_key] += 1;
				}

			}
		}
		return $majorLevel_6axis;
	}

	protected function calMajorLevelForTest($test_1session)
	{
		$filler = [
			'pos' => 0,
			'neg' => 0,
		];
		$sum_6axis = array_fill(   0,
									$this->test_repeat_time,
									[   'acc_x' => $filler,
										'acc_y' => $filler,
										'acc_z' => $filler,
										'acc' => $filler,
										'roll' => $filler,
										'yaw' => $filler,
										'pitch' => $filler,
										'gyro' => $filler
									]
								);

		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}

		foreach ($test_1session as $axis_key => $value) {
			foreach ($value as $key1 => $v1) {
				$posArr = array_filter($v1, "_pos");
				$negArr = array_filter($v1, "_neg");

				$sum_6axis[$key1][$axis_key]['pos'] = array_sum($posArr);
				$sum_6axis[$key1][$axis_key]['neg'] = array_sum($negArr);
			}
		}

		$majorLevel_6axis = array_fill( 0,
										$this->test_repeat_time,
										[   'acc_x' => $filler,
											'acc_y' => $filler,
											'acc_z' => $filler,
											'roll' => $filler,
											'yaw' => $filler,
											'pitch' => $filler,
										]
									);

		foreach (['pos','neg'] as $sign_key) {
			for ($i=0; $i < $this->test_repeat_time; $i++) { 
				//acce
				if( $sum_6axis[$i]['acc_x'][$sign_key] >= $sum_6axis[$i]['acc_y'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['acc_x'][$sign_key] >= $sum_6axis[$i]['acc_z'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis[$i]['acc_x'][$sign_key] += 2;
				}
				elseif( $sum_6axis[$i]['acc_y'][$sign_key] >= $sum_6axis[$i]['acc_x'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['acc_y'][$sign_key] >= $sum_6axis[$i]['acc_z'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis[$i]['acc_y'][$sign_key] += 2;
				}
				elseif( $sum_6axis[$i]['acc_z'][$sign_key] >= $sum_6axis[$i]['acc_x'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['acc_z'][$sign_key] >= $sum_6axis[$i]['acc_y'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis[$i]['acc_z'][$sign_key] += 2;
				}
				elseif( $sum_6axis[$i]['acc_x'][$sign_key] <= $sum_6axis[$i]['acc_y'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['acc_x'][$sign_key] <= $sum_6axis[$i]['acc_z'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis[$i]['acc_y'][$sign_key] += 1;
					$majorLevel_6axis[$i]['acc_z'][$sign_key] += 1;
				}
				elseif( $sum_6axis[$i]['acc_y'][$sign_key] <= $sum_6axis[$i]['acc_x'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['acc_y'][$sign_key] <= $sum_6axis[$i]['acc_z'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis[$i]['acc_x'][$sign_key] += 1;
					$majorLevel_6axis[$i]['acc_z'][$sign_key] += 1;
				}
				elseif( $sum_6axis[$i]['acc_z'][$sign_key] <= $sum_6axis[$i]['acc_x'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['acc_z'][$sign_key] <= $sum_6axis[$i]['acc_y'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis[$i]['acc_x'][$sign_key] += 1;
					$majorLevel_6axis[$i]['acc_y'][$sign_key] += 1;
				}

				//gyro
				if( $sum_6axis[$i]['roll'][$sign_key] >= $sum_6axis[$i]['yaw'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['roll'][$sign_key] >= $sum_6axis[$i]['pitch'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis[$i]['roll'][$sign_key] += 2;
				}
				elseif( $sum_6axis[$i]['yaw'][$sign_key] >= $sum_6axis[$i]['roll'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['yaw'][$sign_key] >= $sum_6axis[$i]['pitch'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis[$i]['yaw'][$sign_key] += 2;
				}
				elseif( $sum_6axis[$i]['pitch'][$sign_key] >= $sum_6axis[$i]['roll'][$sign_key]*$this->major_threshold &&
					$sum_6axis[$i]['pitch'][$sign_key] >= $sum_6axis[$i]['yaw'][$sign_key]*$this->major_threshold)
				{
					$majorLevel_6axis[$i]['pitch'][$sign_key] += 2;
				}
				elseif( $sum_6axis[$i]['roll'][$sign_key] <= $sum_6axis[$i]['yaw'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['roll'][$sign_key] <= $sum_6axis[$i]['pitch'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis[$i]['yaw'][$sign_key] += 1;
					$majorLevel_6axis[$i]['pitch'][$sign_key] += 1;
				}
				elseif( $sum_6axis[$i]['yaw'][$sign_key] <= $sum_6axis[$i]['roll'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['yaw'][$sign_key] <= $sum_6axis[$i]['pitch'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis[$i]['roll'][$sign_key] += 1;
					$majorLevel_6axis[$i]['pitch'][$sign_key] += 1;
				}
				elseif( $sum_6axis[$i]['pitch'][$sign_key] <= $sum_6axis[$i]['roll'][$sign_key]/$this->major_threshold &&
					$sum_6axis[$i]['pitch'][$sign_key] <= $sum_6axis[$i]['yaw'][$sign_key]/$this->major_threshold)
				{
					$majorLevel_6axis[$i]['roll'][$sign_key] += 1;
					$majorLevel_6axis[$i]['yaw'][$sign_key] += 1;
				}

			}
		}
		return $majorLevel_6axis;
	}
 
	protected function calPeakScale($template_1repeat_1axis,$test_1repeat_1axis)
	{
		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}

		$template_pos = array_filter($template_1repeat_1axis, "_pos");
		$template_neg = array_filter($template_1repeat_1axis, "_neg");
		$test_pos = array_filter($test_1repeat_1axis, "_pos");
		$test_neg = array_filter($test_1repeat_1axis, "_neg");
		if(!empty($template_pos)){
			$template_pos_max = max(array_filter($template_1repeat_1axis, "_pos"));
		}
		else{
			$template_pos_max = 0;
		}
		if(!empty($template_neg)){
			$template_neg_min = min(array_filter($template_1repeat_1axis, "_neg"));
		}
		else{
			$template_neg_min = 0;
		}
		if(!empty($test_pos)){
			$test_pos_max = max(array_filter($test_1repeat_1axis, "_pos"));
		}
		else{
			$test_pos_max = 0;
		}
		if(!empty($test_neg)){
			$test_neg_min = min(array_filter($test_1repeat_1axis, "_neg"));
		}
		else{
			$test_neg_min = 0;
		}

		$max = abs($template_pos_max - $test_pos_max) / max(abs($template_pos_max),10e-5);
		$min = abs($template_neg_min - $test_neg_min) / max(abs($template_neg_min),10e-5);

			
		if ($max<$this->error_threshold && $min<$this->error_threshold){
			return 2;
		}
		elseif($max<$this->error_threshold || $min<$this->error_threshold){
			return 1;
		}
		else return 0;
	}

	protected function calFeatureForTest($test_1session)
	{
		$filler = [
			'posAvg' => 0,
			'posStd' => 0,
			'negAvg' => 0,
			'negStd' => 0
		];
		$feature_6axis = array_fill(    0,
										$this->test_repeat_time,
										[   'acc_x' => $filler,
											'acc_y' => $filler,
											'acc_z' => $filler,
											'acc' => $filler,
											'roll' => $filler,
											'yaw' => $filler,
											'pitch' => $filler,
											'gyro' => $filler
										]
									);
		

		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}
		foreach ($test_1session as $axis_key => $test_1session_1axis) {
			foreach ($test_1session_1axis as $key1 => $v1) {
				$posArr = array_filter($v1, "_pos");
				$negArr = array_filter($v1, "_neg");

				
				$posStd = $this->stats_standard_deviation($posArr);
				$posAvg = array_sum($posArr)/count($posArr);
				$feature_6axis[$key1][$axis_key]['posAvg'] = $posAvg;
				$feature_6axis[$key1][$axis_key]['posStd'] = $posStd;
				
				
				$negStd = $this->stats_standard_deviation($negArr);
				$negAvg = array_sum($negArr)/count($negArr);
				$feature_6axis[$key1][$axis_key]['negAvg'] = $negAvg;
				$feature_6axis[$key1][$axis_key]['negStd'] = $negStd;
					   
			}
		}
		return $feature_6axis;
	}

	protected function calFeatureForTemplate()
	{
		$filler = [
			'posAvg' => 0,
			'posStd' => 0,
			'negAvg' => 0,
			'negStd' => 0
		];
		$feature_6axis =[
			'acc_x' => $filler,
			'acc_y' => $filler,
			'acc_z' => $filler,
			'acc' => $filler,
			'roll' => $filler,
			'yaw' => $filler,
			'pitch' => $filler,
			'gyro' => $filler
		];

		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}
		foreach ($this->templateData as $axis_key => $templateData_1axis) {
			foreach ($templateData_1axis as $key1 => $v1) {
				$posArr = array_filter($v1, "_pos");
				$negArr = array_filter($v1, "_neg");


				$posStd = $this->stats_standard_deviation($posArr);
				$posAvg = array_sum($posArr)/count($posArr);
				$feature_6axis[$axis_key]['posAvg'] += $posAvg/$this->template_repeat_time;
				$feature_6axis[$axis_key]['posStd'] += $posStd/$this->template_repeat_time;

				$negStd = $this->stats_standard_deviation($negArr);
				$negAvg = array_sum($negArr)/count($negArr);
				$feature_6axis[$axis_key]['negAvg'] += $negAvg/$this->template_repeat_time;
				$feature_6axis[$axis_key]['negStd'] += $negStd/$this->template_repeat_time;

			}
		}
		return $feature_6axis;
	}

	// v3_1 modified
	protected function validateMove($test_1session)
	{
		$peakScale_6axis = [];
		$isGoodMove = array_fill(   0,
									$this->test_repeat_time,
									[   'acce' => ['pos'=>0, 'neg'=>0],
										'gyro' => ['pos'=>0, 'neg'=>0],
									]
								);
		/*
		for ($i=0; $i < $this->test_repeat_time; $i++) { //test
			$peakScale_1test_6axis = [
				'acc_x' => 0,
				'acc_y' => 0,
				'acc_z' => 0,
				'roll' => 0,
				'yaw' => 0,
				'pitch' => 0
			];
			for ($j=0; $j < $this->template_repeat_time; $j++){ //template
				$peakScale_1test_6axis['acc_x'] += $this->calPeakScale($this->templateData['acc_x'][$j], $test_1session['acc_x'][$i]);
				$peakScale_1test_6axis['acc_y'] += $this->calPeakScale($this->templateData['acc_y'][$j], $test_1session['acc_y'][$i]);
				$peakScale_1test_6axis['acc_z'] += $this->calPeakScale($this->templateData['acc_z'][$j], $test_1session['acc_z'][$i]);
				$peakScale_1test_6axis['roll'] += $this->calPeakScale($this->templateData['roll'][$j], $test_1session['roll'][$i]);
				$peakScale_1test_6axis['yaw'] += $this->calPeakScale($this->templateData['yaw'][$j], $test_1session['yaw'][$i]);
				$peakScale_1test_6axis['pitch'] += $this->calPeakScale($this->templateData['pitch'][$j], $test_1session['pitch'][$i]);
			}
			$peakScale_6axis[] = $peakScale_1test_6axis;
		}
		*/
		$feature_test = $this->calFeatureForTest($test_1session);
		$feature_template = $this->calFeatureForTemplate();

		foreach ($feature_template as $axis_key => $template_1axis) {
			foreach ($feature_test as $key => $test_1repeat) {
				//compare
					//$test_1repeat[$axis_key]
					//$template_1axis
			}
		}

		$majorLevel = $this->calMajorLevelForTemplate();
		$majorLevel_sum = [
			'acce' => ['pos'=>0, 'neg'=>0],
			'gyro' => ['pos'=>0, 'neg'=>0],
		];
		$majorLevel_sum['acce']['pos'] = $majorLevel['acc_x']['pos'] + $majorLevel['acc_y']['pos'] + $majorLevel['acc_z']['pos'];
		$majorLevel_sum['gyro']['pos'] = $majorLevel['roll']['pos'] + $majorLevel['yaw']['pos'] + $majorLevel['pitch']['pos'];
		$majorLevel_sum['acce']['neg'] = $majorLevel['acc_x']['neg'] + $majorLevel['acc_y']['neg'] + $majorLevel['acc_z']['neg'];
		$majorLevel_sum['gyro']['neg'] = $majorLevel['roll']['neg'] + $majorLevel['yaw']['neg'] + $majorLevel['pitch']['neg'];

		$majorLevel_test = $this->calMajorLevelForTest($test_1session);
		foreach ($majorLevel_sum['acce'] as $sign_key => $v1) {
			if($v1>=6){
				if( $majorLevel['acc_x'][$sign_key] > $majorLevel_acceSum/2 ||
					($majorLevel['acc_x'][$sign_key] == 4 && $majorLevel['acc_y'][$sign_key] == 2 && $majorLevel['acc_z'][$sign_key] == 2) )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ($majorLevel_test[$i]['acc_x'][$sign_key] == 2) //Improve:這裡可以設計若等於1則給部分分數
						{
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['acc_y'][$sign_key] > $majorLevel_acceSum/2 ||
						($majorLevel['acc_y'][$sign_key] == 4 && $majorLevel['acc_x'][$sign_key] == 2 && $majorLevel['acc_z'][$sign_key] == 2) )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ($majorLevel_test[$i]['acc_y'][$sign_key] == 2)
						{
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['acc_z'][$sign_key] > $majorLevel_acceSum/2 ||
						($majorLevel['acc_z'][$sign_key] == 4 && $majorLevel['acc_x'][$sign_key] == 2 && $majorLevel['acc_y'][$sign_key] == 2) )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ($majorLevel_test[$i]['acc_z'][$sign_key] == 2)
						{
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['acc_x'][$sign_key] > $majorLevel_acceSum/3 &&
						$majorLevel['acc_y'][$sign_key] > $majorLevel_acceSum/3 )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ( $majorLevel_test[$i]['acc_x'][$sign_key] == 1 && $majorLevel_test[$i]['acc_y'][$sign_key] == 1 ) //Improve:這裡可以設計若等於2則給部分分數
						{
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['acc_x'][$sign_key] > $majorLevel_acceSum/3 &&
						$majorLevel['acc_z'][$sign_key] > $majorLevel_acceSum/3 )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ( $majorLevel_test[$i]['acc_x'][$sign_key] == 1 && $majorLevel_test[$i]['acc_z'][$sign_key] == 1 )
						{
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['acc_y'][$sign_key] > $majorLevel_acceSum/3 &&
						$majorLevel['acc_z'][$sign_key] > $majorLevel_acceSum/3 )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ( $majorLevel_test[$i]['acc_y'][$sign_key] == 1 && $majorLevel_test[$i]['acc_z'][$sign_key] == 1 )
						{
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
				else{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if( $majorLevel_test[$i]['acc_y'][$sign_key] == 0 && $majorLevel_test[$i]['acc_z'][$sign_key] == 0 && $majorLevel_test[$i]['acc_z'][$sign_key] == 0 ){
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
			}
			else{ //Improve:若template模糊，則判斷失準
				for ($i=0; $i < $this->test_repeat_time; $i++) { 
					if( $majorLevel_test[$i]['acc_y'][$sign_key] == 0 && $majorLevel_test[$i]['acc_z'][$sign_key] == 0 && $majorLevel_test[$i]['acc_z'][$sign_key] == 0 ){
							$isGoodMove[$i]['acce'][$sign_key] = 1;
					}
				}
			}
		}
		foreach ($majorLevel_sum['gyro'] as $sign_key => $v1) {
			if($v1>=6){
				if( $majorLevel['roll'][$sign_key] > $majorLevel_gyroSum/2 ||
					($majorLevel['roll'][$sign_key] == 4 && $majorLevel['yaw'][$sign_key] == 2 && $majorLevel['pitch'][$sign_key] == 2) )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ($majorLevel_test[$i]['roll'][$sign_key] == 2) //Improve:這裡可以設計若等於1則給部分分數
						{
							$isGoodMove[$i]['gyro'][$sign_key] = 1;
						}
					}
				}	//回來加玩[$sign_key]
				elseif( $majorLevel['yaw'][$sign_key] > $majorLevel_gyroSum/2 ||
						($majorLevel['yaw'][$sign_key] == 4 && $majorLevel['roll'][$sign_key] == 2 && $majorLevel['pitch'][$sign_key] == 2) )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ($majorLevel_test[$i]['yaw'][$sign_key] == 2) //Improve:這裡可以設計若等於1則給部分分數
						{
							$isGoodMove[$i]['gyro'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['pitch'][$sign_key] > $majorLevel_gyroSum/2 ||
						($majorLevel['pitch'][$sign_key] == 4 && $majorLevel['roll'][$sign_key] == 2 && $majorLevel['yaw'][$sign_key] == 2) )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ($majorLevel_test[$i]['pitch'][$sign_key] == 2) //Improve:這裡可以設計若等於1則給部分分數
						{
							$isGoodMove[$i]['gyro'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['roll'][$sign_key] > $majorLevel_gyroSum/3 &&
						$majorLevel['yaw'][$sign_key] > $majorLevel_gyroSum/3 )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ( $majorLevel_test[$i]['roll'][$sign_key] == 1 && $majorLevel_test[$i]['yaw'][$sign_key] == 1 ) //Improve:這裡可以設計若等於2則給部分分數
						{
							$isGoodMove[$i]['gyro'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['roll'][$sign_key] > $majorLevel_gyroSum/3 &&
						$majorLevel['pitch'][$sign_key] > $majorLevel_gyroSum/3 )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ( $majorLevel_test[$i]['roll'][$sign_key] == 1 && $majorLevel_test[$i]['pitch'][$sign_key] == 1 ) //Improve:這裡可以設計若等於2則給部分分數
						{
							$isGoodMove[$i]['gyro'][$sign_key] = 1;
						}
					}
				}
				elseif( $majorLevel['yaw'][$sign_key] > $majorLevel_gyroSum/3 &&
						$majorLevel['pitch'][$sign_key] > $majorLevel_gyroSum/3 )
				{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if ( $majorLevel_test[$i]['yaw'][$sign_key] == 1 && $majorLevel_test[$i]['pitch'][$sign_key] == 1 ) //Improve:這裡可以設計若等於2則給部分分數
						{
							$isGoodMove[$i]['gyro'][$sign_key] = 1;
						}
					}
				}
				else{
					for ($i=0; $i < $this->test_repeat_time; $i++) { 
						if( $majorLevel_test[$i]['roll'][$sign_key] == 0 && $majorLevel_test[$i]['yaw'][$sign_key] == 0 && $majorLevel_test[$i]['pitch'][$sign_key] == 0 ){
							$isGoodMove[$i]['acce'][$sign_key] = 1;
						}
					}
				}
			}
			else{
				for ($i=0; $i < $this->test_repeat_time; $i++) { 
					if( $majorLevel_test[$i]['roll'][$sign_key] == 0 && $majorLevel_test[$i]['yaw'][$sign_key] == 0 && $majorLevel_test[$i]['pitch'][$sign_key] == 0 ){
						$isGoodMove[$i]['acce'][$sign_key] = 1;
					}
				}
			}
		}

		return $isGoodMove;
	}	

	protected function templateMax($template_1session_1axis)
	{
		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		$tmp = [];
		foreach ($template_1session_1axis as $key => $repeat) {
			$pos = array_filter($repeat, "_pos");
			if(!empty($pos)){
				$tmp[] = max($pos);
			}
			else{
				$tmp[] = 0;
			}
		}
		return max(array_sum($tmp)/count($tmp),10e-5);  
	}

	protected function templateMin($template_1session_1axis)
	{
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}
		$tmp = [];
		foreach ($template_1session_1axis as $key => $repeat) {
			$neg = array_filter($repeat, "_neg");
			if(!empty($neg)){
				$tmp[] = min($neg);
			}
			else{
				$tmp[] = 0;
			}
		}
		return min(array_sum($tmp)/count($tmp),-10e-5);  
	}

	public function calScore()
	{

		$score = [];

		$majorLevel = $this->calMajorLevelForTemplate();
		$majorLevel_sum = [
			'acce' => ['pos'=>0, 'neg'=>0],
			'gyro' => ['pos'=>0, 'neg'=>0],
		];
		$majorLevel_sum['acce']['pos'] = $majorLevel['acc_x']['pos'] + $majorLevel['acc_y']['pos'] + $majorLevel['acc_z']['pos'];
		$majorLevel_sum['gyro']['pos'] = $majorLevel['roll']['pos'] + $majorLevel['yaw']['pos'] + $majorLevel['pitch']['pos'];
		$majorLevel_sum['acce']['neg'] = $majorLevel['acc_x']['neg'] + $majorLevel['acc_y']['neg'] + $majorLevel['acc_z']['neg'];
		$majorLevel_sum['gyro']['neg'] = $majorLevel['roll']['neg'] + $majorLevel['yaw']['neg'] + $majorLevel['pitch']['neg'];

		$majorLevel_sum = array_sum($majorLevel);
		$acc_x_weight = [
							'pos' => $majorLevel['acc_x']['pos']/$majorLevel_sum['acce']['pos'],
							'neg' => $majorLevel['acc_x']['neg']/$majorLevel_sum['acce']['neg']
						];
		$acc_y_weight = [
							'pos' => $majorLevel['acc_y']['pos']/$majorLevel_sum['acce']['pos'],
							'neg' => $majorLevel['acc_y']['neg']/$majorLevel_sum['acce']['neg']
						];
		$acc_z_weight = [
							'pos' => $majorLevel['acc_z']['pos']/$majorLevel_sum['acce']['pos'],
							'neg' => $majorLevel['acc_z']['neg']/$majorLevel_sum['acce']['neg']
						];
		$roll_weight = [
							'pos' => $majorLevel['roll']['pos']/$majorLevel_sum['gyro']['pos'],
							'neg' => $majorLevel['roll']['neg']/$majorLevel_sum['gyro']['neg']
						];
		$yaw_weight = [
							'pos' => $majorLevel['yaw']['pos']/$majorLevel_sum['gyro']['pos'],
							'neg' => $majorLevel['yaw']['neg']/$majorLevel_sum['gyro']['neg']
						];
		$pitch_weight = [
							'pos' => $majorLevel['pitch']['pos']/$majorLevel_sum['gyro']['pos'],
							'neg' => $majorLevel['pitch']['neg']/$majorLevel_sum['gyro']['neg']
						];

		function _pos($var){
			if($var>=0){
				return true;
			}else{
				return false;
			}
		}
		function _neg($var){
			if($var<0){
				return true;
			}else{
				return false;
			}
		}

		for ($i=0; $i < $this->session; $i++) { 
			$score_1session = [
				'acc_x' => 0,
				'acc_y' => 0,
				'acc_z' => 0,
				'roll' => 0,
				'yaw' => 0,
				'pitch' => 0,
				'acce_goodMove' => 0,
				'gyro_goodMove' => 0
			];

			$isGoodMove = $this->validateMove($this->testData[$i]);
			
			//template的max,min用平均
			//test用for
			for ($j=0; $j < $this->test_repeat_time; $j++) { 
				$max_acc_x_test = max(array_filter($this->testData[$i]['acc_x'][$j], "_pos"));
				$min_acc_x_test = min(array_filter($this->testData[$i]['acc_x'][$j], "_neg"));
				$max_acc_y_test = max(array_filter($this->testData[$i]['acc_y'][$j], "_pos"));
				$min_acc_y_test = min(array_filter($this->testData[$i]['acc_y'][$j], "_neg"));
				$max_acc_z_test = max(array_filter($this->testData[$i]['acc_z'][$j], "_pos"));
				$min_acc_z_test = min(array_filter($this->testData[$i]['acc_z'][$j], "_neg"));
				$max_roll_test = max(array_filter($this->testData[$i]['roll'][$j], "_pos"));
				$min_roll_test = min(array_filter($this->testData[$i]['roll'][$j], "_neg"));
				$max_yaw_test = max(array_filter($this->testData[$i]['yaw'][$j], "_pos"));
				$min_yaw_test = min(array_filter($this->testData[$i]['yaw'][$j], "_neg"));
				$max_pitch_test = max(array_filter($this->testData[$i]['pitch'][$j], "_pos"));
				$min_pitch_test = min(array_filter($this->testData[$i]['pitch'][$j], "_neg"));
				

				$acc_x_max_ratio = abs($max_acc_x_test-$this->templateMax($this->templateData['acc_x']))/abs($this->templateMax($this->templateData['acc_x']));
				$acc_x_min_ratio = abs($min_acc_x_test-$this->templateMin($this->templateData['acc_x']))/abs($this->templateMin($this->templateData['acc_x']));

				$acc_y_max_ratio = abs($max_acc_y_test-$this->templateMax($this->templateData['acc_y']))/abs($this->templateMax($this->templateData['acc_y']));
				$acc_y_min_ratio = abs($min_acc_y_test-$this->templateMin($this->templateData['acc_y']))/abs($this->templateMin($this->templateData['acc_y']));

				$acc_z_max_ratio = abs($max_acc_z_test-$this->templateMax($this->templateData['acc_z']))/abs($this->templateMax($this->templateData['acc_z']));
				$acc_z_min_ratio = abs($min_acc_z_test-$this->templateMin($this->templateData['acc_z']))/abs($this->templateMin($this->templateData['acc_z']));

				$roll_max_ratio = abs($max_roll_test-$this->templateMax($this->templateData['roll']))/abs($this->templateMax($this->templateData['roll']));
				$roll_min_ratio = abs($min_roll_test-$this->templateMin($this->templateData['roll']))/abs($this->templateMin($this->templateData['roll']));

				$yaw_max_ratio = abs($max_yaw_test-$this->templateMax($this->templateData['yaw']))/abs($this->templateMax($this->templateData['yaw']));
				$yaw_min_ratio = abs($min_yaw_test-$this->templateMin($this->templateData['yaw']))/abs($this->templateMin($this->templateData['yaw']));

				$pitch_max_ratio = abs($max_pitch_test-$this->templateMax($this->templateData['pitch']))/abs($this->templateMax($this->templateData['pitch']));
				$pitch_min_ratio = abs($min_pitch_test-$this->templateMin($this->templateData['pitch']))/abs($this->templateMin($this->templateData['pitch']));


			
				$score_1session['acce_goodMove'] += 50/2/2*$isGoodMove[$j]['acce']['pos']/$this->test_repeat_time;
				$score_1session['acce_goodMove'] += 50/2/2*$isGoodMove[$j]['acce']['neg']/$this->test_repeat_time;
				$score_1session['gyro_goodMove'] += 50/2/2*$isGoodMove[$j]['gyro']['pos']/$this->test_repeat_time;
				$score_1session['gyro_goodMove'] += 50/2/2*$isGoodMove[$j]['gyro']['neg']/$this->test_repeat_time;

				if($isGoodMove[$j]['acce']['pos']!=0){
					$score_1session['acc_x']+= 50 * $acc_x_weight['pos'] * (1-$acc_x_max_ratio)/2 /$this->test_repeat_time;
					$score_1session['acc_y']+= 50 * $acc_y_weight['pos'] * (1-$acc_y_max_ratio)/2 /$this->test_repeat_time;
					$score_1session['acc_z']+= 50 * $acc_z_weight['pos'] * (1-$acc_z_max_ratio)/2 /$this->test_repeat_time;
				}
				if($isGoodMove[$j]['acce']['neg']!=0){
					$score_1session['acc_x']+= 50 * $acc_x_weight['neg'] * (1-$acc_x_min_ratio)/2 /$this->test_repeat_time;
					$score_1session['acc_y']+= 50 * $acc_y_weight['neg'] * (1-$acc_y_min_ratio)/2 /$this->test_repeat_time;
					$score_1session['acc_z']+= 50 * $acc_z_weight['neg'] * (1-$acc_z_min_ratio)/2 /$this->test_repeat_time;
				}
				if($isGoodMove[$j]['gyro']['pos']!=0){
					$score_1session['roll']+= 50 * $roll_weight['pos'] * (1-$roll_max_ratio)/2 /$this->test_repeat_time;
					$score_1session['yaw']+= 50 * $yaw_weight['pos'] * (1-$yaw_max_ratio)/2 /$this->test_repeat_time;
					$score_1session['pitch']+= 50 * $pitch_weight['pos'] * (1-$pitch_max_ratio)/2 /$this->test_repeat_time;
				}
				if($isGoodMove[$j]['gyro']['neg']!=0){
					$score_1session['roll']+= 50 * $roll_weight['neg'] * (1-$roll_min_ratio)/2 /$this->test_repeat_time;
					$score_1session['yaw']+= 50 * $yaw_weight['neg'] * (1-$yaw_min_ratio)/2 /$this->test_repeat_time;
					$score_1session['pitch']+= 50 * $pitch_weight['neg'] * (1-$pitch_min_ratio)/2 /$this->test_repeat_time;
				}

				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $acc_x_max_ratio: '.strval(round($acc_x_max_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $acc_x_min_ratio: '.strval(round($acc_x_min_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $acc_y_max_ratio: '.strval(round($acc_y_max_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $acc_y_min_ratio: '.strval(round($acc_y_min_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $acc_z_max_ratio: '.strval(round($acc_z_max_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $acc_z_min_ratio: '.strval(round($acc_z_min_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $roll_max_ratio: '.strval(round($roll_max_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $roll_min_ratio: '.strval(round($roll_min_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $yaw_max_ratio: '.strval(round($yaw_max_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $yaw_min_ratio: '.strval(round($yaw_min_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $pitch_max_ratio: '.strval(round($pitch_max_ratio,3)));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $pitch_min_ratio: '.strval(round($pitch_min_ratio,3)));

			}

			Log::debug('AI session: '.strval($i).'  $acc_x_weight["pos"]: '.strval(round($acc_x_weight["pos"],3)));
			Log::debug('AI session: '.strval($i).'  $acc_y_weight["pos"]: '.strval(round($acc_y_weight["pos"],3)));
			Log::debug('AI session: '.strval($i).'  $acc_z_weight["pos"]: '.strval(round($acc_z_weight["pos"],3)));
			Log::debug('AI session: '.strval($i).'  $roll_weight["pos"]: '.strval(round($roll_weight["pos"],3)));
			Log::debug('AI session: '.strval($i).'  $yaw_weight["pos"]: '.strval(round($yaw_weight["pos"],3)));
			Log::debug('AI session: '.strval($i).'  $pitch_weight["pos"]: '.strval(round($pitch_weight["pos"],3)));
			Log::debug('AI session: '.strval($i).'  $acc_x_weight["neg"]: '.strval(round($acc_x_weight["neg"],3)));
			Log::debug('AI session: '.strval($i).'  $acc_y_weight["neg"]: '.strval(round($acc_y_weight["neg"],3)));
			Log::debug('AI session: '.strval($i).'  $acc_z_weight["neg"]: '.strval(round($acc_z_weight["neg"],3)));
			Log::debug('AI session: '.strval($i).'  $roll_weight["neg"]: '.strval(round($roll_weight["neg"],3)));
			Log::debug('AI session: '.strval($i).'  $yaw_weight["neg"]: '.strval(round($yaw_weight["neg"],3)));
			Log::debug('AI session: '.strval($i).'  $pitch_weight["neg"]: '.strval(round($pitch_weight["neg"],3)));

			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_x\']: '.strval(round($score_1session['acc_x'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_y\']: '.strval(round($score_1session['acc_y'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_z\']: '.strval(round($score_1session['acc_z'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'roll\']: '.strval(round($score_1session['roll'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'yaw\']: '.strval(round($score_1session['yaw'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'pitch\']: '.strval(round($score_1session['pitch'])));
			
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acce_goodMove\']: '.strval(round($score_1session['acce_goodMove'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'gyro_goodMove\']: '.strval(round($score_1session['gyro_goodMove'])));
			Log::debug('AI session: '.strval($i).'  $score_1session: '.strval(round(array_sum($score_1session))));
			$score[] = round(array_sum($score_1session));
		
		}
		return $score;
	}
}

?>