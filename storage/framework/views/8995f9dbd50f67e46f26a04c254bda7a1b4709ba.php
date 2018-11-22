<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <title>評分紀錄</title>     
    </head>
    <body>
        <div class="container">
            <h1>評分紀錄</h1>

            <?php $__currentLoopData = $service->plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <h2>課表時間：<?php echo e($plan->started_at); ?> ~ <?php echo e($plan->stopped_at); ?></h2> 

                <?php $__currentLoopData = $plan->videos()->withTrashed()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <h3 style="text-align:left">影片名稱：<?php echo e($video->description); ?></h3>
                    <img src="<?php echo e($message->embed(asset($video->thumbnail))); ?>" width="100"><br><br>
                    <!-- <img src="<?php echo e(asset($video->thumbnail)); ?>" width="100"><br><br> -->
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
