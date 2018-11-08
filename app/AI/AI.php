<?php

namespace App\AI;

use Exception;
use App\ServicePlanVideo;
use Log;

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
        $this->templateData = $this->parseTemplateData($template);
        $this->testData = $this->parseTestData($test);
        
        $this->major_threshold = $param['major_threshold'];
        $this->error_threshold = $param['error_threshold'];
        $this->point_threshold = $param['point_threshold'];
    }

    protected function autocorrelation($series)
    {   
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

    protected function findMaxIndexOfAutocorrelation($arr, $t1, $t2)
    {   
        $t2_1 = ceil($t2/2);
        $t2_2 = $t2 - $t2_1;
        $max_index_arr = [];

        for ($i=1; $i < $this->test_repeat_time; $i++) { 
            $max_index_arr[] = array_search(max(array_slice($arr, $i*$t1-$t2_1+1, $t2)), array_slice($arr, $i*$t1-$t2_1+1, $t2)) + $i*$t1-$t2_1+1;
        }
        if ($this->test_repeat_time==1){
            return [count($arr)];
        }
        else return $max_index_arr;
    }

    protected function seperateEachRepeat($arr, $index)
    {
        $arr_seperated = [];
        $arr_seperated[] = array_slice($arr,0,$index[0]);
        for ($i=1; $i < count($index); $i++) { 
            $arr_seperated[] = array_slice($arr,$index[$i-1],$index[$i]-$index[$i-1]);
        }
        $arr_seperated[] = array_slice($arr,$index[count($index)-1]);

        return $arr_seperated;
    }

    protected function parseTestData($test)
    {
        $testData = [];
        $testData_eachRepeat = [];

        foreach ($test as $key1 => $session) {
            Log::alert('session: '.strval(count($session)));
            Log::alert('session: '.strval(count($session[0])));
            Log::alert('session: '.strval(count($session[0][0])));
            $t1 = round(count($session[0])/$this->test_repeat_time);
            $t2 = round(count($session[0])/$this->test_repeat_time *2/3);
            foreach ($session[0] as $key2 => $sample) {

                $testData[$key1]['acc_x'][$key2] = round(floatval($sample[0]),5);
                $testData[$key1]['acc_y'][$key2] = round(floatval($sample[1]),5);
                $testData[$key1]['acc_z'][$key2] = round(floatval($sample[2]),5);
                $testData[$key1]['acc'][$key2] = round(floatval(sqrt($sample[0]**2 + $sample[1]**2 + $sample[2]**2)),5);
                $testData[$key1]['roll'][$key2] = round(floatval($sample[3]),5);
                $testData[$key1]['yaw'][$key2] = round(floatval($sample[4]),5);
                $testData[$key1]['pitch'][$key2] = round(floatval($sample[5]),5);
                $testData[$key1]['gyro'][$key2] = round(floatval(sqrt($sample[3]**2 + $sample[4]**2 + $sample[5]**2)),5);
                //$testData[$key1]['rot_x'][$key2] = round(floatval($sample[6]),5);
                //$testData[$key1]['rot_y'][$key2] = round(floatval($sample[7]),5);
                //$testData[$key1]['rot_z'][$key2] = round(floatval($sample[8]),5);
            }
            Log::alert(strval($t1));
            Log::alert(strval($t2));
            Log::alert(strval(count($this->autocorrelation($testData[$key1]['acc_x']))));
            $testData_eachRepeat[$key1]['acc_x'] = 
                $this->seperateEachRepeat(  $testData[$key1]['acc_x'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['acc_x']), $t1, $t2
                                            )
                );

            $testData_eachRepeat[$key1]['acc_y'] = 
                $this->seperateEachRepeat(  $testData[$key1]['acc_y'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['acc_y']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['acc_z'] = 
                $this->seperateEachRepeat(  $testData[$key1]['acc_z'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['acc_z']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['acc'] = 
                $this->seperateEachRepeat(  $testData[$key1]['acc'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['acc']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['roll'] = 
                $this->seperateEachRepeat(  $testData[$key1]['roll'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['roll']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['yaw'] = 
                $this->seperateEachRepeat(  $testData[$key1]['yaw'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['yaw']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['pitch'] = 
                $this->seperateEachRepeat(  $testData[$key1]['pitch'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['pitch']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['gyro'] = 
                $this->seperateEachRepeat(  $testData[$key1]['gyro'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['gyro']), $t1, $t2
                                            )
                );
            /*
            $testData_eachRepeat[$key1]['rot_x'] = 
                $this->seperateEachRepeat(  $testData[$key1]['rot_x'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['rot_x']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['rot_y'] = 
                $this->seperateEachRepeat(  $testData[$key1]['rot_y'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['rot_y']), $t1, $t2
                                            )
                );
            $testData_eachRepeat[$key1]['rot_z'] = 
                $this->seperateEachRepeat(  $testData[$key1]['rot_z'], 
                                            $this->findMaxIndexOfAutocorrelation(
                                                $this->autocorrelation($testData[$key1]['rot_z']), $t1, $t2
                                            )
                );
            */
        }
        /*
        Log::debug('test max acc_x: '.max($testData_eachRepeat[0]['acc_x'][0]));
        Log::debug('test min acc_x: '.min($testData_eachRepeat[0]['acc_x'][0]));
        Log::debug('test max acc_y: '.max($testData_eachRepeat[0]['acc_y'][0]));
        Log::debug('test min acc_y: '.min($testData_eachRepeat[0]['acc_y'][0]));
        Log::debug('test max acc_z: '.max($testData_eachRepeat[0]['acc_z'][0]));
        Log::debug('test min acc_z: '.min($testData_eachRepeat[0]['acc_z'][0]));
        Log::debug('test max roll: '.max($testData_eachRepeat[0]['roll'][0]));
        Log::debug('test min roll: '.min($testData_eachRepeat[0]['roll'][0]));
        Log::debug('test max yaw: '.max($testData_eachRepeat[0]['yaw'][0]));
        Log::debug('test min yaw: '.min($testData_eachRepeat[0]['yaw'][0]));
        Log::debug('test max pitch: '.max($testData_eachRepeat[0]['pitch'][0]));
        Log::debug('test min pitch: '.min($testData_eachRepeat[0]['pitch'][0]));
        */
        return $testData_eachRepeat;
    }

    protected function parseTemplateData($template)
    {
        $templateData = [];
        foreach ($template as $key1 => $repeat) {
            foreach ($repeat as $key2 => $sample) {

                $templateData['acc_x'][$key1][$key2] = round(floatval($sample[0]),5);
                $templateData['acc_y'][$key1][$key2] = round(floatval($sample[1]),5);
                $templateData['acc_z'][$key1][$key2] = round(floatval($sample[2]),5);
                $templateData['acc'][$key2] = round(floatval(sqrt($sample[0]**2 + $sample[1]**2 + $sample[2]**2)),5);
                $templateData['roll'][$key1][$key2] = round(floatval($sample[3]),5);
                $templateData['yaw'][$key1][$key2] = round(floatval($sample[4]),5);
                $templateData['pitch'][$key1][$key2] = round(floatval($sample[5]),5);
                $templateData['gyro'][$key2] = round(floatval(sqrt($sample[3]**2 + $sample[4]**2 + $sample[5]**2)),5);
                //$templateData['rot_x'][$key1][$key2] = round(floatval($sample[6]),5);
                //$templateData['rot_y'][$key1][$key2] = round(floatval($sample[7]),5);
                //$templateData['rot_z'][$key1][$key2] = round(floatval($sample[8]),5);
            }
        }
        /*
        Log::debug('template max acc_x: '.max($templateData['acc_x'][0]));
        Log::debug('template min acc_x: '.min($templateData['acc_x'][0]));
        Log::debug('template max acc_y: '.max($templateData['acc_y'][0]));
        Log::debug('template min acc_y: '.min($templateData['acc_y'][0]));
        Log::debug('template max acc_z: '.max($templateData['acc_z'][0]));
        Log::debug('template min acc_z: '.min($templateData['acc_z'][0]));
        Log::debug('template max roll: '.max($templateData['roll'][0]));
        Log::debug('template min roll: '.min($templateData['roll'][0]));
        Log::debug('template max yaw: '.max($templateData['yaw'][0]));
        Log::debug('template min yaw: '.min($templateData['yaw'][0]));
        Log::debug('template max pitch: '.max($templateData['pitch'][0]));
        Log::debug('template min pitch: '.min($templateData['pitch'][0]));
        */
        return $templateData;
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
}

/*
TEMPLATE
9 * n * 5
self.motionData.append(JSON([
                          motion["acc_x"].doubleValue
                        , motion["acc_y"].doubleValue
                        , motion["acc_z"].doubleValue
                        , motion["roll"].doubleValue
                        , motion["yaw"].doubleValue
                        , motion["pitch"].doubleValue
                        , motion["rot_x"].doubleValue
                        , motion["rot_y"].doubleValue
                        , motion["rot_z"].doubleValue
                        ]))

[
    [
        [0.024658203125, -0.0090789794921875, -1.0114898681640625, 0.04128864706830027, -0.0003047396089430681, 0.007376584074233626, -0.016968330885439867, 0.0610953122513988, -0.016968330885439867], 
        [0.0235595703125, -0.14227294921875, -0.9936676025390624, 0.044158343056446926, -0.01485044936786902, 0.11802145929113354, -0.06859450756495292, 0.017500696787346705, -0.06859450756495292], 
        [-0.15081787109375, -0.08221435546875, -0.7280426025390625, -0.27058585804783897, 0.05220591334391579, 0.31963911522163974, -0.16100832791957456, 0.9691189943576544, -0.16100832791957456], 
        [-0.07586669921875, -0.123931884765625, -1.0031280517578125, -0.14592297233910273, 0.1272755627559553, 0.16805408859833043, 0.03932476928832805, 0.3936982997397218, 0.03932476928832805]
    ], 
    [
        [-0.0291290283203125, -0.161346435546875, -1.0794677734375, -0.02357609079746995, 0.005686661031244979, 0.24356351652224495, 0.33659027227238975, -0.08223228919113967, 0.33659027227238975], 
        [-0.5817108154296875, -0.0835723876953125, -0.258331298828125, -0.22080841644569896, -1.1228300900040014, 0.24515869294655143, -1.7552279554500452, -0.2869612000943271, -1.7552279554500452], 
        [-0.125640869140625, -0.054962158203125, -0.5734405517578125, 0.011008961706848725, -1.19419142819574, 0.7384492259069978, -3.9806202234387125, -1.1269424479296035, -3.9806202234387125], 
        [0.1114654541015625, -0.3296661376953125, -0.6773223876953125, -0.0258759823470726, -1.3460068846356412, 0.41759681291648154, -0.022778283119556732, 0.49635916502064137, -0.022778283119556732]
    ], 
    [
        [0.05377197265625, -0.7397308349609375, -0.64794921875, 0.05395040287419033, -0.04056103507374874, 0.8503379361781204, 0.33526082225622217, -0.22833810028284884, 0.33526082225622217], 
        [0.01617431640625, -0.3025360107421875, -0.9872283935546876, 0.018241399992594404, -1.7402470816027065, 0.694553012003373, -0.46461481745911487, -2.0509703966066906, -0.46461481745911487], 
        [-0.284423828125, -0.0565643310546875, -0.52532958984375, 0.07373254377404985, -1.153160349176442, 0.5951763090427449, 1.324114639239691, -0.09423968359797993, 1.324114639239691], 
        [-0.006927490234375, -0.368896484375, -0.83197021484375, 0.0062654860758347974, -1.8425311588850855, 0.11111294112864048, 1.7553587166595677, 0.29193731659114014, 1.7553587166595677], 
        [-0.0506134033203125, -0.615875244140625, -0.7657012939453125, 0.15190812912600196, -2.2443473579886017, 0.7047155271833927, 0.21069385911274688, -0.1246987896174342, 0.21069385911274688]
    ], 
    [
        [-0.6710968017578125, 0.0630950927734375, -1.636566162109375, -0.4656625438800464, 0.011327823854643764, 0.022544855647724286, 2.286337644270694, 0.7058175837037005, 2.286337644270694], 
        [-0.3049774169921875, -0.3490447998046875, -1.1884918212890625, -0.5560000111142834, -0.11302998428822485, 0.3647004101292989, 1.8861862388936017, -0.3154128152840796, 1.8861862388936017], 
        [0.1341705322265625, -0.778778076171875, -0.4659881591796875, 0.03323076543802283, -0.44081613060845914, 0.8117906948737219, 0.2965808042678023, 0.487509214402199, 0.2965808042678023]
    ], 
    [
        [-0.121917724609375, 0.4210205078125, -1.0933380126953125, 0.28434789251108106, 0.10434692076637456, -0.36647490937424754, 6.866148074004624, -0.679348159313294, 6.866148074004624], 
        [-0.2153167724609375, 0.167877197265625, -0.076507568359375, 1.041088656926114, 0.7777264992799511, -0.772337879678817, 4.469463947863149, -0.9035343914567091, 4.469463947863149], 
        [0.197662353515625, -0.18536376953125, 0.1292724609375, -0.2966349589294145, 1.9319782311372946, 1.0606908579966434, -0.2886411221099491, -0.2682753966218951, -0.2886411221099491], 
        [0.1042327880859375, -0.0421142578125, -0.1387939453125, -0.6783165358526227, -3.1186160878176588, 0.8948543297568295, -3.5214675493753496, 1.4288200132894644, -3.5214675493753496]
    ]
]
*/

/*
TEST
9 * n * repeat_time * session
{   
    "data": [
                [
                    [[-0.0173187255859375, -0.01495361328125, -0.990386962890625, -0.018049177619389647, 0.0002670092495838629, 0.014792365483688376, -0.020303940150764112, 0.013993846263930364, -0.020303940150764112]], 
                    [[-0.01812744140625, -0.0142974853515625, -0.9892730712890624, -0.01790411294747475, 0.0002514253182548906, 0.014041843009830987, -0.019234680973097298, 0.01292831551178966, -0.019234680973097298]]
                ], 
                [
                    [[-0.0186004638671875, -0.0170440673828125, -0.9913482666015624, -0.01704354299194999, 0.00024074375789691853, 0.014124319570865995, -0.01923414834087928, 0.015058844383853052, -0.01923414834087928]], 
                    [[-0.01861572265625, -0.01531982421875, -0.9904327392578124, -0.017301841670333946, 0.00025094189906109724, 0.01450283100941231, -0.01923441465698829, 0.013993579947821355, -0.01923441465698829]]
                ]
            ], 
    "stop_at": "2018-09-29 10:20:10", 
    "start_at": "2018-09-29 10:19:48"
}
*/








