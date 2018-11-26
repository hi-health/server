<!DOCTYPE html>
<html>
	<head>
		<title>HI-Health</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="../JS/jquery-3.2.0.js" type="text/javascript" ></script>
		<script src="../JS/bootstrap.min.js"></script>
		<link type="text/css" rel="stylesheet" href="../css/bootstrap.min.css" />
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
		<style>
			.w3-row-padding img {margin-bottom: 12px}
			.point-tab {
				background-color: #eceff4;
			}
			@media only screen and (max-width:620px) {
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
    	@if (count($errors) > 0)
        	<div class="alert alert-danger">
            	<ul style="color:red;">
            	@foreach ($errors->all() as $error)
                	<li>{{ $error }}</li>
            	@endforeach
            	</ul>
        	</div>
   		@endif
	</div>

	<form action="{{ url('password/reset_password/'.$user_id) }}" method="post" class="w3-container w3-card-4 w3-light-grey w3-margin w3-center">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="w3-row w3-section">
			<b style="font-size: 25px">密碼重設</b>
		</div>
		<div class="w3-row w3-section">
			<div class="w3-quarter" style="width: 60px">
				<i class="fa fa-lock" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="password" name="new_password" id="pwd" class="w3-input w3-border" placeholder="請設定登入密碼">
			</div><br/>
			<div class="w3-quarter" style="width: 60px">
				<i class="fa fa-lock" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="password" name="check_new_password" id="pwd1" class="w3-input w3-border" placeholder="請再次填寫密碼" onkeyup="validate()"><span id="tishi"></span>
			</div>
		</div>
		<div class="w3-row w3-section w3-center">
			<input type="submit" id="button" value="送出" class="w3-button w3-block w3-section w3-ripple w3-padding" style="background-color: #628194; color: white;" onclick="return validPwd()">
			<input type="reset" name="ret" value="重置" class="w3-button w3-block w3-section w3-ripple w3-padding" style="background-color: #628194; color: white;">
		</div>
	</form>
</html>

<script>

function validate() {
 var pwd1 = document.getElementById("pwd").value;
 var pwd2 = document.getElementById("pwd1").value;

<!-- 對比兩次輸入的密碼 -->
 if(pwd1 == pwd2) {
  
  document.getElementById("tishi").innerHTML="<font color='green'>兩次密碼相同</font>";
  document.getElementById("button").disabled = false;
    }
else {
  document.getElementById("tishi").innerHTML="<font color='red'>兩次密碼不相同</font>";
  document.getElementById("button").disabled = true;
   }
}

function validPwd()
{
var pwd = document.getElementById('pwd').value;
if (pwd.length < 6)
{
alert("密碼長度至少6位");
return false;
}
return true;
}

</script>