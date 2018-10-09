<?php $__env->startSection('contents'); ?>
<h3>
    <i class="fa fa-list-alt"></i>
    服務管理 - <?php echo e($doctor->name); ?>的服務列表
</h3>
<hr />
<div id="services-table" class="table-responsive">
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
                <th>狀態</th>
                <th>功能</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td class="text-right" colspan="7"><?php echo e($services); ?></td>
            </tr>
        </tfoot>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr <?php if( $service->payment_status==1 && strlen($service->invoice)==0): ?>style="color:#F00"<?php endif; ?>>
                    <td><?php echo e($service->order_number); ?></td>
                    <td><?php echo e(isset($service->member->name) ? $service->member->name : '---'); ?></td>
                    <td><?php echo e(isset($service->doctor->name) ? $service->doctor->name : '---'); ?></td>
                    <td><?php echo e($service->treatment_type_text); ?></td>
                    <td>$<?php echo e(number_format($service->charge_amount, 0)); ?></td>
                    <td><?php echo e($service->payment_method_text); ?></td>
                    <td><?php echo e($service->payment_status_text); ?></td>
                    <td><?php echo e($service->created_at); ?></td>
                    <td><?php echo e($service->updated_at); ?></td>
                    <td><?php echo e($service->invoice_status_text); ?></td>
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
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>