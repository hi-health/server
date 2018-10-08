<?php $__env->startSection('contents'); ?>
<h3>
    <i class="fa fa-list-alt"></i>
    影片管理 - 課程列表
</h3>
<hr />
<div id="plans-table" class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>交易序號</th>
                <th>服務類型</th>
                <th>服務人員</th>
                <th>服務對象</th>
                <th>服務費用</th>
                <th>課程數量</th>
                <th>影片數量</th>
                <th>建立時間</th>
                <th>更新時間</th>
                <th>功能</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td class="text-right" colspan="7"><?php echo e($service_plans_group); ?></td>
            </tr>
        </tfoot>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $service_plans_group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $service = $service_plan->service;
		    if(!$service->members_id) continue;
                    $plans = $service_plans->where('services_id', $service->id);
                    $video_count = $plans->sum(function($plan) {
                        return $plan->videos->count();
                    });
                ?>
                <tr>
                    <td><?php echo e($service->order_number); ?></td>
                    <td><?php echo e($service->treatment_type_text); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin-services-list-by-doctor', ['doctor_id' => $service->doctor->id])); ?>"><?php echo e($service->doctor->name); ?></a>
                    </td>
                    <td>
                        <a href="<?php echo e(route('admin-services-list-by-member', ['member_id' => $service->member->id])); ?>"><?php echo e($service->member->name); ?></a>
                    </td>
                    <td>$<?php echo e(number_format($service->charge_amount, 0)); ?></td>
                    <td><?php echo e($plans->count()); ?></td>
                    <td><?php echo e($video_count); ?></td>
                    <td><?php echo e($service->created_at); ?></td>
                    <td><?php echo e($service->updated_at); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin-videos-detail', ['service_id' => $service->id])); ?>">明細</a>
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
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>