<?php
namespace App\AI;

use Exception;
use Log;

function dot_product($a, $b){
    $c = 0;
    foreach ($b as $key => $value) {
        $c += $a[$key]*$value;
    }
    return c;
}
function cross_product3D($a, $b){
    $c = [
        $a[1]*$b[2] - $a[2]*$b[1],
        -$a[0]*$b[2] + $a[2]*$b[0],
        $a[0]*$b[1] - $a[1]*$b[0],
    ];
    return c;
}
function arr_multiply($a,$b){
    $c = [];
    foreach ($b as $key => $value) {
        $c[] = $a*$value;
    }
    return c;
}
function arr_elm_sum($a, $b){
    $c = [];
    foreach ($b as $key => $value) {
        $c[] = $a[$key]+$value;
    }
    return c;
}

function euclidean_distance($a){
    $b = 0;
    foreach ($a as $key => $value) {
        $b += pow($value);
    }
    return sqrt($b);
}
?>