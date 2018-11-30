@extends('points.master')
@section('content')

<style type="text/css">
	#transfer{
		background: #ffffff url('{{asset('HiPoint/bg_pay.png')}}') no-repeat center;
		background-size: cover;
		width: 100%;
		height: 100%;
	}
	#title{
		padding-top: 20%;
		color: white;
		text-align: center;
	}
	input{
		border-radius: 10px
	}
	form{
		margin: 35px auto;
	}
	pre{
		margin: 0px
	}

</style>

<div id="transfer">
	<div id="title">
		<em style="font-size: 25px;">轉讓點數</em>
	</div>

	<form id="myForm" action="{{ url('api/point/'.$users_id.'/transfer') }}" method="post" class=" w3-margin w3-center">

		<div class="w3-row" style="width: 85%; margin:20px auto;">
			<div style="margin-left: 15px">
				<pre style="color: white;text-align:left;">密碼</pre>
			</div>
			<div class="">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="password" id="password" class="w3-input w3-border" placeholder="請輸入您的密碼">
			</div>
			
		</div>
		
		<div class="w3-row" style="width: 85%; margin:20px auto;">
			<div style="margin-left: 15px">
				<pre style="color: white;text-align:left;">手機號碼</pre>
			</div>
			<div class="">
				<input type="text" id="receiver_account" class="w3-input w3-border" placeholder="請輸入轉入者之手機號碼">
			</div>
		</div>

		<div class="w3-row" style="width: 85%; margin:20px auto;">
			<div style="margin-left: 15px">
				<pre style="color: white;text-align:left;">新台幣</pre>
			</div>
			<div class="">
				<input type="text" id="transferred_point" class="w3-input w3-border" placeholder="請輸入欲轉出的回購金額" style="ime-mode:disabled" onkeyup="return ValidateNumber(this,value)">
			</div>
		</div>

		<div class="w3-row w3-center" style="width: 85%; margin:60px auto 0px;">
			<button id="confirm" type="button" class="w3-button w3-block w3-padding" style="background-color: #e2e6eb; color: black;">轉讓</button>
		</div>

	</form>
</div>

<script>
	window.addEventListener('load', function(){
		document.getElementById('confirm').addEventListener("click", myAlert, false);
		
		function myAlert(){
			swal(	{	title: "即將把Hi Point從您的帳戶中轉出",
						text: "確定要繼續嗎?",
						type: "warning",   
						showCancelButton: true,   
						confirmButtonColor: "#DD6B55",   
						confirmButtonText: "確認",   
						cancelButtonText: "取消",   
					}
				).then((result) => {
					if (result.value) {					
						myPost();
					}
					else{
						document.getElementById('myForm').reset();
					}
				});		
		}

		function myPost(){
			var xhr = new XMLHttpRequest();
			var url = "{{ url('api/point/'.$users_id.'/transfer') }}";
			xhr.open("POST", url, true);
			xhr.setRequestHeader("Content-Type", "application/json");
			xhr.onreadystatechange = function () {
			    if (xhr.readyState === 4 && xhr.status === 200) {
			    	swal(
							'Success!',
							xhr.responseText,
							'success'
						)
			    	document.getElementById('myForm').reset();
			    }
			    else if(xhr.status != 200){
			    	swal(
							'Error!',
							xhr.responseText,
							'error'
						)
			    }
			};
			var data = JSON.stringify(	{
											"receiver_account": document.getElementById('receiver_account').value, 
											"transferred_point": document.getElementById('transferred_point').value,
											'password': document.getElementById('password').value
										}
									);
			xhr.send(data);
		}
	});
	function ValidateNumber(e, pnumber)
		{
			if (!/^\d+$/.test(pnumber))
			{
				e.value = /^\d+/.exec(e.value);
			}
			return false;
		}
	
</script>
@stop
