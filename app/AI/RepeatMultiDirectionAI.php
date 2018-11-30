<?php
namespace App\AI;
use Exception;
use Log;

class RepeatMultiDirectionAI extends AI{



	public function __construct($template, $test, $param)
    {
        parent::__construct($template, $test, $param);
        $this->template_repeat_time = count($this->templateData['acc_x']);
    }

    protected function calMajorLevel()
    {
    	$absSum_5temp_6axis = array_fill(	0,
    										$this->template_repeat_time,
    										[	'acc_x' => 0,
    											'acc_y' => 0,
    											'acc_z' => 0,
    											'roll' => 0,
    											'yaw' => 0,
    											'pitch' => 0
    										]
    									);
    	
    	$majorLevel_6axis =[
    		'acc_x' => 0,
    		'acc_y' => 0,
    		'acc_z' => 0,
    		'roll' => 0,
    		'yaw' => 0,
    		'pitch' => 0
    	];

    	foreach ($this->templateData['acc_x'] as $key1 => $v1) {
    		foreach ($v1 as $key2 => $v2) {
    			$absSum_5temp_6axis[$key1]['acc_x'] += abs($v2);
    		}
    	}
    	foreach ($this->templateData['acc_y'] as $key1 => $v1) {
    		foreach ($v1 as $key2 => $v2) {
    			$absSum_5temp_6axis[$key1]['acc_y'] += abs($v2);
    		}
    	}
    	foreach ($this->templateData['acc_z'] as $key1 => $v1) {
    		foreach ($v1 as $key2 => $v2) {
    			$absSum_5temp_6axis[$key1]['acc_z'] += abs($v2);
    		}
    	}
    	foreach ($this->templateData['roll'] as $key1 => $v1) {
    		foreach ($v1 as $key2 => $v2) {
    			$absSum_5temp_6axis[$key1]['roll'] += abs($v2);
    		}
    	}
    	foreach ($this->templateData['yaw'] as $key1 => $v1) {
    		foreach ($v1 as $key2 => $v2) {
    			$absSum_5temp_6axis[$key1]['yaw'] += abs($v2);
    		}
    	}
    	foreach ($this->templateData['pitch'] as $key1 => $v1) {
    		foreach ($v1 as $key2 => $v2) {
    			$absSum_5temp_6axis[$key1]['pitch'] += abs($v2);
    		}
    	}
    	
    	for ($i=0; $i < $this->template_repeat_time; $i++) { 
    		//acce
    		if(	$absSum_5temp_6axis[$i]['acc_x'] >= $absSum_5temp_6axis[$i]['acc_y']*$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['acc_x'] >= $absSum_5temp_6axis[$i]['acc_z']*$this->major_threshold)
    		{
    			$majorLevel_6axis['acc_x'] += 2;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['acc_y'] >= $absSum_5temp_6axis[$i]['acc_x']*$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['acc_y'] >= $absSum_5temp_6axis[$i]['acc_z']*$this->major_threshold)
    		{
    			$majorLevel_6axis['acc_y'] += 2;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['acc_z'] >= $absSum_5temp_6axis[$i]['acc_x']*$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['acc_z'] >= $absSum_5temp_6axis[$i]['acc_y']*$this->major_threshold)
    		{
    			$majorLevel_6axis['acc_z'] += 2;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['acc_x'] <= $absSum_5temp_6axis[$i]['acc_y']/$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['acc_x'] <= $absSum_5temp_6axis[$i]['acc_z']/$this->major_threshold)
    		{
    			$majorLevel_6axis['acc_y'] += 1;
    			$majorLevel_6axis['acc_z'] += 1;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['acc_y'] <= $absSum_5temp_6axis[$i]['acc_x']/$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['acc_y'] <= $absSum_5temp_6axis[$i]['acc_z']/$this->major_threshold)
    		{
    			$majorLevel_6axis['acc_x'] += 1;
    			$majorLevel_6axis['acc_z'] += 1;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['acc_z'] <= $absSum_5temp_6axis[$i]['acc_x']/$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['acc_z'] <= $absSum_5temp_6axis[$i]['acc_y']/$this->major_threshold)
    		{
    			$majorLevel_6axis['acc_x'] += 1;
    			$majorLevel_6axis['acc_y'] += 1;
    		}

    		//gyro
    		if(	$absSum_5temp_6axis[$i]['roll'] >= $absSum_5temp_6axis[$i]['yaw']*$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['roll'] >= $absSum_5temp_6axis[$i]['pitch']*$this->major_threshold)
    		{
    			$majorLevel_6axis['roll'] += 2;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['yaw'] >= $absSum_5temp_6axis[$i]['roll']*$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['yaw'] >= $absSum_5temp_6axis[$i]['pitch']*$this->major_threshold)
    		{
    			$majorLevel_6axis['yaw'] += 2;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['pitch'] >= $absSum_5temp_6axis[$i]['roll']*$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['pitch'] >= $absSum_5temp_6axis[$i]['yaw']*$this->major_threshold)
    		{
    			$majorLevel_6axis['pitch'] += 2;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['roll'] <= $absSum_5temp_6axis[$i]['yaw']/$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['roll'] <= $absSum_5temp_6axis[$i]['pitch']/$this->major_threshold)
    		{
    			$majorLevel_6axis['yaw'] += 1;
    			$majorLevel_6axis['pitch'] += 1;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['yaw'] <= $absSum_5temp_6axis[$i]['roll']/$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['yaw'] <= $absSum_5temp_6axis[$i]['pitch']/$this->major_threshold)
    		{
    			$majorLevel_6axis['roll'] += 1;
    			$majorLevel_6axis['pitch'] += 1;
    		}
    		elseif(	$absSum_5temp_6axis[$i]['pitch'] <= $absSum_5temp_6axis[$i]['roll']/$this->major_threshold &&
    			$absSum_5temp_6axis[$i]['pitch'] <= $absSum_5temp_6axis[$i]['yaw']/$this->major_threshold)
    		{
    			$majorLevel_6axis['roll'] += 1;
    			$majorLevel_6axis['yaw'] += 1;
    		}

    	}
    	return $majorLevel_6axis;
    }
 
    protected function calPeakScale($template_1repeat_1axis,$test_1repeat_1axis)
    {
    	$template_max = max($template_1repeat_1axis);
		$template_min = min($template_1repeat_1axis);
		$test_max = max($test_1repeat_1axis);
		$test_min = min($test_1repeat_1axis);

		$max = abs($template_max - $test_max) / max(abs($template_max),10e-5);
		$min = abs($template_min - $test_min) / max(abs($template_min),10e-5);

			
		if ($max<$this->error_threshold && $min<$this->error_threshold){
			return 2;
		}
		elseif($max<$this->error_threshold || $min<$this->error_threshold){
			return 1;
		}
		else return 0;
    }

	protected function validateMove($test_1session)
    {
    	
        $peakScale_6axis = [];
        $isGoodMove = array_fill(   0,
                                    $this->test_repeat_time,
                                    [   'acce' => 0,
                                        'gyro' => 0,
                                    ]
                                );

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

        $majorLevel = $this->calMajorLevel();
        $majorLevel_acceSum = $majorLevel['acc_x'] + $majorLevel['acc_y'] + $majorLevel['acc_z'];
        $majorLevel_gyroSum = $majorLevel['roll'] + $majorLevel['yaw'] + $majorLevel['pitch'];

        if($majorLevel_acceSum>=6){
            if( $majorLevel['acc_x'] > $majorLevel_acceSum/2 ||
                ($majorLevel['acc_x'] == 4 && $majorLevel['acc_y'] == 2 && $majorLevel['acc_z'] == 2) )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if ($peakScale_6axis[$i]['acc_x'] >= $this->point_threshold) {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
            elseif( $majorLevel['acc_y'] > $majorLevel_acceSum/2 ||
                    ($majorLevel['acc_y'] == 4 && $majorLevel['acc_x'] == 2 && $majorLevel['acc_z'] == 2) )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if ($peakScale_6axis[$i]['acc_y'] >= $this->point_threshold) {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
            elseif( $majorLevel['acc_z'] > $majorLevel_acceSum/2 ||
                    ($majorLevel['acc_z'] == 4 && $majorLevel['acc_x'] == 2 && $majorLevel['acc_y'] == 2) )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if ($peakScale_6axis[$i]['acc_z'] >= $this->point_threshold) {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
            elseif( $majorLevel['acc_x'] > $majorLevel_acceSum/3 &&
                    $majorLevel['acc_y'] > $majorLevel_acceSum/3 )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['acc_x'] >= $this->point_threshold &&
                        $peakScale_6axis[$i]['acc_y'] >= $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
            elseif( $majorLevel['acc_x'] > $majorLevel_acceSum/3 &&
                    $majorLevel['acc_z'] > $majorLevel_acceSum/3 )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['acc_x'] >= $this->point_threshold &&
                        $peakScale_6axis[$i]['acc_z'] >= $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
            elseif( $majorLevel['acc_y'] > $majorLevel_acceSum/3 &&
                    $majorLevel['acc_z'] > $majorLevel_acceSum/3 )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['acc_y'] >= $this->point_threshold &&
                        $peakScale_6axis[$i]['acc_z'] >= $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
            else{
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['acc_x'] < $this->point_threshold &&
                        $peakScale_6axis[$i]['acc_y'] < $this->point_threshold &&
                        $peakScale_6axis[$i]['acc_z'] < $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['acce'] = 1;
                    }
                }
            }
        }
        else{
            for ($i=0; $i < $this->test_repeat_time; $i++) { 
                if( $peakScale_6axis[$i]['acc_x'] < $this->point_threshold &&
                    $peakScale_6axis[$i]['acc_y'] < $this->point_threshold &&
                    $peakScale_6axis[$i]['acc_z'] < $this->point_threshold ) 
                {
                    $isGoodMove[$i]['acce'] = 1;
                }
            }
        }

        if($majorLevel_gyroSum>=6){
            if( $majorLevel['roll'] > $majorLevel_gyroSum/2 ||
                ($majorLevel['roll'] == 4 && $majorLevel['yaw'] == 2 && $majorLevel['pitch'] == 2) )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if ($peakScale_6axis[$i]['roll'] >= $this->point_threshold) {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
            elseif( $majorLevel['yaw'] > $majorLevel_gyroSum/2 ||
                    ($majorLevel['yaw'] == 4 && $majorLevel['roll'] == 2 && $majorLevel['pitch'] == 2) )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if ($peakScale_6axis[$i]['yaw'] >= $this->point_threshold) {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
            elseif( $majorLevel['pitch'] > $majorLevel_gyroSum/2 ||
                    ($majorLevel['pitch'] == 4 && $majorLevel['roll'] == 2 && $majorLevel['yaw'] == 2) )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if ($peakScale_6axis[$i]['pitch'] >= $this->point_threshold) {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
            elseif( $majorLevel['roll'] > $majorLevel_gyroSum/3 &&
                    $majorLevel['yaw'] > $majorLevel_gyroSum/3 )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['roll'] >= $this->point_threshold &&
                        $peakScale_6axis[$i]['yaw'] >= $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
            elseif( $majorLevel['roll'] > $majorLevel_gyroSum/3 &&
                    $majorLevel['pitch'] > $majorLevel_gyroSum/3 )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['roll'] >= $this->point_threshold &&
                        $peakScale_6axis[$i]['pitch'] >= $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
            elseif( $majorLevel['yaw'] > $majorLevel_gyroSum/3 &&
                    $majorLevel['pitch'] > $majorLevel_gyroSum/3 )
            {
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['yaw'] >= $this->point_threshold &&
                        $peakScale_6axis[$i]['pitch'] >= $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
            else{
                for ($i=0; $i < $this->test_repeat_time; $i++) { 
                    if( $peakScale_6axis[$i]['roll'] < $this->point_threshold &&
                        $peakScale_6axis[$i]['yaw'] < $this->point_threshold &&
                        $peakScale_6axis[$i]['pitch'] < $this->point_threshold ) 
                    {
                        $isGoodMove[$i]['gyro'] = 1;
                    }
                }
            }
        }
        else{
            for ($i=0; $i < $this->test_repeat_time; $i++) { 
                if( $peakScale_6axis[$i]['roll'] < $this->point_threshold &&
                    $peakScale_6axis[$i]['yaw'] < $this->point_threshold &&
                    $peakScale_6axis[$i]['pitch'] < $this->point_threshold ) 
                {
                    $isGoodMove[$i]['gyro'] = 1;
                }
            }
        }

        return $isGoodMove;
    }	

    protected function findTemplateMax($template_1session_1axis)
    {
        $tmp = [];
        foreach ($template_1session_1axis as $key => $repeat) {
            $tmp[] = max($repeat);
        }
        if(array_sum($tmp)>=0)
        {
          return max(array_sum($tmp)/count($tmp),10e-5);  
        }
        else return min(array_sum($tmp)/count($tmp),-10e-5);
    }

    protected function findTemplateMin($template_1session_1axis)
    {
        $tmp = [];
        foreach ($template_1session_1axis as $key => $repeat) {
            $tmp[] = min($repeat);
        }
        if(array_sum($tmp)>=0)
        {
          return max(array_sum($tmp)/count($tmp),10e-5);  
        }
        else return min(array_sum($tmp)/count($tmp),-10e-5);
    }

    public function calScore()
    {

        $score = [];
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
            $majorLevel = $this->calMajorLevel();

            $majorLevel_sum = array_sum($majorLevel);
            $acc_x_weight = $majorLevel['acc_x']/$majorLevel_sum;
            $acc_y_weight = $majorLevel['acc_y']/$majorLevel_sum;
            $acc_z_weight = $majorLevel['acc_z']/$majorLevel_sum;
            $roll_weight = $majorLevel['roll']/$majorLevel_sum;
            $yaw_weight = $majorLevel['yaw']/$majorLevel_sum;
            $pitch_weight = $majorLevel['pitch']/$majorLevel_sum;

            //template的max,min用平均
            //test用for
            for ($j=0; $j < $this->test_repeat_time; $j++) { 

                $acc_x_max_ratio = abs(max($this->testData[$i]['acc_x'][$j])-$this->findTemplateMax($this->templateData['acc_x']))/abs($this->findTemplateMax($this->templateData['acc_x']));
                $acc_x_min_ratio = abs(min($this->testData[$i]['acc_x'][$j])-$this->findTemplateMin($this->templateData['acc_x']))/abs($this->findTemplateMin($this->templateData['acc_x']));

                $acc_y_max_ratio = abs(max($this->testData[$i]['acc_y'][$j])-$this->findTemplateMax($this->templateData['acc_y']))/abs($this->findTemplateMax($this->templateData['acc_y']));
                $acc_y_min_ratio = abs(min($this->testData[$i]['acc_y'][$j])-$this->findTemplateMin($this->templateData['acc_y']))/abs($this->findTemplateMin($this->templateData['acc_y']));

                $acc_z_max_ratio = abs(max($this->testData[$i]['acc_z'][$j])-$this->findTemplateMax($this->templateData['acc_z']))/abs($this->findTemplateMax($this->templateData['acc_z']));
                $acc_z_min_ratio = abs(min($this->testData[$i]['acc_z'][$j])-$this->findTemplateMin($this->templateData['acc_z']))/abs($this->findTemplateMin($this->templateData['acc_z']));

                $roll_max_ratio = abs(max($this->testData[$i]['roll'][$j])-$this->findTemplateMax($this->templateData['roll']))/abs($this->findTemplateMax($this->templateData['roll']));
                $roll_min_ratio = abs(min($this->testData[$i]['roll'][$j])-$this->findTemplateMin($this->templateData['roll']))/abs($this->findTemplateMin($this->templateData['roll']));

                $yaw_max_ratio = abs(max($this->testData[$i]['yaw'][$j])-$this->findTemplateMax($this->templateData['yaw']))/abs($this->findTemplateMax($this->templateData['yaw']));
                $yaw_min_ratio = abs(min($this->testData[$i]['yaw'][$j])-$this->findTemplateMin($this->templateData['yaw']))/abs($this->findTemplateMin($this->templateData['yaw']));

                $pitch_max_ratio = abs(max($this->testData[$i]['pitch'][$j])-$this->findTemplateMax($this->templateData['pitch']))/abs($this->findTemplateMax($this->templateData['pitch']));
                $pitch_min_ratio = abs(min($this->testData[$i]['pitch'][$j])-$this->findTemplateMin($this->templateData['pitch']))/abs($this->findTemplateMin($this->templateData['pitch']));


                $score_1session['acc_x'] += max( 50 * $acc_x_weight * (2-$acc_x_max_ratio-$acc_x_min_ratio)/2 /$this->test_repeat_time, 0 );
                $score_1session['acc_y'] += max( 50 * $acc_y_weight * (2-$acc_y_max_ratio-$acc_y_min_ratio)/2 /$this->test_repeat_time, 0 );
                $score_1session['acc_z'] += max( 50 * $acc_z_weight * (2-$acc_z_max_ratio-$acc_z_min_ratio)/2 /$this->test_repeat_time, 0 );
                $score_1session['roll'] += max( 50 * $roll_weight * (2-$roll_max_ratio-$roll_min_ratio)/2 /$this->test_repeat_time, 0 );
                $score_1session['yaw'] += max( 50 * $yaw_weight * (2-$yaw_max_ratio-$yaw_min_ratio)/2 /$this->test_repeat_time, 0 );
                $score_1session['pitch'] += max( 50 * $pitch_weight * (2-$pitch_max_ratio-$pitch_min_ratio)/2 /$this->test_repeat_time, 0 );

            
                $score_1session['acce_goodMove'] += 50/2*$isGoodMove[$j]['acce']/$this->test_repeat_time;
                $score_1session['gyro_goodMove'] += 50/2*$isGoodMove[$j]['gyro']/$this->test_repeat_time;

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

            Log::debug('AI session: '.strval($i).'  $acc_x_weight: '.strval(round($acc_x_weight,3)));
            Log::debug('AI session: '.strval($i).'  $acc_y_weight: '.strval(round($acc_y_weight,3)));
            Log::debug('AI session: '.strval($i).'  $acc_z_weight: '.strval(round($acc_z_weight,3)));
            Log::debug('AI session: '.strval($i).'  $roll_weight: '.strval(round($roll_weight,3)));
            Log::debug('AI session: '.strval($i).'  $yaw_weight: '.strval(round($yaw_weight,3)));
            Log::debug('AI session: '.strval($i).'  $pitch_weight: '.strval(round($pitch_weight,3)));

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