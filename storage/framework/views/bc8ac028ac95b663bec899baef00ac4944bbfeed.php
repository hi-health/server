<?php $__env->startSection('content'); ?>
	<div class="w3-center" style="padding-top: 100px">
		<em style="font-size: 16px;">HiPoint當前兌台幣匯率為 1:<?php echo e($HiPointRate); ?></em>
	</div>
	<div class="w3-center" style="padding-top: 30px">
		<em style="font-size: 18px;">您當前有 <?php echo e($RemainedPoint); ?> HiPoint</em>

	</div>
	<div class="w3-center">
		<em style="font-size: 18px;">共值 <?php echo e($RemainedPoint*$HiPointRate); ?> 台幣</em>
	</div>
	
<?php $__env->stopSection(); ?>

<?php echo $__env->make('points.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>