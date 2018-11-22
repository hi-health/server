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
	<div class="form-group">
    	<?php if(count($errors) > 0): ?>
        	<div class="alert alert-danger">
            	<ul style="color:red;">
            	<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                	<li><?php echo e($error); ?></li>
            	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            	</ul>
        	</div>
   		<?php endif; ?>
	</div>

	<form action="<?php echo e(url('password/sms_input')); ?>" method="post" class="w3-container w3-card-4 w3-light-grey w3-margin w3-center">
		<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
		<div class="w3-row w3-section">
			<b style="font-size: 25px">密碼重設</b>
		</div>
		<div class="w3-row w3-section">
			<div class="w3-quarter" style="width: 60px">
  				<i class="fas fa-phone fa-flip-horizontal" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="text" name="phone" class="w3-input w3-border" placeholder="請輸入手機號碼" onblur="checkPhone(this.value);">
			</div>
		</div>
		<div class="w3-row w3-section w3-center">
			<input type="submit" value="送出" class="w3-button w3-block w3-section w3-ripple w3-padding" style="background-color: #628194; color: white;">
		</div>
	</form>
</html>

<script>
function checkPhone( strPhone )
{
    var cellphone = /^09[0-9]{8}$/;

    if ( !cellphone.test( strPhone ) ) {
        alert( "手機格式錯誤" );
    }
}; 
</script>
