<!DOCTYPE html>
<html>
	<head>
		<title>HI-Health</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
		<style>
			.w3-row-padding img {margin-bottom: 12px}
			.point-tab {
				background-color: #eceff4;
			}
			@media  only screen and (max-width:620px) {
  				.w3-third {
  					width:calc(100%  /3);;
  				}
			}
			img{
				width: 60%;

			}
		</style>
	</head>
	<body>
		<div class="w3-row point-tab" style="padding-top: 15px">
			<div class="w3-third w3-center">
				<a href="<?php echo e(route('point-transfer', ['users_id' => $users_id])); ?>">
					<img src="<?php echo e(asset('ico_pay.png')); ?>" >
				</a>
			</div>
			<div class="w3-third w3-center">
				<a href="<?php echo e(route('point-index', ['users_id' => $users_id])); ?>">
					<img src="<?php echo e(asset('ico_HiPoint.png')); ?>" >
				</a>
			</div>
			<div class="w3-third w3-center">
				<a href="<?php echo e(route('point-list-all-transaction', ['users_id' => $users_id])); ?>">
					<img src="<?php echo e(asset('ico_record.png')); ?>" >
				</a>
			</div>
		</div>
		<div class="w3-row point-tab w3-center">
			<div class="w3-third w3-center" style="margin-top: 10px">
				<a href="<?php echo e(route('point-transfer', ['users_id' => $users_id])); ?>">
					<img src="<?php echo e(asset('ico_pay_word.png')); ?>" >
				</a>
			</div>
			<div class="w3-third w3-center" style="margin-top: 10px">
				<a href="<?php echo e(route('point-index', ['users_id' => $users_id])); ?>">
					<img src="<?php echo e(asset('ico_HiPoint_word.png')); ?>" style="width: 65%">
				</a>
			</div>
			<div class="w3-third w3-center" style="margin-top: 10px">
				<a href="<?php echo e(route('point-list-all-transaction', ['users_id' => $users_id])); ?>">
					<img src="<?php echo e(asset('ico_record_word.png')); ?>" >
				</a>
			</div>
		</div>
		<div class="w3-row point-tab" style="padding-bottom: 10px">
			<div class="w3-third w3-center">
				&nbsp;
			</div>
			<div class="w3-third w3-center">
				<?php echo e($RemainedPoint); ?>

			</div>
			<div class="w3-third w3-center">
				&nbsp;
			</div>
		</div>
		<?php echo $__env->yieldContent('content'); ?>
	</body>
</html>
