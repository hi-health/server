<?php
namespace App\AI;
use Exception;
use Log;

class RepeatMultiDirectionAIv2 extends AI{



	public function __construct($template, $test, $param)
    {
        parent::__construct($template, $test, $param);
        $this->template_repeat_time = count($this->templateData['acc_x']);
    }

    protected function calFeature()
    {
        $feature = [
            "max" => [],
            "min" => [],
            "amp" => [],
            "mean" => [],
            "var" => [],
            "absSum" => []
        ];
        $feature_template = [
    		'acc_x' => $feature,
    		'acc_y' => $feature,
    		'acc_z' => $feature,
    		'roll_madgwick' => $feature,
    		'yaw_madgwick' => $feature,
    		'pitch_madgwick' => $feature
        ];
        $feature_test = array_fill(
            0,
            $this->session,
            [
                'acc_x' => $feature,
                'acc_y' => $feature,
                'acc_z' => $feature,
                'roll_madgwick' => $feature,
                'yaw_madgwick' => $feature,
                'pitch_madgwick' => $feature
            ]
        );
        
        foreach (['acc_x', 'acc_y', 'acc_z', 'roll_madgwick', 'yaw_madgwick', 'pitch_madgwick'] as $axis) {
            foreach ($this->templateData[$axis] as $key1 => $v1) {
                
                $feature_template[$axis]["max"][] = max($v1);
                $feature_template[$axis]["min"][] = min($v1);
                $feature_template[$axis]["amp"][] = max($v1) - min($v1);
                $feature_template[$axis]["mean"][] = array_sum($v1) / count($v1);
                $feature_template[$axis]["var"][] = stddev($v1);
                $feature_template[$axis]["absSum"][] = 0;
                foreach ($v1 as $key2 => $v2) {
                    $feature_template[$axis]["absSum"][] += abs($v2);
                }
            }

            for ($i=0; $i < $this->session; $i++) {
                for ($j=0; $j < $this->test_repeat_time; $j++) {
                    $v1 = $this->testData[$i][$axis][$j];
                    $feature_test[$i][$axis]["max"][] = max($v1);
                    $feature_test[$i][$axis]["min"][] = min($v1);
                    $feature_test[$i][$axis]["amp"][] = max($v1) - min($v1);
                    $feature_test[$i][$axis]["mean"][] = array_sum($v1) / count($v1);
                    $feature_test[$i][$axis]["var"][] = stddev($v1);
                    $feature_test[$i][$axis]["absSum"][] = 0;
                    foreach ($v1 as $key2 => $v2) {
                        $feature_test[$i][$axis]["absSum"][] += abs($v2);
                    }
                }
            }
        }
        
    	return [
            "template" => $feature_template,            
            "test" => $feature_test
        ];
    }

    protected function validateMove()
    {
        $isGoodMove = array_fill(
                0,
                $this->session,
                array_fill( 0,
                            $this->test_repeat_time,
                            [
                                "roll_madgwick" => [0, 0],
                                "yaw_madgwick" => [0, 0],
                                "pitch_madgwick" => [0, 0]
                            ]
                )
        );

        $feature = $this->calFeature();
        foreach (['roll_madgwick', 'yaw_madgwick', 'pitch_madgwick'] as $axis) {
            for ($i=0; $i < $this->session; $i++) { 
                for ($j=0; $j < $this->test_repeat_time; $j++) { 
                    for ($k=0; $k < count($this->templateData[$axis]); $k++) { 
                        $t = max($feature["test"][$i][$axis]['amp'][$j], 10e-4);
                        $T = max($feature["template"][$axis]['amp'][$k], 10e-4);
                        if($t<0.2 && $T<0.2){//both < 10deg
                            $isGoodMove[$i][$j][$axis][0] += 1 / count($this->templateData[$axis]);
                        }
                        else{
                            $isGoodMove[$i][$j][$axis][0] += min($t/$T,$T/$t) / count($this->templateData[$axis]);
                                //( 1 - abs($t / $T - 1) ) / count($this->templateData[$axis]);
                        }

                        if($t>$T){
                            $isGoodMove[$i][$j][$axis][1] = 1; //too large
                        }
                        else{
                            $isGoodMove[$i][$j][$axis][1] = -1; //too small
                        }
                    }
                }
            }
        }
        return $isGoodMove;
    }

    protected function validateSpeed()
    {
        $isGoodSpeed = array_fill(
                0,
                $this->session,
                array_fill( 0,
                            $this->test_repeat_time,
                            [  
                                "acc_x" => [0, 0],
                                "acc_y" => [0, 0],
                                "acc_z" => [0, 0]
                            ]
                )
        );

        $feature = $this->calFeature();
        foreach (['acc_x', 'acc_y', 'acc_z'] as $axis) {
            for ($i=0; $i < $this->session; $i++) { 
                for ($j=0; $j < $this->test_repeat_time; $j++) { 
                    for ($k=0; $k < count($this->templateData[$axis]); $k++) { 
                        $t = max($feature["test"][$i][$axis]['amp'][$j], 10e-4);
                        $T = max($feature["template"][$axis]['amp'][$k], 10e-4);
                        $isGoodSpeed[$i][$j][$axis][0] += min($t/$T,$T/$t) / count($this->templateData[$axis]);
                        if($t>$T){
                            $isGoodSpeed[$i][$j][$axis][1] = 1; //too fast
                        }
                        else{
                            $isGoodSpeed[$i][$j][$axis][1] = -1; //too slow
                        }
                            //( 1 - abs($t / $T - 1) ) / count($this->templateData[$axis]);
                    }
                }
            }
        }
        return $isGoodSpeed;
    }

    public function calScore()
    {

        $score = [];
        $score_detail = [];
        $reason = [];
        $feature = $this->calFeature();
        $isGoodMove = $this->validateMove();
        $isGoodSpeed = $this->validateSpeed();
        for ($i=0; $i < $this->session; $i++) { 
            $noMajorLevel = true;

            $score_1session = [
                'acc_x' => 0,
                'acc_y' => 0,
                'acc_z' => 0,
                'goodMove_roll' => 0,
                'goodMove_yaw' => 0,
                'goodMove_pitch' => 0,
            ];
    
            //for reason
            $acc_x_var_ratio = 0;
            $acc_y_var_ratio = 0;
            $acc_z_var_ratio = 0;

            $roll_amp_ratio = 0;
            $yaw_amp_ratio = 0;
            $pitch_amp_ratio = 0;
            
            $acc_x_var_sign = 0;
            $acc_y_var_sign = 0;
            $acc_z_var_sign = 0;

            $roll_amp_sign = 0;
            $yaw_amp_sign = 0;
            $pitch_amp_sign = 0;

            //template的max,min用平均
            //test用for
            for ($j=0; $j < $this->test_repeat_time; $j++) { 

                $acc_partition = 20;
                $att_partition = 80;
                $score_1session['acc_x'] += max( $acc_partition/3 * $isGoodSpeed[$i][$j]['acc_x'][0] / $this->test_repeat_time, 0 );
                $score_1session['acc_y'] += max( $acc_partition/3 * $isGoodSpeed[$i][$j]['acc_y'][0] / $this->test_repeat_time, 0 );
                $score_1session['acc_z'] += max( $acc_partition/3 * $isGoodSpeed[$i][$j]['acc_z'][0] / $this->test_repeat_time, 0 );

                $score_1session['goodMove_roll'] += $att_partition * $isGoodMove[$i][$j]['roll_madgwick'][0]/count($isGoodMove[$i][$j])/$this->test_repeat_time;
                $score_1session['goodMove_yaw'] += $att_partition * $isGoodMove[$i][$j]['yaw_madgwick'][0]/count($isGoodMove[$i][$j])/$this->test_repeat_time;
                $score_1session['goodMove_pitch'] += $att_partition * $isGoodMove[$i][$j]['pitch_madgwick'][0]/count($isGoodMove[$i][$j])/$this->test_repeat_time;

                $acc_x_var_ratio += $isGoodSpeed[$i][$j]['acc_x'][0];
                $acc_y_var_ratio += $isGoodSpeed[$i][$j]['acc_y'][0];
                $acc_z_var_ratio += $isGoodSpeed[$i][$j]['acc_z'][0];

                $roll_amp_ratio += $isGoodMove[$i][$j]["roll_madgwick"][0];
                $yaw_amp_ratio += $isGoodMove[$i][$j]["yaw_madgwick"][0];
                $pitch_amp_ratio += $isGoodMove[$i][$j]["pitch_madgwick"][0];

                $acc_x_var_sign += $isGoodSpeed[$i][$j]['acc_x'][1];
                $acc_y_var_sign += $isGoodSpeed[$i][$j]['acc_y'][1];
                $acc_z_var_sign += $isGoodSpeed[$i][$j]['acc_z'][1];

                $roll_amp_sign += $isGoodMove[$i][$j]["roll_madgwick"][1];
                $yaw_amp_sign += $isGoodMove[$i][$j]["yaw_madgwick"][1];
                $pitch_amp_sign += $isGoodMove[$i][$j]["pitch_madgwick"][1];
            }

            $arr_acce = [   array_sum($feature["test"][$i]['acc_x']['var']),
                            array_sum($feature["test"][$i]['acc_y']['var']),
                            array_sum($feature["test"][$i]['acc_z']['var'])
                        ];
            $arr_gyro = [   array_sum($feature["test"][$i]['roll_madgwick']['amp']),
                            array_sum($feature["test"][$i]['yaw_madgwick']['amp']),
                            array_sum($feature["test"][$i]['pitch_madgwick']['amp'])
                        ];
            Log::debug('reason max: '.strval(array_keys($arr_acce,max($arr_acce))[0]));
            switch(array_keys($arr_acce,max($arr_acce))[0]){
                case 0:
                    $diff_acce = 100-($acc_x_var_ratio)/$this->test_repeat_time*100;
                    $sign_acce = ($acc_x_var_sign > 0);
                    break;
                case 1:
                    $diff_acce = 100-($acc_y_var_ratio)/$this->test_repeat_time*100;
                    $sign_acce = ($acc_y_var_sign > 0);
                    break;
                case 2:
                    $diff_acce = 100-($acc_z_var_ratio)/$this->test_repeat_time*100;
                    $sign_acce = ($acc_z_var_sign > 0);
                    break;
            }
            Log::debug('reason max: '.strval(array_keys($arr_gyro,max($arr_gyro))[0]));
            switch(array_keys($arr_gyro,max($arr_gyro))[0]){
                case 0:
                    $diff_gyro = 100-($roll_amp_ratio)/$this->test_repeat_time*100;
                    $sign_gyro = ($roll_amp_sign > 0);
                    break;
                case 1:
                    $diff_gyro = 100-($yaw_amp_ratio)/$this->test_repeat_time*100;
                    $sign_gyro = ($yaw_amp_sign > 0);
                    break;
                case 2:
                    $diff_gyro = 100-($pitch_amp_ratio)/$this->test_repeat_time*100;
                    $sign_gyro = ($pitch_amp_sign > 0);
                    break;
            }
            $diff_acce = round($diff_acce, 1);
            $diff_gyro = round($diff_gyro, 1);
            if($sign_acce){
                $s0 = "快";
            }
            else{
                $s0 = "慢";
            }
            if($sign_gyro){
                $s1 = "大";
            }
            else{
                $s1 = "小";
            }
            $reason_1session = [sprintf("您的速度比標準%s了",$s0).strval($diff_acce)."%",sprintf("您的角度比標準%s了",$s1).strval($diff_gyro)."%"];
            

            Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_x\']: '.strval(round($score_1session['acc_x'])));
            Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_y\']: '.strval(round($score_1session['acc_y'])));
            Log::debug('AI session: '.strval($i).'  $score_1session[\'acc_z\']: '.strval(round($score_1session['acc_z'])));
            
            Log::debug('AI session: '.strval($i).'  $score_1session[\'goodMove_roll\']: '.strval(round($score_1session['goodMove_roll'])));
            Log::debug('AI session: '.strval($i).'  $score_1session[\'goodMove_yaw\']: '.strval(round($score_1session['goodMove_yaw'])));
            Log::debug('AI session: '.strval($i).'  $score_1session[\'goodMove_pitch\']: '.strval(round($score_1session['goodMove_pitch'])));

            Log::debug('AI session: '.strval($i).'  $score_1session: '.strval(round(array_sum($score_1session))));
            $score[] = round(array_sum($score_1session));
            $score_detail[] = $score_1session;
            $reason[] = $reason_1session;
        
        }
        return [
            'score' => $score,
            'reason' => $reason,
            'score_detail' => $score_detail,
            'good_move' => $isGoodMove,
            'good_speed' => $isGoodSpeed
        ];
    }
    public function printFeature()
	{   
        $score = $this->calScore();
		$tmp = [
				'test'=> $this->testData,
                'template'=>$this->templateData,
                'rawTest'=> $this->rawTestData,
                'rawTemplate'=>$this->rawTemplateData,
                //'testAC'=>$this->testDataAC,
                //'rawTestAC'=>$this->rawTestDataAC,
                'score' => $score['score'],
                'reason' => $score['reason'],
                'score_detail' => $score['score_detail'],
                'good_move' => $score['good_move'],
                'good_speed' => $score['good_speed']
                ];
                
		return $tmp;
	}
}

?>