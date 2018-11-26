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

		$feature_template = $this->calFeatureForTemplate();

		$majorLevel_6axis = [   'acc_x' => $filler,
								'acc_y' => $filler,
								'acc_z' => $filler,
								'roll' => $filler,
								'yaw' => $filler,
								'pitch' => $filler,
							];

		foreach (['pos','neg'] as $sign_key) {
			foreach (['acc_x','acc_y','acc_z'] as $axis_key) {
				if(abs($feature_template['acc']['pos']['avg']) != 0){
					if(abs($feature_template[$axis_key][$sign_key]['avg'])/abs($feature_template['acc']['pos']['avg']) > 1/sqrt(2)){
						$majorLevel_6axis[$axis_key][$sign_key] += 2;
					}
					elseif(abs($feature_template[$axis_key][$sign_key]['avg'])/abs($feature_template['acc']['pos']['avg']) > 1/sqrt(3)){
						$majorLevel_6axis[$axis_key][$sign_key] += 1;
					}
				}
			}
			foreach (['roll','yaw','pitch'] as $axis_key) {
				if(abs($feature_template['gyro']['pos']['avg']) != 0){
					if(abs($feature_template[$axis_key][$sign_key]['avg'])/abs($feature_template['gyro']['pos']['avg']) > 1/sqrt(2)){
						$majorLevel_6axis[$axis_key][$sign_key] += 2;
					}
					elseif(abs($feature_template[$axis_key][$sign_key]['avg'])/abs($feature_template['gyro']['pos']['avg']) > 1/sqrt(3)){
						$majorLevel_6axis[$axis_key][$sign_key] += 1;
					}
				}
			}
		}
		return $majorLevel_6axis;
	}

	protected function calFeatureForTest($test_1session)
	{
		$filler = [
			'pos' => ['avg'=>0, 'std'=>0],
			'neg' => ['avg'=>0, 'std'=>0]
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
		
		foreach ($test_1session as $axis_key => $test_1session_1axis) {
			foreach ($test_1session_1axis as $key1 => $v1) {
				$posArr = array_filter($v1, array($this, '_pos'));
				$negArr = array_filter($v1, array($this, '_neg'));

				if(!empty($posArr)){
					$posStd = $this->stats_standard_deviation($posArr);
					$posAvg = array_sum($posArr)/count($posArr);
					$feature_6axis[$key1][$axis_key]['pos']['avg'] = $posAvg;
					$feature_6axis[$key1][$axis_key]['pos']['std'] = $posStd;
				}
				else{
					$feature_6axis[$key1][$axis_key]['pos']['avg'] = 0;
					$feature_6axis[$key1][$axis_key]['pos']['std'] = 0;
				}
				
				if(!empty($negArr)){
					$negStd = $this->stats_standard_deviation($negArr);
					$negAvg = array_sum($negArr)/count($negArr);
					$feature_6axis[$key1][$axis_key]['neg']['avg'] = $negAvg;
					$feature_6axis[$key1][$axis_key]['neg']['std'] = $negStd;
				}
				else{
					$feature_6axis[$key1][$axis_key]['neg']['avg'] = 0;
					$feature_6axis[$key1][$axis_key]['neg']['std'] = 0;
				}
				//$feature_6axis[$key1][$axis_key]['pos']['avg'] = $this->cutOffNum($feature_6axis[$key1][$axis_key]['pos']['avg'], -0.05, 0.05);
				//$feature_6axis[$key1][$axis_key]['pos']['std'] = $this->cutOffNum($feature_6axis[$key1][$axis_key]['pos']['std'], -0.05, 0.05);
				//$feature_6axis[$key1][$axis_key]['neg']['avg'] = $this->cutOffNum($feature_6axis[$key1][$axis_key]['neg']['avg'], -0.05, 0.05);
				//$feature_6axis[$key1][$axis_key]['neg']['std'] = $this->cutOffNum($feature_6axis[$key1][$axis_key]['neg']['std'], -0.05, 0.05);	   
				Log::debug('test repeat: '.$key1.'  $feature_6axis['.$axis_key.']["pos"]["avg"]: '.strval(round($feature_6axis[$key1][$axis_key]['pos']['avg'],3)));
				Log::debug('test repeat: '.$key1.'  $feature_6axis['.$axis_key.']["pos"]["std"]: '.strval(round($feature_6axis[$key1][$axis_key]['pos']['std'],3)));
				Log::debug('test repeat: '.$key1.'  $feature_6axis['.$axis_key.']["neg"]["avg"]: '.strval(round($feature_6axis[$key1][$axis_key]['neg']['avg'],3)));
				Log::debug('test repeat: '.$key1.'  $feature_6axis['.$axis_key.']["neg"]["std"]: '.strval(round($feature_6axis[$key1][$axis_key]['neg']['std'],3)));
			}

		}
		/*
		for ($i=0; $i < $this->test_repeat_time; $i++) {
			foreach (['acc_x','acc_y','acc_z'] as $axis_key) {
				$feature_6axis[$i][$axis_key]['pos']['avg'] = $feature_6axis[$i][$axis_key]['pos']['avg']/$feature_6axis[$i]['acc']['pos']['avg'];
				$feature_6axis[$i][$axis_key]['pos']['std'] = $feature_6axis[$i][$axis_key]['pos']['std']/$feature_6axis[$i]['acc']['pos']['std'];
				$feature_6axis[$i][$axis_key]['neg']['avg'] = $feature_6axis[$i][$axis_key]['neg']['avg']/$feature_6axis[$i]['acc']['pos']['avg'];
				$feature_6axis[$i][$axis_key]['neg']['std'] = $feature_6axis[$i][$axis_key]['neg']['std']/$feature_6axis[$i]['acc']['pos']['std'];
			}
			foreach (['roll','yaw','pitch'] as $axis_key) {
				$feature_6axis[$i][$axis_key]['pos']['avg'] = $feature_6axis[$i][$axis_key]['pos']['avg']/$feature_6axis[$i]['gyro']['pos']['avg'];
				$feature_6axis[$i][$axis_key]['pos']['std'] = $feature_6axis[$i][$axis_key]['pos']['std']/$feature_6axis[$i]['gyro']['pos']['std'];
				$feature_6axis[$i][$axis_key]['neg']['avg'] = $feature_6axis[$i][$axis_key]['neg']['avg']/$feature_6axis[$i]['gyro']['pos']['avg'];
				$feature_6axis[$i][$axis_key]['neg']['std'] = $feature_6axis[$i][$axis_key]['neg']['std']/$feature_6axis[$i]['gyro']['pos']['std'];
			}
		}
		*/
		return $feature_6axis;
	}

	protected function calFeatureForTemplate()
	{
		$filler = [
			'pos' => ['avg'=>0, 'std'=>0],
			'neg' => ['avg'=>0, 'std'=>0]
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

		foreach ($this->templateData as $axis_key => $templateData_1axis) {
			foreach ($templateData_1axis as $key1 => $v1) {
				$posArr = array_filter($v1, array($this, '_pos'));
				$negArr = array_filter($v1, array($this, '_neg'));

				if(!empty($posArr)){
					$posStd = $this->stats_standard_deviation($posArr);
					$posAvg = array_sum($posArr)/count($posArr);
					$feature_6axis[$axis_key]['pos']['avg'] += $posAvg/$this->template_repeat_time;
					$feature_6axis[$axis_key]['pos']['std'] += $posStd/$this->template_repeat_time;
				}
				
				if(!empty($negArr)){
					$negStd = $this->stats_standard_deviation($negArr);
					$negAvg = array_sum($negArr)/count($negArr);
					$feature_6axis[$axis_key]['neg']['avg'] += $negAvg/$this->template_repeat_time;
					$feature_6axis[$axis_key]['neg']['std'] += $negStd/$this->template_repeat_time;
				}
			}
			//$feature_6axis[$axis_key]['pos']['avg'] = $this->cutOffNum($feature_6axis[$axis_key]['pos']['avg'], -0.05, 0.05);
			//$feature_6axis[$axis_key]['pos']['std'] = $this->cutOffNum($feature_6axis[$axis_key]['pos']['std'], -0.05, 0.05);
			//$feature_6axis[$axis_key]['neg']['avg'] = $this->cutOffNum($feature_6axis[$axis_key]['neg']['avg'], -0.05, 0.05);
			//$feature_6axis[$axis_key]['neg']['std'] = $this->cutOffNum($feature_6axis[$axis_key]['neg']['std'], -0.05, 0.05);
			Log::debug('template: $feature_6axis['.$axis_key.']["pos"]["avg"]: '.strval(round($feature_6axis[$axis_key]['pos']['avg'],3)));
			Log::debug('template: $feature_6axis['.$axis_key.']["pos"]["std"]: '.strval(round($feature_6axis[$axis_key]['pos']['std'],3)));
			Log::debug('template: $feature_6axis['.$axis_key.']["neg"]["avg"]: '.strval(round($feature_6axis[$axis_key]['neg']['avg'],3)));
			Log::debug('template: $feature_6axis['.$axis_key.']["neg"]["std"]: '.strval(round($feature_6axis[$axis_key]['neg']['std'],3)));
		}
		return $feature_6axis;
	}

	protected function checkGoodMove($f_template, $f_test, $threshold){
		if($f_template['avg'] == 0){
			if($f_template['std'] == 0){
				Log::debug('1');
				if(	$f_test['avg'] < 0.1 &&
					$f_test['avg'] > -0.1 &&
					$f_test['std'] < 0.1 &&
					$f_test['std'] > -0.1
				){
					return true;
				}
				else return false;
			}
			else{
				Log::debug('2');
				if(	$f_test['std']/$f_template['std'] > $threshold &&
					$f_test['std']/$f_template['std'] < 1/$threshold &&
					$f_test['avg'] < 0.1 &&
					$f_test['avg'] > -0.1
				){
					return true;
				}
				else return false;
			}
		}
		else{
			if($f_template['std'] == 0){
				Log::debug('3');
				if(	$f_test['avg']/$f_template['avg'] > $threshold &&
				$f_test['avg']/$f_template['avg'] < 1/$threshold &&
					$f_test['std'] < 0.1 &&
					$f_test['std'] > -0.1
				){
					return true;
				}
				else return false;
			}
			else{
				Log::debug('4');
				if(	
					(
						($f_test['avg']/$f_template['avg'] > $threshold && $f_test['avg']/$f_template['avg'] < 1/$threshold) ||
						abs($f_test['avg']-$f_template['avg']) < 0.15
					) 
					&&
					(
						($f_test['std']/$f_template['std'] > $threshold && $f_test['std']/$f_template['std'] < 1/$threshold) ||
						abs($f_test['std']-$f_template['std']) < 0.15
					) 
				){
					return true;
				}
				else return false;
			}
		}
	}
	// v3_1 modified
	protected function validateMove($test_1session)
	{
		$peakScale_6axis = [];
		$importantAxisMap = array_fill(   0,
									$this->test_repeat_time,
									[   'acce' => ['pos'=>0, 'neg'=>0],
										'gyro' => ['pos'=>0, 'neg'=>0],
									]
								);
		$goodMoveMap = array_fill(   0,
									$this->test_repeat_time,
									[   'acce' => ['pos'=>0, 'neg'=>0],
										'gyro' => ['pos'=>0, 'neg'=>0],
									]
								);
		$isGoodMove = array_fill(   0,
									$this->test_repeat_time,
									[   'acce' => ['pos'=>0, 'neg'=>0],
										'gyro' => ['pos'=>0, 'neg'=>0],
									]
								);
		$printGoodMove = array_fill(   0,
									$this->test_repeat_time,
									[   'acce' => ['pos'=>[0,0,0], 'neg'=>[0,0,0]],
										'gyro' => ['pos'=>[0,0,0], 'neg'=>[0,0,0]],
									]
								);

		
		$feature_test = $this->calFeatureForTest($test_1session);
		$feature_template = $this->calFeatureForTemplate();

		foreach (['pos','neg'] as $sign_key) {
			foreach ($feature_test as $key => $test_1repeat) {
				//compare
					//$test_1repeat[$axis_key][$sign_key]
					//$feature_template[$axis_key][$sign_key]
				$threshold = 0.5;

				if($this->checkGoodMove($feature_template['acc_x'][$sign_key], $test_1repeat['acc_x'][$sign_key], $threshold)){
					$isGoodMove[$key]['acce'][$sign_key] += 0.34;
					$printGoodMove[$key]['acce'][$sign_key][0] = 1;
				}
				if($this->checkGoodMove($feature_template['acc_y'][$sign_key], $test_1repeat['acc_y'][$sign_key], $threshold)){
					$isGoodMove[$key]['acce'][$sign_key] += 0.34;
					$printGoodMove[$key]['acce'][$sign_key][1] = 1;
				}
				if($this->checkGoodMove($feature_template['acc_z'][$sign_key], $test_1repeat['acc_z'][$sign_key], $threshold)){
					$isGoodMove[$key]['acce'][$sign_key] += 0.34;
					$printGoodMove[$key]['acce'][$sign_key][2] = 1;
				}
				if($this->checkGoodMove($feature_template['roll'][$sign_key], $test_1repeat['roll'][$sign_key], $threshold)){
					$isGoodMove[$key]['gyro'][$sign_key] += 0.34;
					$printGoodMove[$key]['gyro'][$sign_key][0] = 1;
				}
				if($this->checkGoodMove($feature_template['yaw'][$sign_key], $test_1repeat['yaw'][$sign_key], $threshold)){
					$isGoodMove[$key]['gyro'][$sign_key] += 0.34;
					$printGoodMove[$key]['gyro'][$sign_key][1] = 1;
				}
				if($this->checkGoodMove($feature_template['pitch'][$sign_key], $test_1repeat['pitch'][$sign_key], $threshold)){
					$isGoodMove[$key]['gyro'][$sign_key] += 0.34;
					$printGoodMove[$key]['gyro'][$sign_key][2] = 1;
				}
			}
		}

		$majorLevel = $this->calMajorLevelForTemplate();
		$majorLevel_sum = [
			'acce' => ['pos'=>0, 'neg'=>0],
			'gyro' => ['pos'=>0, 'neg'=>0]
		];
		$majorLevel_sum['acce']['pos'] = $majorLevel['acc_x']['pos'] + $majorLevel['acc_y']['pos'] + $majorLevel['acc_z']['pos'];
		$majorLevel_sum['gyro']['pos'] = $majorLevel['roll']['pos'] + $majorLevel['yaw']['pos'] + $majorLevel['pitch']['pos'];
		$majorLevel_sum['acce']['neg'] = $majorLevel['acc_x']['neg'] + $majorLevel['acc_y']['neg'] + $majorLevel['acc_z']['neg'];
		$majorLevel_sum['gyro']['neg'] = $majorLevel['roll']['neg'] + $majorLevel['yaw']['neg'] + $majorLevel['pitch']['neg'];

		return [$isGoodMove,$printGoodMove];
	}	

	protected function templateMax($template_1session_1axis)
	{
		$tmp = [];
		foreach ($template_1session_1axis as $key => $repeat) {
			$pos = array_filter($repeat, array($this, '_pos'));
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
		$tmp = [];
		foreach ($template_1session_1axis as $key => $repeat) {
			$neg = array_filter($repeat, array($this, '_neg'));
			if(!empty($neg)){
				$tmp[] = min($neg);
			}
			else{
				$tmp[] = 0;
			}
		}
		return min(array_sum($tmp)/count($tmp),-10e-5);  
	}

	protected function _pos($var){
		if($var>=0){
			return true;
		}else{
			return false;
		}
	}
	protected function _neg($var){
		if($var<0){
			return true;
		}else{
			return false;
		}
	}

	public function calScore()
	{
		$goodMove_Point = 65;
		$ratio_Point = 100-$goodMove_Point;

		$score = array_fill(	0,
								$this->session,
								0
								);

		$majorLevel = $this->calMajorLevelForTemplate();
		$majorLevel_sum = [
			'acce' => ['pos'=>0, 'neg'=>0],
			'gyro' => ['pos'=>0, 'neg'=>0]
		];
		$majorLevel_sum['acce']['pos'] += ($majorLevel['acc_x']['pos'] + $majorLevel['acc_y']['pos'] + $majorLevel['acc_z']['pos']);
		$majorLevel_sum['gyro']['pos'] += ($majorLevel['roll']['pos'] + $majorLevel['yaw']['pos'] + $majorLevel['pitch']['pos']);
		$majorLevel_sum['acce']['neg'] += ($majorLevel['acc_x']['neg'] + $majorLevel['acc_y']['neg'] + $majorLevel['acc_z']['neg']);
		$majorLevel_sum['gyro']['neg'] += ($majorLevel['roll']['neg'] + $majorLevel['yaw']['neg'] + $majorLevel['pitch']['neg']);

		$acc_x_weight = [
							'pos' => $majorLevel['acc_x']['pos']/max($majorLevel_sum['acce']['pos'],10e-3),
							'neg' => $majorLevel['acc_x']['neg']/max($majorLevel_sum['acce']['neg'],10e-3)
						];
		$acc_y_weight = [
							'pos' => $majorLevel['acc_y']['pos']/max($majorLevel_sum['acce']['pos'],10e-3),
							'neg' => $majorLevel['acc_y']['neg']/max($majorLevel_sum['acce']['neg'],10e-3)
						];
		$acc_z_weight = [
							'pos' => $majorLevel['acc_z']['pos']/max($majorLevel_sum['acce']['pos'],10e-3),
							'neg' => $majorLevel['acc_z']['neg']/max($majorLevel_sum['acce']['neg'],10e-3)
						];
		$roll_weight = [
							'pos' => $majorLevel['roll']['pos']/max($majorLevel_sum['gyro']['pos'],10e-3),
							'neg' => $majorLevel['roll']['neg']/max($majorLevel_sum['gyro']['neg'],10e-3)
						];
		$yaw_weight = [
							'pos' => $majorLevel['yaw']['pos']/max($majorLevel_sum['gyro']['pos'],10e-3),
							'neg' => $majorLevel['yaw']['neg']/max($majorLevel_sum['gyro']['neg'],10e-3)
						];
		$pitch_weight = [
							'pos' => $majorLevel['pitch']['pos']/max($majorLevel_sum['gyro']['pos'],10e-3),
							'neg' => $majorLevel['pitch']['neg']/max($majorLevel_sum['gyro']['neg'],10e-3)
						];

		for ($i=0; $i < $this->session; $i++) { 
			$score_1session = [
				'acc_x' => ['pos'=>0, 'neg'=>0],
				'acc_y' => ['pos'=>0, 'neg'=>0],
				'acc_z' => ['pos'=>0, 'neg'=>0],
				'roll' => ['pos'=>0, 'neg'=>0],
				'yaw' => ['pos'=>0, 'neg'=>0],
				'pitch' => ['pos'=>0, 'neg'=>0],
				'acce_goodMove' => ['pos'=>0, 'neg'=>0],
				'gyro_goodMove' => ['pos'=>0, 'neg'=>0]
			];

			$tmp = $this->validateMove($this->testData[$i]);
			$isGoodMove = $tmp[0];
			$printGoodMove = $tmp[1];
			
			//template的max,min用平均
			//test用for
			for ($j=0; $j < $this->test_repeat_time; $j++) { 
				$pos_acc_x_test = array_filter($this->testData[$i]['acc_x'][$j], array($this, '_pos'));
				$neg_acc_x_test = array_filter($this->testData[$i]['acc_x'][$j], array($this, '_neg'));
				$pos_acc_y_test = array_filter($this->testData[$i]['acc_y'][$j], array($this, '_pos'));
				$neg_acc_y_test = array_filter($this->testData[$i]['acc_y'][$j], array($this, '_neg'));
				$pos_acc_z_test = array_filter($this->testData[$i]['acc_z'][$j], array($this, '_pos'));
				$neg_acc_z_test = array_filter($this->testData[$i]['acc_z'][$j], array($this, '_neg'));
				$pos_roll_test = array_filter($this->testData[$i]['roll'][$j], array($this, '_pos'));
				$neg_roll_test = array_filter($this->testData[$i]['roll'][$j], array($this, '_neg'));
				$pos_yaw_test = array_filter($this->testData[$i]['yaw'][$j], array($this, '_pos'));
				$neg_yaw_test = array_filter($this->testData[$i]['yaw'][$j], array($this, '_neg'));
				$pos_pitch_test = array_filter($this->testData[$i]['pitch'][$j], array($this, '_pos'));
				$neg_pitch_test = array_filter($this->testData[$i]['pitch'][$j], array($this, '_neg'));

				if(!empty($pos_acc_x_test)){
					$max_acc_x_test = max($pos_acc_x_test);
				}
				else{
					$max_acc_x_test = 0;
				}

				if(!empty($neg_acc_x_test)){
					$min_acc_x_test = min($neg_acc_x_test);
				}
				else{
					$min_acc_x_test = 0;
				}

				if(!empty($pos_acc_y_test)){
					$max_acc_y_test = max($pos_acc_y_test);
				}
				else{
					$max_acc_y_test = 0;
				}

				if(!empty($neg_acc_y_test)){
					$min_acc_y_test = min($neg_acc_y_test);
				}
				else{
					$min_acc_y_test = 0;
				}

				if(!empty($pos_acc_z_test)){
					$max_acc_z_test = max($pos_acc_z_test);
				}
				else{
					$max_acc_z_test = 0;
				}

				if(!empty($neg_acc_z_test)){
					$min_acc_z_test = min($neg_acc_z_test);
				}
				else{
					$min_acc_z_test = 0;
				}

				if(!empty($pos_roll_test)){
					$max_roll_test = max($pos_roll_test);
				}
				else{
					$max_roll_test = 0;
				}

				if(!empty($neg_roll_test)){
					$min_roll_test = min($neg_roll_test);
				}
				else{
					$min_roll_test = 0;
				}

				if(!empty($pos_yaw_test)){
					$max_yaw_test = max($pos_yaw_test);
				}
				else{
					$max_yaw_test = 0;
				}

				if(!empty($neg_yaw_test)){
					$min_yaw_test = min($neg_yaw_test);
				}
				else{
					$min_yaw_test = 0;
				}

				if(!empty($pos_pitch_test)){
					$max_pitch_test = max($pos_pitch_test);
				}
				else{
					$max_pitch_test = 0;
				}

				if(!empty($neg_pitch_test)){
					$min_pitch_test = min($neg_pitch_test);
				}
				else{
					$min_pitch_test = 0;
				}
				
				//$this->cutOffNum($feature_6axis[$axis_key]['pos']['avg'], -0.05, 0.05);
				$error_threshold = 0.3;
				$acc_x_max_ratio = $this->cutOffNum( abs($max_acc_x_test-$this->templateMax($this->templateData['acc_x']))/abs($this->templateMax($this->templateData['acc_x'])), 0, $error_threshold);
				$acc_x_min_ratio = $this->cutOffNum( abs($min_acc_x_test-$this->templateMin($this->templateData['acc_x']))/abs($this->templateMin($this->templateData['acc_x'])), 0, $error_threshold);

				$acc_y_max_ratio = $this->cutOffNum( abs($max_acc_y_test-$this->templateMax($this->templateData['acc_y']))/abs($this->templateMax($this->templateData['acc_y'])), 0, $error_threshold);
				$acc_y_min_ratio = $this->cutOffNum( abs($min_acc_y_test-$this->templateMin($this->templateData['acc_y']))/abs($this->templateMin($this->templateData['acc_y'])), 0, $error_threshold);

				$acc_z_max_ratio = $this->cutOffNum( abs($max_acc_z_test-$this->templateMax($this->templateData['acc_z']))/abs($this->templateMax($this->templateData['acc_z'])), 0, $error_threshold);
				$acc_z_min_ratio = $this->cutOffNum( abs($min_acc_z_test-$this->templateMin($this->templateData['acc_z']))/abs($this->templateMin($this->templateData['acc_z'])), 0, $error_threshold);

				$roll_max_ratio = $this->cutOffNum( abs($max_roll_test-$this->templateMax($this->templateData['roll']))/abs($this->templateMax($this->templateData['roll'])), 0, $error_threshold);
				$roll_min_ratio = $this->cutOffNum( abs($min_roll_test-$this->templateMin($this->templateData['roll']))/abs($this->templateMin($this->templateData['roll'])), 0, $error_threshold);

				$yaw_max_ratio = $this->cutOffNum( abs($max_yaw_test-$this->templateMax($this->templateData['yaw']))/abs($this->templateMax($this->templateData['yaw'])), 0, $error_threshold);
				$yaw_min_ratio = $this->cutOffNum( abs($min_yaw_test-$this->templateMin($this->templateData['yaw']))/abs($this->templateMin($this->templateData['yaw'])), 0, $error_threshold);

				$pitch_max_ratio = $this->cutOffNum( abs($max_pitch_test-$this->templateMax($this->templateData['pitch']))/abs($this->templateMax($this->templateData['pitch'])), 0, $error_threshold);
				$pitch_min_ratio = $this->cutOffNum( abs($min_pitch_test-$this->templateMin($this->templateData['pitch']))/abs($this->templateMin($this->templateData['pitch'])), 0, $error_threshold);

			
				$score_1session['acce_goodMove']['pos'] += $goodMove_Point/2/2*$isGoodMove[$j]['acce']['pos']/$this->test_repeat_time;
				$score_1session['acce_goodMove']['neg'] += $goodMove_Point/2/2*$isGoodMove[$j]['acce']['neg']/$this->test_repeat_time;
				$score_1session['gyro_goodMove']['pos'] += $goodMove_Point/2/2*$isGoodMove[$j]['gyro']['pos']/$this->test_repeat_time;
				$score_1session['gyro_goodMove']['neg'] += $goodMove_Point/2/2*$isGoodMove[$j]['gyro']['neg']/$this->test_repeat_time;

				if($isGoodMove[$j]['acce']['pos']!=0){
					$score_1session['acc_x']['pos']+= max($ratio_Point * $acc_x_weight['pos'] * (1-$acc_x_max_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['acc_y']['pos']+= max($ratio_Point * $acc_y_weight['pos'] * (1-$acc_y_max_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['acc_z']['pos']+= max($ratio_Point * $acc_z_weight['pos'] * (1-$acc_z_max_ratio)/4 /$this->test_repeat_time, 0);
				}
				if($isGoodMove[$j]['acce']['neg']!=0){
					$score_1session['acc_x']['neg']+= max($ratio_Point * $acc_x_weight['neg'] * (1-$acc_x_min_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['acc_y']['neg']+= max($ratio_Point * $acc_y_weight['neg'] * (1-$acc_y_min_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['acc_z']['neg']+= max($ratio_Point * $acc_z_weight['neg'] * (1-$acc_z_min_ratio)/4 /$this->test_repeat_time, 0);
				}
				if($isGoodMove[$j]['gyro']['pos']!=0){
					$score_1session['roll']['pos']+= max($ratio_Point * $roll_weight['pos'] * (1-$roll_max_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['yaw']['pos']+= max($ratio_Point * $yaw_weight['pos'] * (1-$yaw_max_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['pitch']['pos']+= max($ratio_Point * $pitch_weight['pos'] * (1-$pitch_max_ratio)/4 /$this->test_repeat_time, 0);
				}
				if($isGoodMove[$j]['gyro']['neg']!=0){
					$score_1session['roll']['neg']+= max($ratio_Point * $roll_weight['neg'] * (1-$roll_min_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['yaw']['neg']+= max($ratio_Point * $yaw_weight['neg'] * (1-$yaw_min_ratio)/4 /$this->test_repeat_time, 0);
					$score_1session['pitch']['neg']+= max($ratio_Point * $pitch_weight['neg'] * (1-$pitch_min_ratio)/4 /$this->test_repeat_time, 0);
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
			Log::debug('');
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
			Log::debug('');


			for ($j=0; $j < $this->test_repeat_time; $j++) { 
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $isGoodMove["acce"]["pos"]: '.strval($isGoodMove[$j]["acce"]["pos"]));
				//$isGoodMove[$key]['acce'][$sign_key][0]
				Log::debug(strval($printGoodMove[$j]['acce']['pos'][0]).' '.strval($printGoodMove[$j]['acce']['pos'][1]).' '.strval($printGoodMove[$j]['acce']['pos'][2]));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $isGoodMove["acce"]["neg"]: '.strval($isGoodMove[$j]["acce"]["neg"]));
				Log::debug(strval($printGoodMove[$j]['acce']['neg'][0]).' '.strval($printGoodMove[$j]['acce']['neg'][1]).' '.strval($printGoodMove[$j]['acce']['neg'][2]));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $isGoodMove["gyro"]["pos"]: '.strval($isGoodMove[$j]["gyro"]["pos"]));
				Log::debug(strval($printGoodMove[$j]['gyro']['pos'][0]).' '.strval($printGoodMove[$j]['gyro']['pos'][1]).' '.strval($printGoodMove[$j]['gyro']['pos'][2]));
				Log::debug('AI session: '.strval($i).' repeat: '.$j.'  $isGoodMove["gyro"]["neg"]: '.strval($isGoodMove[$j]["gyro"]["neg"]));
				Log::debug(strval($printGoodMove[$j]['gyro']['neg'][0]).' '.strval($printGoodMove[$j]['gyro']['neg'][1]).' '.strval($printGoodMove[$j]['gyro']['neg'][2]));
			}
			Log::debug('');


			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_x\'][\'pos\']: '.strval(round($score_1session['acc_x']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_x\'][\'neg\']: '.strval(round($score_1session['acc_x']['neg'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_y\'][\'pos\']: '.strval(round($score_1session['acc_y']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_y\'][\'neg\']: '.strval(round($score_1session['acc_y']['neg'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_z\'][\'pos\']: '.strval(round($score_1session['acc_z']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_z\'][\'neg\']: '.strval(round($score_1session['acc_z']['neg'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'roll\'][\'pos\']: '.strval(round($score_1session['roll']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'roll\'][\'neg\']: '.strval(round($score_1session['roll']['neg'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'yaw\'][\'pos\']: '.strval(round($score_1session['yaw']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'yaw\'][\'neg\']: '.strval(round($score_1session['yaw']['neg'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'pitch\'][\'pos\']: '.strval(round($score_1session['pitch']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'pitch\'][\'neg\']: '.strval(round($score_1session['pitch']['neg'])));
			
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acce_goodMove\'][\'pos\']: '.strval(round($score_1session['acce_goodMove']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'acce_goodMove\'][\'neg\']: '.strval(round($score_1session['acce_goodMove']['neg'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'gyro_goodMove\'][\'pos\']: '.strval(round($score_1session['gyro_goodMove']['pos'])));
			Log::debug('AI session: '.strval($i).'  $score_1session[\'gyro_goodMove\'][\'neg\']: '.strval(round($score_1session['gyro_goodMove']['neg'])));
			foreach ($score_1session as $key => $value) {
				$score[$i] += array_sum($value);
			}
			Log::debug('AI session: '.strval($i).'  $score_1session: '.strval(round($score[$i])));

			/*
			$validatedMove = ($score_1session['acce_goodMove']['pos']+$score_1session['acce_goodMove']['neg']+$score_1session['gyro_goodMove']['pos']+$score_1session['gyro_goodMove']['neg'])/$goodMove_Point*4*$this->test_repeat_time;
			
			$inlinearMove_bonus = 72*(1-exp(-0.1*$validatedMove)) - ($score_1session['acce_goodMove']['pos']+$score_1session['acce_goodMove']['neg']+$score_1session['gyro_goodMove']['pos']+$score_1session['gyro_goodMove']['neg']);
			
			$score[$i] = round($score[$i]+$inlinearMove_bonus);
			*/
		
		}
		return $score;
	}
	public function printFeature()
	{
		$tmp = [
				'test'=>[],
				'template'=>[]
				];
		$majorLevel = $this->calMajorLevelForTemplate();
		for ($i=0; $i < $this->session; $i++) { 
			$tmp['test'][] = $this->calFeatureForTest($this->testData[$i]);
			$tmp['template'] = $this->calFeatureForTemplate();
		}
		return $tmp;
	}
}

?>