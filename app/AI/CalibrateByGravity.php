<?php

namespace App\AI;

use Exception;
use Log;
include 'math.php';
include 'Rodrigues.php';

function calibrateByGravity($g0, $g1, $a0){

    $theta = acos( dot_product($go,$g1)/(euclidean_distance($g0)*euclidean_distance($g1)) );
    $axis = [
        $g0[1]*$g1[2] - $g0[2]*$g1[1],
        $g0[2]*$g1[0] - $g0[0]*$g1[2],
        $g0[0]*$g1[1] - $g0[1]*$g1[0],
    ];
    $dis = euclidean_distance($axis);
    $unit_axis = [ $axis[0]/$dis, $axis[1]/$dis, $axis[2]/$dis ];

    $a1 = rodrigues($a0, $unit_axis, $theta);
    return $a1;
}

?>