<?php

function transpose($A){
    $ans = array();
    for($x = 0; $x <= count($A[0])-1; $x++){
        for($y = 0; $y <= count($A)-1; $y++){
            $ans[$x][$y] = $A[$y][$x];
        }
    }
    return $ans;
}

function norm2of($A){
    $tot = 0; 
    for($x = 0; $x <= count($A) - 1; $x++){
        for($y = 0; $y <= count($A[$x]) - 1; $y++){
            $tot += pow($A[$x][$y],2);
        }
    }
    $ans = pow($tot,0.5);
    return $ans;
}

function arrayDivide($AR,$num){
    $ans = [];
    for($a = 0; $a <= count($AR) - 1; $a++){
        for($b = 0; $b <= count($AR[0]) - 1; $b++){
            $ans[$a][$b] = $AR[$a][$b] / $num;
        }
    }
    return $ans;
}

function M_mult($_A,$_B) {
    // AxB outcome is C with A's rows and B'c cols
    $r = count($_A);
    $c = count($_B[0]);
    $in= count($_B); // or $_A[0]. $in is 'inner' count
  
    if ( $in != count($_A[0]) ) {
      print("ERROR: need to have inner size of matrices match.\n");
      print("     : trying to multiply a ".count($_A)."x".count($_A[0])." by a ".count($_B)."x".count($_B[0])." matrix.\n");
      print("\n");
      exit(1);
    }
  
    // allocate retval
    $retval = array();
    for($i=0;$i< $r; $i++) { $retval[$i] = array(); }
    // multiplication here
    for($ri=0;$ri<$r;$ri++) {
        for($ci=0;$ci<$c;$ci++) {
            $retval[$ri][$ci] = 0.0;
            for($j=0;$j<$in;$j++) {
                $retval[$ri][$ci] += $_A[$ri][$j] * $_B[$j][$ci];
            }
        }
    }
    return $retval;
}

function quaternProd($A,$B){
    $ans = array();
    for($x = 0; $x <= count($A)-1; $x++){
        $ans[$x][0] = $A[$x][0]*$B[$x][0]-$A[$x][1]*$B[$x][1]-$A[$x][2]*$B[$x][2]-$A[$x][3]*$B[$x][3];
        $ans[$x][1] = $A[$x][0]*$B[$x][1]+$A[$x][1]*$B[$x][0]+$A[$x][2]*$B[$x][3]-$A[$x][3]*$B[$x][2];
        $ans[$x][2] = $A[$x][0]*$B[$x][2]-$A[$x][1]*$B[$x][3]+$A[$x][2]*$B[$x][0]+$A[$x][3]*$B[$x][1];
        $ans[$x][3] = $A[$x][0]*$B[$x][3]+$A[$x][1]*$B[$x][2]-$A[$x][2]*$B[$x][1]+$A[$x][3]*$B[$x][0];
    }
    return $ans;
}

function quaternConj($A){
    $ans = array();
    for($x = 0; $x <= count($A)-1; $x++){
        $ans[$x][0] = $A[$x][0];
        $ans[$x][1] = -$A[$x][1];
        $ans[$x][2] = -$A[$x][2];
        $ans[$x][3] = -$A[$x][3];
    }
    return $ans;
}

function quatern2euler($q){
    $ans = array();
    $euler = array();
    
    for($x = 0; $x <= count($q)-1; $x++){
        $ans[0][0][$x] = 2*pow($q[$x][0],2) - 1 + 2*pow($q[$x][1],2);
        $ans[1][0][$x] = 2*($q[$x][1]*$q[$x][2] - $q[$x][0]*$q[$x][3]);
        $ans[2][0][$x] = 2*($q[$x][1]*$q[$x][3] + $q[$x][0]*$q[$x][2]);
        $ans[2][1][$x] = 2*($q[$x][2]*$q[$x][3] - $q[$x][0]*$q[$x][1]);
        $ans[2][2][$x] = 2*pow($q[$x][0],2) - 1 + 2*pow($q[$x][3],2);
    }

    for($y = 0; $y <= count($q)-1; $y++){
        $euler[$y][0] = atan2($ans[2][1][$y], $ans[2][2][$y]);
        $euler[$y][1] = -atan($ans[2][0][$y]/pow(1-pow($ans[2][0][$y],2),0.5));
        $euler[$y][2] = atan2($ans[1][0][$y], $ans[0][0][$y]);
    }

    return $euler;
}

class MadgwickAHRS{
    public $SamplePeriod = 1/256;
    public $Quaternion = array(array(1,0,0,0));
    public $Beta = 1;

    //If you want to change parameters above, use this function.
    function Property($SamplePeriod, $Quaternion, $Beta){
        $this->SamplePeriod = $SamplePeriod;
        $this->Quaternion = $Quaternion;
        $this->Beta = $Beta;
    }

    //Calculation of quaternion of IMU
    function UpdateIMU($Data){

        $Accelerometer = [[$Data[0], $Data[1], $Data[2]]];
        //Gyroscope unit should be radian
        $Gyroscope = [[$Data[3], $Data[4], $Data[5]]];
        $q = $this->Quaternion;
        if(norm2of($Accelerometer) == 0){
            return "Accelerometer cannot be 0";
        }
        $Accelerometer = arrayDivide($Accelerometer,norm2of($Accelerometer));
        

        $F = array(array(2*($q[0][1]*$q[0][3] - $q[0][0]*$q[0][2]) - $Accelerometer[0][0]), 
        array(2*($q[0][0]*$q[0][1] + $q[0][2]*$q[0][3]) - $Accelerometer[0][1]), 
        array(2*(0.5 - pow($q[0][1],2) - pow($q[0][2],2)) - $Accelerometer[0][2]));

        $J = array(array(-2*$q[0][2], 2*$q[0][3], -2*$q[0][0], 2*$q[0][1]),
        array(2*$q[0][1], 2*$q[0][0], 2*$q[0][3], 2*$q[0][2]),
        array(0, -4*$q[0][1], -4*$q[0][2], 0));

        $step = M_mult(transpose($J),$F);
        $step = arrayDivide($step,norm2of($step));

        $qDot = array();
        $num = quaternProd($q,array(array(0,$Gyroscope[0][0],$Gyroscope[0][1],$Gyroscope[0][2])));
        for($x = 0; $x <= 3; $x++){
            $qDot[0][$x] = 0.5*$num[0][$x] - $this->Beta*transpose($step)[0][$x];
        }
        
        //print_r($qDot);
        for($x = 0; $x <=3; $x++){
            $q[0][$x] = $q[0][$x] + $this->SamplePeriod*$qDot[0][$x];
        }

        $this->Quaternion = arrayDivide($q,norm2of($q));
    }
}

function Madgwick($Data){
    $ans = array();
    $MadgwickAHRS = new MadgwickAHRS;
    $quaternion = array();
    $eulerAngle = array();
    $quaCorrected = array();

    //If there is any change of sampling period, quaternion, or beta, please delete the '//' in next line and change the parameters.
    $MadgwickAHRS->Property(0.05,[[1,0,0,0]],0.0001);

    for($t = 0; $t <= count($Data)-1; $t++){
        $MadgwickAHRS->UpdateIMU($Data[$t]);
        $quaternion[$t] = $MadgwickAHRS->Quaternion[0];
        $quaCorrected[$t] = [[$quaternion[$t][0], -$quaternion[$t][1], -$quaternion[$t][2], -$quaternion[$t][3]]];
        $eulerAngle[$t] = [quatern2euler($quaCorrected[$t])[0][0] ,quatern2euler($quaCorrected[$t])[0][1] ,quatern2euler($quaCorrected[$t])[0][2]];
    }
    return $eulerAngle;
}


?>