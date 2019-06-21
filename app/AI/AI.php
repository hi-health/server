<?php

namespace App\AI;

use Exception;
use App\ServicePlanVideo;
use Log;
include 'CalibrateByGravity.php';
include 'Madgwick.php';

abstract class AI
{
    protected $templateData;

    protected $testData;

    protected $session;

    protected $test_repeat_time;

    protected $template_repeat_time;

    protected $major_threshold;

    protected $error_threshold;

    protected $point_threshold;

    public function __construct($template, $test, $param)
    {
        $this->session = $param['session'];
        $this->test_repeat_time = $param['repeat_time'];

        $parseTest = $this->parseTestData($test);
        $parseTemplate = $this->parseTemplateData($template);

        $this->templateData = $parseTemplate['template'];
        $this->testData = $parseTest['each_repeat'];
        $this->templateTime = $this->getTemplateTime($template);
        $this->rawTemplateData = $parseTemplate['template_raw'];
        $this->rawTestData = $parseTest['each_repeat_raw'];

        // $this->testDataAC = $parseTest['auto_correlation'];
        // $this->rawTestDataAC = $parseTest['auto_correlation_raw'];
        
        $this->major_threshold = $param['major_threshold'];
        $this->error_threshold = $param['error_threshold'];
        $this->point_threshold = $param['point_threshold'];
        
    }

    protected function autocorrelation($series)
    {   
        //remove DC offset
        $mean = array_sum($series)/count($series);
        for ($x = 0; $x < count($series); $x++) {
            $series[$x] = $series[$x] - $mean;
        }

        //AC calculation
        $A = [];
        
        for ($x = 0; $x < count($series); $x++) {
            $a = 0;
            foreach ($series as $key => $value) {
                $a += $value * (isset($series[$key+$x]) ? $key+$x : 0);
            } 
            $A[] = $a;
        } 
        
        return $A;
    }

    protected function movingAverageLPF($series, $window_size){
        $newSeries = [];
        for ($i=0; $i < count($series); $i++) { 
            $tmp = $series[$i];
            for ($j=1; $j <= $window_size; $j++) { 
                if($i+$j < count($series)){
                    $tmp += $series[$i+$j];
                    $tmp += $series[$i];
                }
                else if($j <= $i){
                    $tmp += $series[$i];
                    $tmp += $series[$i-$j];
                }
                else{
                    $tmp += $series[$i];
                    $tmp += $series[$i];
                }
            }
            $newSeries[] = $tmp/($window_size*2+1);
        }

        return $newSeries;
    }

    protected function findMaxIndexOfAutocorrelation($arr, $t1, $t2)
    {   
        $t2_1 = ceil($t2/2);
        $t2_2 = $t2 - $t2_1;
        $max_index_arr = [];

        // for ($i=1; $i < $this->test_repeat_time; $i++) { 
        //     if(count(array_slice($arr, $i*$t1-$t2_1+1, $t2)) < 1){
        //         throw new Exception('Data Segmentation Error');
        //     }
        //     $max_index_arr[] = array_search(max(array_slice($arr, $i*$t1-$t2_1+1, $t2)), array_slice($arr, $i*$t1-$t2_1+1, $t2)) + $i*$t1-$t2_1+1;
        // }

        //metronome
        for ($i=1; $i < $this->test_repeat_time; $i++) { 
            if(count(array_slice($arr, $i*$t1-$t2_1+1, $t2)) < 1){
                throw new Exception('Data Segmentation Error');
            }
            $max_index_arr[] = 60*$i;
        }

        if ($this->test_repeat_time==1){
            return [count($arr)];
        }
        else return $max_index_arr;
    }

    protected function seperateEachRepeat($arr, $index)
    {
        $arr_seperated = [];

        $arr_seperated[] = $this->cutOffArr(array_slice($arr,0,$index[0]),-0.1,0.1,false);
        if(count($index)>1){
            for ($i=1; $i < count($index); $i++) { 
                $arr_seperated[] = $this->cutOffArr(array_slice($arr,$index[$i-1],$index[$i]-$index[$i-1]),-0.1,0.1,false);
            }
            $arr_seperated[] = $this->cutOffArr(array_slice($arr,$index[count($index)-1]),-0.1,0.1,false);
        }

        return $arr_seperated;
    }

    protected function parseTestData($test)
    {
        
        //[session, 1, samples_of_whole_practice, 9axis]

        //0531 改成[session, repeat, samples_of_one_repeat, 9axis]
        
        $testData = [];
        $testData_eachRepeat = [];
        $testData_autoCorrelation = [];

        $rawTestData = [];
        $rawTestData_eachRepeat = [];
        $rawTestData_autoCorrelation = [];

        foreach ($test as $key1 => $session) {
            Log::alert('session: '.strval(count($session)));
            Log::alert('session: '.strval(count($session[0])));
            Log::alert('session: '.strval(count($session[0][0])));
            // $t1 = round(count($session[0])/$this->test_repeat_time);
            // $t2 = round(count($session[0])/$this->test_repeat_time *2/3);

            foreach ($session as $key2 => $repeat) {
                $repeat_madgwick = Madgwick($repeat);
                $isShouldAbs = [false, false, false, false, false, false, false, false, false];

                foreach ($repeat as $key3 => $sample) {
                    
                    $ACC = calibrateByGravity(
                        [floatval($sample[6]),floatval($sample[7]),floatval($sample[8])],
                        [1,0,0],
                        [floatval($sample[0]),floatval($sample[1]),floatval($sample[2])]
                    );
                    $testData[$key1]['acc_x'][$key2][$key3] = round($ACC[0], 5);
                    $testData[$key1]['acc_y'][$key2][$key3] = round($ACC[1], 5);
                    $testData[$key1]['acc_z'][$key2][$key3] = round($ACC[2], 5);
                    $testData[$key1]['acc'][$key2][$key3] = round(floatval(sqrt($sample[0]**2 + $sample[1]**2 + $sample[2]**2)),5);

                    $testData[$key1]['gyro_x'][$key2][$key3] = round(floatval($sample[3]),5);
                    $testData[$key1]['gyro_y'][$key2][$key3] = round(floatval($sample[4]),5);
                    $testData[$key1]['gyro_z'][$key2][$key3] = round(floatval($sample[5]),5);
                    $testData[$key1]['gyro'][$key2][$key3] = round(floatval(sqrt($sample[3]**2 + $sample[4]**2 + $sample[5]**2)),5);

                    $testData[$key1]['roll_madgwick'][$key2][$key3] = round(floatval($repeat_madgwick[$key3][0]),5);
                    $testData[$key1]['yaw_madgwick'][$key2][$key3] = round(floatval($repeat_madgwick[$key3][1]),5);
                    $testData[$key1]['pitch_madgwick'][$key2][$key3] = round(floatval($repeat_madgwick[$key3][2]),5);

                    //絕對值版本
                    /*
                    $testData[$key1]["roll"][$key3]
                    $testData[$key1]["yaw"][$key3]
                    $testData[$key1]["pitch"][$key3]
                    */

                    if($key3 > 0){
                        $last_sample = $repeat[$key3-1];
                    }
                    else{
                        $last_sample = [0,0,0,0,0,0,0,0,0];
                    }
                    
                    if(abs(floatval($sample[6]) - floatval($last_sample[6])) > 5){
                        if(floatval($sample[6]) < 0){
                            $isShouldAbs[6] = true;
                        }
                        else{
                            $isShouldAbs[6] = false;
                        }
                    }
                    if(abs(floatval($sample[7]) - floatval($last_sample[7])) > 5){
                        if(floatval($sample[7]) < 0){
                            $isShouldAbs[7] = true;
                        }
                        else{
                            $isShouldAbs[7] = false;
                        }
                    }
                    if(abs(floatval($sample[8]) - floatval($last_sample[8])) > 5){
                        if(floatval($sample[8]) < 0){
                            $isShouldAbs[8] = true;
                        }
                        else{
                            $isShouldAbs[8] = false;
                        }
                    }

                    if($isShouldAbs[6]){
                        $testData[$key1]['roll'][$key2][$key3] = round(abs(floatval($sample[6])), 5);
                    }
                    else{
                        $testData[$key1]['roll'][$key2][$key3] = round(floatval($sample[6]), 5);
                    }
                    if($isShouldAbs[7]){
                        $testData[$key1]['yaw'][$key2][$key3] = round(abs(floatval($sample[7])), 5);
                    }
                    else{
                        $testData[$key1]['yaw'][$key2][$key3] = round(floatval($sample[7]), 5);
                    }
                    if($isShouldAbs[8]){
                        $testData[$key1]['pitch'][$key2][$key3] = round(abs(floatval($sample[8])), 5);
                    }
                    else{
                        $testData[$key1]['pitch'][$key2][$key3] = round(floatval($sample[8]), 5);
                    }
                    //絕對值版本end
                    
                    $rawTestData[$key1]['acc_x'][$key2][$key3] = round(floatval($sample[0]),5);
                    $rawTestData[$key1]['acc_y'][$key2][$key3] = round(floatval($sample[1]),5);
                    $rawTestData[$key1]['acc_z'][$key2][$key3] = round(floatval($sample[2]),5);
                    $rawTestData[$key1]['gyro_x'][$key2][$key3] = round(floatval($sample[3]),5);
                    $rawTestData[$key1]['gyro_y'][$key2][$key3] = round(floatval($sample[4]),5);
                    $rawTestData[$key1]['gyro_z'][$key2][$key3] = round(floatval($sample[5]),5);
                    $rawTestData[$key1]['roll'][$key2][$key3] = round(floatval($sample[6]),5);
                    $rawTestData[$key1]['yaw'][$key2][$key3] = round(floatval($sample[7]),5);
                    $rawTestData[$key1]['pitch'][$key2][$key3] = round(floatval($sample[8]),5);
                    $rawTestData[$key1]['roll_madgwick'][$key2][$key3] = round(floatval($repeat_madgwick[$key3][0]),5);
                    $rawTestData[$key1]['yaw_madgwick'][$key2][$key3] = round(floatval($repeat_madgwick[$key3][1]),5);
                    $rawTestData[$key1]['pitch_madgwick'][$key2][$key3] = round(floatval($repeat_madgwick[$key3][2]),5);
                }
                //LPF
                // $testData[$key1]['acc_x'] = $this->movingAverageLPF($testData[$key1]['acc_x'], 5);
                // $testData[$key1]['acc_y'] = $this->movingAverageLPF($testData[$key1]['acc_y'], 5);
                // $testData[$key1]['acc_z'] = $this->movingAverageLPF($testData[$key1]['acc_z'], 5);
                // $testData[$key1]['roll'] = $this->movingAverageLPF($testData[$key1]['roll'], 5);
                // $testData[$key1]['yaw'] = $this->movingAverageLPF($testData[$key1]['yaw'], 5);
                // $testData[$key1]['pitch'] = $this->movingAverageLPF($testData[$key1]['pitch'], 5);
            }
        }
        return [
            "each_repeat" => $testData,
            "each_repeat_raw" => $rawTestData,
        ];
    }

    protected function parseTemplateData($template)
    {
        //[repeat, samples_of_each_repeat, 9axis]

        //[1, samples_of_all_repeats, 9axis]

        $templateData = [];
        $rawTemplateData = [];
        foreach ($template as $key1 => $repeat) {
            $repeat_madgwick = Madgwick($repeat);
            foreach ($repeat as $key2 => $sample) {

                $ACC = calibrateByGravity(
                    [floatval($sample[6]),floatval($sample[7]),floatval($sample[8])],
                    [1,0,0],
                    [floatval($sample[0]),floatval($sample[1]),floatval($sample[2])]
                );
                $templateData['acc_x'][$key1][$key2] = round($ACC[0], 5);
                $templateData['acc_y'][$key1][$key2] = round($ACC[1], 5);
                $templateData['acc_z'][$key1][$key2]= round($ACC[2], 5);
                $templateData['acc'][$key1][$key2] = round(floatval(sqrt($sample[0]**2 + $sample[1]**2 + $sample[2]**2)),5);

                $templateData['gyro_x'][$key1][$key2] = round($sample[3], 5);
                $templateData['gyro_y'][$key1][$key2] = round($sample[4], 5);
                $templateData['gyro_z'][$key1][$key2]= round($sample[5], 5);
                $templateData['gyro'][$key1][$key2] = round(floatval(sqrt($sample[3]**2 + $sample[4]**2 + $sample[5]**2)),5);

                $templateData['roll_madgwick'][$key1][$key2] = round(floatval($repeat_madgwick[$key2][0]),5);
                $templateData['yaw_madgwick'][$key1][$key2] = round(floatval($repeat_madgwick[$key2][1]),5);
                $templateData['pitch_madgwick'][$key1][$key2] = round(floatval($repeat_madgwick[$key2][2]),5);

                // 絕對值版本
                if(abs(floatval($sample[6])) > 2.6){
                    $templateData['roll'][$key1][$key2] = round(abs(floatval($sample[6])), 5);
                }
                else{
                    $templateData['roll'][$key1][$key2] = round(floatval($sample[6]), 5);
                }
                if(abs(floatval($sample[7])) > 2.6){
                    $templateData['yaw'][$key1][$key2] = round(abs(floatval($sample[7])), 5);
                }
                else{
                    $templateData['yaw'][$key1][$key2] = round(floatval($sample[7]), 5);
                }
                if(abs(floatval($sample[8])) > 2.6){
                    $templateData['pitch'][$key1][$key2] = round(abs(floatval($sample[8])), 5);
                }
                else{
                    $templateData['pitch'][$key1][$key2] = round(floatval($sample[8]), 5);
                }
                //絕對值版本end

                
                $rawTemplateData['acc_x'][$key1][$key2] = round(floatval($sample[0]),5);
                $rawTemplateData['acc_y'][$key1][$key2] = round(floatval($sample[1]),5);
                $rawTemplateData['acc_z'][$key1][$key2] = round(floatval($sample[2]),5);
                $rawTemplateData['gyro_x'][$key1][$key2] = round(floatval($sample[3]),5);
                $rawTemplateData['gyro_y'][$key1][$key2] = round(floatval($sample[4]),5);
                $rawTemplateData['gyro_z'][$key1][$key2] = round(floatval($sample[5]),5);
                $rawTemplateData['roll'][$key1][$key2] = round(floatval($sample[6]),5);
                $rawTemplateData['yaw'][$key1][$key2] = round(floatval($sample[7]),5);
                $rawTemplateData['pitch'][$key1][$key2] = round(floatval($sample[8]),5);
                $rawTemplateData['roll_madgwick'][$key1][$key2] = round(floatval($repeat_madgwick[$key2][0]),5);
                $rawTemplateData['yaw_madgwick'][$key1][$key2] = round(floatval($repeat_madgwick[$key2][1]),5);
                $rawTemplateData['pitch_madgwick'][$key1][$key2] = round(floatval($repeat_madgwick[$key2][2]),5);
                
            }
            $templateData['acc_x'][$key1] = $this->cutOffArr($templateData['acc_x'][$key1],-0.1,0.1,false);
            $templateData['acc_y'][$key1] = $this->cutOffArr($templateData['acc_y'][$key1],-0.1,0.1,false);
            $templateData['acc_z'][$key1] = $this->cutOffArr($templateData['acc_z'][$key1],-0.1,0.1,false);
            $templateData['acc'][$key1] = $this->cutOffArr($templateData['acc'][$key1],-0.1,0.1,false);
            $templateData['gyro_x'][$key1] = $this->cutOffArr($templateData['gyro_x'][$key1],-0.1,0.1,false);
            $templateData['gyro_y'][$key1] = $this->cutOffArr($templateData['gyro_y'][$key1],-0.1,0.1,false);
            $templateData['gyro_z'][$key1] = $this->cutOffArr($templateData['gyro_z'][$key1],-0.1,0.1,false);
            $templateData['gyro'][$key1] = $this->cutOffArr($templateData['gyro'][$key1],-0.1,0.1,false);
            $templateData['roll'][$key1] = $this->cutOffArr($templateData['roll'][$key1],-0.1,0.1,false);
            $templateData['yaw'][$key1] = $this->cutOffArr($templateData['yaw'][$key1],-0.1,0.1,false);
            $templateData['pitch'][$key1] = $this->cutOffArr($templateData['pitch'][$key1],-0.1,0.1,false);
            $templateData['roll_madgwick'][$key1] = $this->cutOffArr($templateData['roll_madgwick'][$key1],-0.1,0.1,false);
            $templateData['yaw_madgwick'][$key1] = $this->cutOffArr($templateData['yaw_madgwick'][$key1],-0.1,0.1,false);
            $templateData['pitch_madgwick'][$key1] = $this->cutOffArr($templateData['pitch_madgwick'][$key1],-0.1,0.1,false);
            
            $rawTemplateData['acc_x'][$key1] = $this->cutOffArr($rawTemplateData['acc_x'][$key1],-0.1,0.1,false);
            $rawTemplateData['acc_y'][$key1] = $this->cutOffArr($rawTemplateData['acc_y'][$key1],-0.1,0.1,false);
            $rawTemplateData['acc_z'][$key1] = $this->cutOffArr($rawTemplateData['acc_z'][$key1],-0.1,0.1,false);
            $rawTemplateData['gyro_x'][$key1] = $this->cutOffArr($rawTemplateData['gyro_x'][$key1],-0.1,0.1,false);
            $rawTemplateData['gyro_y'][$key1] = $this->cutOffArr($rawTemplateData['gyro_y'][$key1],-0.1,0.1,false);
            $rawTemplateData['gyro_z'][$key1] = $this->cutOffArr($rawTemplateData['gyro_z'][$key1],-0.1,0.1,false);
            $rawTemplateData['roll'][$key1] = $this->cutOffArr($rawTemplateData['roll'][$key1],-0.1,0.1,false);
            $rawTemplateData['yaw'][$key1] = $this->cutOffArr($rawTemplateData['yaw'][$key1],-0.1,0.1,false);
            $rawTemplateData['pitch'][$key1] = $this->cutOffArr($rawTemplateData['pitch'][$key1],-0.1,0.1,false);
            $rawTemplateData['roll_madgwick'][$key1] = $this->cutOffArr($rawTemplateData['roll_madgwick'][$key1],-0.1,0.1,false);
            $rawTemplateData['yaw_madgwick'][$key1] = $this->cutOffArr($rawTemplateData['yaw_madgwick'][$key1],-0.1,0.1,false);
            $rawTemplateData['pitch_madgwick'][$key1] = $this->cutOffArr($rawTemplateData['pitch_madgwick'][$key1],-0.1,0.1,false);
        }

        return [
                "template"=>$templateData,
                "template_raw"=>$rawTemplateData
        ];
    }

    protected function parseRawTemplateData($template)
    {
        $templateData = [];
        foreach ($template as $key1 => $repeat) {
            foreach ($repeat as $key2 => $sample) {

                $templateData['acc'][$key1][$key2] = round(floatval(sqrt($sample[0]**2 + $sample[1]**2 + $sample[2]**2)),5);

                $templateData['gyro'][$key1][$key2] = round(floatval(sqrt($sample[3]**2 + $sample[4]**2 + $sample[5]**2)),5);
                
                $templateData['acc_x'][$key1][$key2] = round(floatval($sample[0]),5);
                $templateData['acc_y'][$key1][$key2] = round(floatval($sample[1]),5);
                $templateData['acc_z'][$key1][$key2] = round(floatval($sample[2]),5);
                $templateData['roll'][$key1][$key2] = round(floatval($sample[3]),5);
                $templateData['yaw'][$key1][$key2] = round(floatval($sample[4]),5);
                $templateData['pitch'][$key1][$key2] = round(floatval($sample[5]),5);
                $templateData['rot_x'][$key1][$key2] = round(floatval($sample[6]),5);
                $templateData['rot_y'][$key1][$key2] = round(floatval($sample[7]),5);
                $templateData['rot_z'][$key1][$key2] = round(floatval($sample[8]),5);
            }
            $templateData['acc_x'][$key1] = $this->cutOffArr($templateData['acc_x'][$key1],-0.1,0.1,false);
            $templateData['acc_y'][$key1] = $this->cutOffArr($templateData['acc_y'][$key1],-0.1,0.1,false);
            $templateData['acc_z'][$key1] = $this->cutOffArr($templateData['acc_z'][$key1],-0.1,0.1,false);
            $templateData['acc'][$key1] = $this->cutOffArr($templateData['acc'][$key1],-0.1,0.1,false);
            $templateData['roll'][$key1] = $this->cutOffArr($templateData['roll'][$key1],-0.1,0.1,false);
            $templateData['yaw'][$key1] = $this->cutOffArr($templateData['yaw'][$key1],-0.1,0.1,false);
            $templateData['pitch'][$key1] = $this->cutOffArr($templateData['pitch'][$key1],-0.1,0.1,false);
            $templateData['gyro'][$key1] = $this->cutOffArr($templateData['gyro'][$key1],-0.1,0.1,false);
        }

        return $templateData;
    }

    protected function getTemplateTime($template)
    {
        $templateTime = [];
        foreach ($template as $key1 => $repeat) {
            $templateTime[] = count($repeat);
        }
        Log::alert('templateTime: '.strval(array_sum($templateTime)));  
        return array_sum($templateTime);
    }

    protected function exception($message)
    {
        throw new Exception($message);
    }

    protected function stats_standard_deviation(array $a, $sample = false) 
    {
        $n = count($a);
        if ($n === 0) {
            //The array has zero element
            return 0;
        }
        if ($sample && $n === 1) {
            //The array has only 1 element
            return 0;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        return sqrt($carry / $n);
    }

    protected function cutOffArr(array $a , $min, $max, $enable){
        if(!$enable){
            return $a;
        }
        else{
            $tmp = [];
            foreach ($a as $key => $value) {
                if($value>$max || $value<$min){
                    $tmp[] = $value;
                }
                else{
                    $tmp[] = 0;
                }
            }
            return $tmp;
        }
    }

    protected function cutOffNum($n , $min, $max){
        if($n>$max || $n<$min){
            return $n;
        }
        else{
            return 0;
        }
    }
}








