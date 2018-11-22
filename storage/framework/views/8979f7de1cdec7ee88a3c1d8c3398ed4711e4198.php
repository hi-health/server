<style type="text/css">
	em{
		padding: 0px;
		margin: 0px;
	}
</style>

<?php $__env->startSection('content'); ?>
	<ul class="w3-ul w3-card-4">
		<?php $__currentLoopData = $Transaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<li class="w3-bar" style="padding-top: 0px; padding-bottom: 0px">
			<div class="w3-bar-block w3-bar-item" style="padding-top: 15px; padding-bottom: 0px">
				<span  class="w3-bar-item" style="padding-bottom: 0px; color: #3b6279">
					<?php echo e($Transactions->created_at->format('Y-m-d')); ?>

				</span>
				<span  class="w3-bar-item" style="padding-top: 0px; color: #3b6279">
					<?php echo e($Transactions->created_at->format('H:i')); ?>

				</span>
			</div>
			<div class="w3-bar-block w3-bar-item w3-right w3-center" style="padding-top: 0px; padding-bottom: 0px">
				<?php if($Transactions->point>=0): ?>
					<span class="w3-bar-item" style="padding-bottom: 0px;">
						<b style="font-size: 22px; color: #fe7168">+<?php echo e($Transactions->point); ?></b>
					</span>
					<span class="w3-bar-item" style="padding-bottom: 0px; padding-top: 0px; color: #3b6279">
						<em style="font-size: 11px;">來自</em>
					</span>
					<span  class="w3-bar-item" style="padding-top: 0px; font-size: 13px; color: #3b6279">
						<?php if(isset($Transactions->pointconsume_id)): ?>
							<?php echo e($Transactions->transaction->user->account); ?>

						<?php else: ?>
							您好健康APP
						<?php endif; ?>
					</span>
				<?php elseif($Transactions->point<0): ?>
					<span class="w3-bar-item" style="padding-bottom: 0px;">
						<b style="font-size: 22px; color: #628194"><?php echo e($Transactions->point); ?></b>
					</span>
					<span class="w3-bar-item" style="padding-bottom: 0px; padding-top: 0px; color: #3b6279">
						<em style="font-size: 11px;">給予</em>
					</span>
					<span  class="w3-bar-item" style="padding-top: 0px; font-size: 13px; color: #3b6279">
						<?php if(isset($Transactions->transaction->user->account)): ?>
							<?php echo e($Transactions->transaction->user->account); ?>

						<?php else: ?>
							未知
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</div>
		</li>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	</ul>
<?php $__env->stopSection(); ?>
  
<?php echo $__env->make('points.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>