<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>評分紀錄</title>
        <!-- Bootstrap -->
        <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- <link href="table.css" rel="stylesheet"> -->
        <style type="text/css">
            @import  url(http://fonts.googleapis.com/earlyaccess/notosanstc.css);
            body {
                background: #E8F1F2;
                font-family: "Noto Sans TC", sans-serif;
                font-weight: 300; }

            h1, h2, h3 {
                font-weight: 300;
                color: #132A3C;
                font-size: 27px;
                line-height: 27px;
                margin-top: 35px;
                margin-bottom: 25px;
                text-align: center; }

            .aspect-fill {
                background-size: cover !important;
                background-position: center center !important; }

            .video_bmp {
                width: 100%;
                display: block;
                padding-bottom: 66.66%;
                margin-bottom: 10px;
                margin-top: 20px; }

            .container {
                padding: 0px 0px 20px 0px; }
            @media  only screen and (min-width: 768px) {
                .container {
                    padding-left: 25px;
                    padding-right: 25px; } }

            .rwd-table tr {
                background: #FFF;
                border-top: 1px solid #CFCFCF; }
            @media  only screen and (min-width: 768px) {
                .rwd-table tr {
                    background: #F9F9F9; } }

            .rwd-table tr:nth-of-type(2n) {
                background: #FFF; }

            .rwd-table tr:first-child {
                background: #4D95FF;
                border: none; }

            .rwd-table th,
            .rwd-table td {
                margin: 0.5em 1em; }

            .rwd-table {
                width: 100%; }

            .rwd-table th {
                display: none; }

            .rwd-table td {
                display: block; }
            .rwd-table td[rowspan="3"] {
                font-size: 18px;
                font-weight: 400;
                background: #D3D3D3;
                margin: 0;
                padding: 5px; }
            @media  only screen and (min-width: 768px) {
                .rwd-table td[rowspan="3"] {
                    background: transparent; } }
            .rwd-table td[data-th="time"] {
                margin: 0;
                padding: 5px;
                font-size: 18px;
                font-weight: 400;
                background: #E3E3E3; }
            @media  only screen and (min-width: 768px) {
                .rwd-table td[data-th="time"] {
                    background: transparent; } }

            .hidden {
                font-size: 19px;
                text-align: center; }
            @media  only screen and (min-width: 768px) {
                .hidden {
                    display: none; } }

            .rwd-table td:before {
                content: attr(data-th);
                font-weight: bold;
                font-size: 19px;
                border-bottom: 1px solid #CCC;
                display: block;
                width: 100%;
                padding-bottom: 6px;
                padding-top: 10px;
                margin: 0 auto 10px auto; }
            @media  only screen and (min-width: 768px) {
                .rwd-table td:before {
                    display: none; } }

            .rwd-table tr td:nth-child(1):before {
                display: none; }

            .rwd-table tr:nth-child(3n-1) td:nth-child(2):before {
                display: none; }

            .rwd-table th, .rwd-table td {
                text-align: center; }
            @media  only screen and (min-width: 768px) {
                .rwd-table th, .rwd-table td {
                    display: table-cell;
                    padding: 0.25em 0.5em;
                    padding: 1em !important; } }

            .rwd-table th, .rwd-table td:before {
                color: #333333;
                font-weight: 400; }

            .rwd-table th {
                color: #FFF; }

            @media  only screen and (min-width: 768px) {
                .rwd-table th:first-child,
                .rwd-table td:first-child {
                    padding-left: 0; } }

            @media  only screen and (min-width: 768px) {
                .rwd-table th:last-child,
                .rwd-table td:last-child {
                    padding-right: 0; } }

            .rwd-table tfoot tr:first-child {
                background-color: #C96013;
                color: #FFF;
                font-size: 20px; }
            .rwd-table tfoot tr:first-child td {
                text-align: center; }
            @media  only screen and (min-width: 768px) {
                .rwd-table tfoot tr:first-child td {
                    padding: 15px !important;
                    text-align: left; } }
            @media  only screen and (min-width: 768px) {
                .rwd-table tfoot tr:first-child td:first-child {
                    text-align: right; } }
            .rwd-table tfoot tr:first-child td:last-child {
                font-size: 26px; }

            /*# sourceMappingURL=table.css.map */


        </style>
    </head>
    <body>
        <?php
            $plans = $service->plans;
            $max_days = $plans->max(function($plan) {
                return $plan->daily->count();
            });
            $daily = $service->daily()->orderBy('scored_at', 'ASC')->get();
            $first_day = $daily->first();
            $last_day = $daily->last();
            if (!function_exists('toChinessNumber')) {
                function toChinessNumber($number) {
                    $maps = ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二', '十三', '十四', '十五'];
                    return isset($maps[$number - 1]) ? $maps[$number - 1] : '';
                }
            }
        ?>
        <div class="container">
            <h1>評分紀錄</h1>
            <p class="hidden">開始日期：<?php echo e($first_day->scored_at); ?></p>
            <table class="rwd-table">
                <tr>
                    <th colspan="2">開始日期：<?php echo e($first_day->scored_at); ?></th>
                    <th>第一日</th>
                    <th>第二日</th>
                    <th>第三日</th>
                    <th>第四日</th>
                    <th>第五日</th>
                    <th>第六日</th>
                    <th>第七日</th>
                </tr>
                <?php for($i = 1; $i <= ceil($max_days / 7); $i++): ?>
                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php if($row === 0): ?>
                                <td rowspan="<?php echo e($plans->count()); ?>" data-th="">
                                    第<?php echo e(toChinessNumber($i)); ?>週
                                </td>
                            <?php endif; ?>
                            <td data-th="time">
                                <?php echo e($plan->started_at); ?> ~ <?php echo e($plan->stopped_at); ?>

                            </td>
                            <?php $__currentLoopData = $plan->daily->forPage($i, 7); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td data-th="第一日">
                                    <div class="aspect-fill video_bmp" style="background: url('<?php echo e($day->video->thumbnail_url); ?>');"></div>
                                    <?php if($day->score === 3): ?>
                                        <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <!-- Generator: Sketch 46.1 (44463) - http://www.bohemiancoding.com/sketch -->
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g transform="translate(-111.000000, -247.000000)" fill="#1CAB58">
                                            <g transform="translate(111.000000, 246.000000)">
                                            <path d="M11.9931429,20.7142857 C8.538,20.7142857 5.49085714,18.1428571 4.26171429,15.5714286 L19.7091429,15.5714286 C18.5091429,18.1428571 15.492,20.7142857 11.9931429,20.7142857 M8.98114286,7.42857143 C9.93085714,7.42857143 10.6971429,8.19657143 10.6971429,9.14285714 C10.6971429,10.09 9.93085714,10.8571429 8.98114286,10.8571429 C8.03228571,10.8571429 7.26514286,10.09 7.26514286,9.14285714 C7.26514286,8.19657143 8.03228571,7.42857143 8.98114286,7.42857143 M14.9897143,7.42857143 C15.9377143,7.42857143 16.7057143,8.19657143 16.7057143,9.14285714 C16.7057143,10.09 15.9377143,10.8571429 14.9897143,10.8571429 C14.0425714,10.8571429 13.272,10.09 13.272,9.14285714 C13.272,8.19657143 14.0425714,7.42857143 14.9897143,7.42857143 M12.0145714,1 C5.38028571,1 0,6.37257143 0,13 C0,19.6265714 5.38028571,25 12.0145714,25 C18.6514286,25 24,19.6265714 24,13 C24,6.37257143 18.6514286,1 12.0145714,1" id="Fill-1-Copy"></path>
                                            </g>
                                            </g>
                                            </g>
                                        </svg>
                                    <?php elseif($day->score === 2): ?>
                                        <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <!-- Generator: Sketch 46.1 (44463) - http://www.bohemiancoding.com/sketch -->
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g transform="translate(-193.000000, -247.000000)" fill="#FF8C00">
                                            <g transform="translate(193.000000, 246.000000)">
                                            <path d="M6,15.5714286 L18,15.5714286 L18,17.2857143 L6,17.2857143 L6,15.5714286 Z M8.98114286,7.42857143 C9.93085714,7.42857143 10.6971429,8.19657143 10.6971429,9.14285714 C10.6971429,10.09 9.93085714,10.8571429 8.98114286,10.8571429 C8.03228571,10.8571429 7.26514286,10.09 7.26514286,9.14285714 C7.26514286,8.19657143 8.03228571,7.42857143 8.98114286,7.42857143 M14.9897143,7.42857143 C15.9377143,7.42857143 16.7057143,8.19657143 16.7057143,9.14285714 C16.7057143,10.09 15.9377143,10.8571429 14.9897143,10.8571429 C14.0425714,10.8571429 13.272,10.09 13.272,9.14285714 C13.272,8.19657143 14.0425714,7.42857143 14.9897143,7.42857143 M12.0145714,1 C5.38028571,1 0,6.37257143 0,13 C0,19.6265714 5.38028571,25 12.0145714,25 C18.6514286,25 24,19.6265714 24,13 C24,6.37257143 18.6514286,1 12.0145714,1" id="Fill-1"></path>
                                            </g>
                                            </g>
                                            </g>
                                        </svg>
                                    <?php else: ?>
                                        <svg width="30px" height="30px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g transform="translate(-275.000000, -247.000000)" fill="#FF0000">
                                            <g transform="translate(275.000000, 246.000000)">
                                            <g>
                                            <path d="M17.4325714,19.7234286 C16.5385714,17.6542857 14.4128571,16.3162857 12.0188571,16.3162857 C9.56571429,16.3162857 7.428,17.6491429 6.57085714,19.7122857 L4.92857143,19.0291429 C6.06171429,16.3017143 8.844,14.5394286 12.0188571,14.5394286 C15.1225714,14.5394286 17.8877143,16.2965714 19.0645714,19.018 L17.4325714,19.7234286 Z M8.88771429,7.54857143 C9.77142857,7.54857143 10.488,8.26514286 10.488,9.148 C10.488,10.0317143 9.77142857,10.7482857 8.88771429,10.7482857 C8.00571429,10.7482857 7.28914286,10.0317143 7.28914286,9.148 C7.28914286,8.26514286 8.00571429,7.54857143 8.88771429,7.54857143 L8.88771429,7.54857143 Z M15.1105714,7.54857143 C15.9942857,7.54857143 16.7108571,8.26514286 16.7108571,9.148 C16.7108571,10.0317143 15.9942857,10.7482857 15.1105714,10.7482857 C14.2285714,10.7482857 13.5102857,10.0317143 13.5102857,9.148 C13.5102857,8.26514286 14.2285714,7.54857143 15.1105714,7.54857143 L15.1105714,7.54857143 Z M12,1 C5.37257143,1 0,6.37257143 0,13 C0,19.6274286 5.37257143,25 12,25 C18.6282857,25 24,19.6274286 24,13 C24,6.37257143 18.6282857,1 12,1 L12,1 Z" id="Fill-1"></path>
                                            </g>
                                            </g>
                                            </g>
                                            </g>
                                        </svg>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php for($j = 0; $j < (7 - $plan->daily->forPage($i, 7)->count()); $j++): ?>
                                <td></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endfor; ?>
            </table>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <!--<script src="js/bootstrap.min.js"></script> -->
    </body>
</html>
