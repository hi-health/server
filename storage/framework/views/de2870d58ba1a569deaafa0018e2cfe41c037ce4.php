<?php $__env->startPush('head'); ?>
<style type="text/css">
    .score .fa {
        font-size: 32px;
    }
    .score .fa.active {
        color: #3c8dbc;
    }
    .score .date {
        line-height: 32px;
    }
    .score hr {
        margin: 5px 0;
    }
    .col-md-6 {
        min-height: 240px;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('contents'); ?>
<h3>
    <i class="fa fa-list-alt"></i>
    影片管理 - 影片明細 - 交易序號 <?php echo e($service->order_number); ?>

</h3>
<hr />
<div id="services-videos-detail">
    <?php $__currentLoopData = $service_plans->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="row">
            <?php $__currentLoopData = $chunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-12">
                    <div class="box box-solid box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">課程時間 <?php echo e($service_plan->started_at); ?> ~ <?php echo e($service_plan->stopped_at); ?></h3>
                            <div class="box-tools">建立於<?php echo e($service_plan->created_at); ?></div>
                        </div>
                        <div class="box-body">
                            <?php $__currentLoopData = $service_plan->videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_plan_video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="<?php echo e(url($service_plan_video->video)); ?>" target="_blank">
                                            <img class="img-responsive" src="<?php echo e(url($service_plan_video->thumbnail)); ?>" alt="點此開啟播放" title="點此開啟播放" />
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <?php
                                            $days = $service_plan_video->score->count();
                                            $average = $service_plan_video->score->average();
                                        ?>
                                        <div class="score">
                                            <br />
                                            <div>
                                                <i class="fa fa-calendar"></i>
                                                <b>復健了<?php echo e($days); ?> 天</b>
                                            </div>
                                            <br />
                                            <div>平均分數
                                                <?php for($i = 1; $i <= 3; $i++): ?>
                                                    <?php if($i <= $average): ?>
                                                        <i class="fa fa-smile-o active"></i>
                                                    <?php else: ?>
                                                        <i class="fa fa-smile-o"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 score">
                                        <?php $__currentLoopData = $service_plan_video->score; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $daily): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="row">
                                                <div class="col-md-5 date"><?php echo e($daily->created_at->format('Y-m-d h:i A')); ?></div>
                                                <div class="col-md-5">
                                                    <?php for($i = 1; $i <= 3; $i++): ?>
                                                        <?php if($i <= $daily->score): ?>
                                                            <i class="fa fa-smile-o active"></i>
                                                        <?php else: ?>
                                                            <i class="fa fa-smile-o"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <hr />
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <br />
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>