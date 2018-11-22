<?php $__env->startSection('content'); ?>
	<form action="<?php echo e(url('api/point/'.$users_id.'/transfer')); ?>" method="post" class="w3-container w3-card-4 w3-light-grey w3-margin w3-center">
		<div class="w3-row w3-section">
			<b style="font-size: 25px">交易點數</b>
		</div>

		<div class="w3-row w3-section">
			
			<div class="w3-quarter" style="width: 60px">
				<i class="fa fa-lock" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
				<input type="password" name="password" class="w3-input w3-border" placeholder="請輸入您的密碼">
			</div>
			
		</div>
		
		<div class="w3-row w3-section">
			<div class="w3-quarter" style="width: 60px">
  				<i class="fas fa-phone fa-flip-horizontal" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="text" name="receiver_account" class="w3-input w3-border" placeholder="請輸入轉入者之手機號碼">
			</div>
		</div>

		<div class="w3-row w3-section">
			<div class="w3-quarter" style="width: 60px">
  				<i class="fas fa-coins" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="text" name="transferred_point" class="w3-input w3-border" placeholder="請輸入欲轉出點數" style="ime-mode:disabled" onkeyup="return ValidateNumber(this,value)">
			</div>
		</div>

		<div class="w3-row w3-section w3-center">
			<input type="submit" value="送出" class="w3-button w3-block w3-section w3-ripple w3-padding" style="background-color: #628194; color: white;">
		</div>

	</form>

<?php $__env->stopSection(); ?>




<script>
	function ValidateNumber(e, pnumber)
	{
	  if (!/^\d+$/.test(pnumber))
	  {
		e.value = /^\d+/.exec(e.value);
	  }
	  return false;
	}
</script>

<?php echo $__env->make('points.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>