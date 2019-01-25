<?php
namespace App\AI;

use Exception;
use Log;
include 'math.php';

function rodrigues($v,$axis,$theta){
    $V = [0, 0, 0];
    $V = arr_elm_sum( arr_multiply(cos($theta), $v), $V );
    $V = arr_elm_sum( arr_multiply((1-cos($theta))*dot_product($v,$axis), $axis), $V );
    $V = arr_elm_sum( arr_multiply(sin($theta), cross_product3D($axis,$v)), $V );

    return $V;
}
?>