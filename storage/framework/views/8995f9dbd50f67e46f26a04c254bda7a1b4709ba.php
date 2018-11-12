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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
                font-size: 13px;
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
                font-size: 13px;
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
                font-size: 13px;
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
                font-weight: 300;
                font-size: 13px; }

            .rwd-table th {
                color: #333333; }

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

            <?php $__currentLoopData = $service->plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <h2>課表時間：<?php echo e($plan->started_at); ?> ~ <?php echo e($plan->stopped_at); ?></h2> 

                <?php $__currentLoopData = $plan->videos()->withTrashed()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <h3 style="text-align:left">影片名稱：<?php echo e($video->description); ?></h3>
                    <img src="<?php echo e(asset($video->thumbnail)); ?>" width="100"><br><br>
                    <table class="table table-sm" style="width:400px">
                        <thead bgcolor="#84c1ff">
                            <tr>
                                <th scope="col">執行日期</th>
                                <th scope="col">平均分數</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                                $dailys = $service->daily()->where('service_plan_videos_id', $video->id)->orderBy('scored_at', 'ASC')->get()
                            ?>

                            <?php $__currentLoopData = $dailys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $daily): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($daily->created_at->format('Y-m-d')); ?></td>
                                    <td>
                                        <?php
                                            $score = json_decode($daily->score);
                                            $average_score = array_sum($score) / $video->session;
                                            echo $average_score;
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </tbody>
                    </table>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>  -->
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <!-- <script src="js/bootstrap.min.js"></script>  -->
    </body>
</html>
