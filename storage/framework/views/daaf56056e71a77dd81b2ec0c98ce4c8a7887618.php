<?php $__env->startPush('head'); ?>
<style type="text/css">

</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('contents'); ?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
<h3>
    <i class="fa fa-dashboard"></i>
    總覽
</h3>
<hr />
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3><?php echo e($services->count()); ?></h3>
                <p>成交交易</p>
            </div>
            <div class="icon">
                <i class="fa fa-star"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3>$<?php echo e(number_format($services->chargeAmount())); ?></h3>
                <p>成交總金額</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3>$<?php echo e(number_format($sum_points)); ?></h3>
                <p>總發放點數</p>
            </div>
            <div class="icon">
                <i class="fas fa-hospital-symbol"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo e($service_plans->count()); ?></h3>
                <p>已上傳課程</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-aqua-active">
            <div class="inner">
                <h3><?php echo e($service_plans->videosCount()); ?></h3>
                <p>已上傳影片</p>
            </div>
            <div class="icon">
                <i class="fa fa-video-camera"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3><?php echo e($member_requests->where('treatment_type', 1)->count()); ?></h3>
                <p>諮詢神經方面</p>
            </div>
            <div class="icon">
                <i class="fa fa-heart"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-orange-active">
            <div class="inner">
                <h3><?php echo e($member_requests->where('treatment_type', 2)->count()); ?></h3>
                <p>諮詢骨科方面</p>
            </div>
            <div class="icon">
                <i class="fa fa-child"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo e($members->where('online', 1)->count()); ?></h3>
                <p>線上會員</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-green-active">
            <div class="inner">
                <h3><?php echo e($members->count()); ?></h3>
                <p>會員人數</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-gray">
            <div class="inner">
                <h3><?php echo e($doctors->where('online', 1)->count()); ?></h3>
                <p>線上員工</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-gray-active">
            <div class="inner">
                <h3><?php echo e($doctors->count()); ?></h3>
                <p>員工人數</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>交易序號</th>
                    <th>服務對象</th>
                    <th>服務人員</th>
                    <th>服務類型</th>
                    <th>服務費用</th>
                    <th>付款方式</th>
                    <th>付款狀態</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                    <th>功能</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $services->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($service->order_number); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin-services-list-by-member', ['member_id' =>  $service->member ? $service->member->id : ''])); ?>"><?php echo e($service->member ? $service->member->name : ''); ?></a>
                        </td>
                        <td>
                            <a href="<?php echo e(route('admin-services-list-by-doctor', ['doctor_id' => $service->doctor ? $service->doctor->id : ''])); ?>"><?php echo e($service->doctor ? $service->doctor->name :''); ?></a>
                        </td>
                        <td><?php echo e($service->treatment_type_text); ?></td>
                        <td>$<?php echo e(number_format($service->charge_amount, 0)); ?></td>
                        <td><?php echo e($service->payment_method_text); ?></td>
                        <td><?php echo e($service->payment_status_text); ?></td>
                        <td><?php echo e($service->created_at); ?></td>
                        <td><?php echo e($service->updated_at); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin-services-detail', ['service_id' => $service->id])); ?>">明細</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td class="text-center" colspan="10">目前沒有資料</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>