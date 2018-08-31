<?php $__env->startSection('contents'); ?>
<h3>
    <i class="fa fa-list-alt"></i>
    會員管理 - 會員明細
</h3>
<hr />
<div id="member-detail">
    <dl class="dl-horizontal">
        <dt>姓名</dt>
        <dd><?php echo e($member->name); ?></dd>
        <?php if(!empty($member->avatar)): ?>
            <dt></dt>
            <dd>
                <img src="<?php echo e($member->avatar); ?>" class="img-responsive" />
            </dd>
        <?php endif; ?>
        <dt>帳號</dt>
        <dd><?php echo e($member->account); ?></dd>
        <dt>生日 </dt>
        <dd><?php echo e($member->birthday); ?></dd>
        <dt>性別</dt>
        <dd><?php echo e($member->gender); ?></dd>
        <dt>Email</dt>
        <dd><?php echo e($member->email); ?></dd>
        <dt>縣市</dt>
        <dd><?php echo e($member->city); ?></dd>
        <dt>鄉鎮</dt>
        <dd><?php echo e($member->district); ?></dd>
        <?php if(!empty($member->facebook_id)): ?>
            <dt>Facebook編號</dt>
            <dd><?php echo e($member->facebook_id); ?></dd>
            <dt>Facebook Token</dt>
            <dd><?php echo e($member->facebook_token); ?></dd>
        <?php endif; ?>
        <dt>狀態</dt>
        <dd><?php echo e($member->status_text); ?></dd>
        <dt>註冊日期</dt>
        <dd><?php echo e($member->created_at); ?></dd>
        <dt>更新日期</dt>
        <dd><?php echo e($member->updated_at); ?></dd>
    </dl>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>