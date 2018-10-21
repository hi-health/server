<!DOCTYPE html>
<html>
	<head>
		<title>HI-Health</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
			.w3-row-padding img {margin-bottom: 12px}
			.point-tab {
				background-color: #a6a6a6;
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
	<body>
		<div class="w3-row point-tab" style="padding-top: 15px">
			<div class="w3-third w3-center">
				<a href="{{ route('point-transfer', ['users_id' => $users_id]) }}">
					<img src="{{asset('ico_pay.png')}}" >
				</a>
			</div>
			<div class="w3-third w3-center">
				<img src="{{asset('ico_HiPoint.png')}}" >
			</div>
			<div class="w3-third w3-center">
				<a href="{{ route('point-list-all-transaction', ['users_id' => $users_id]) }}">
					<img src="{{asset('ico_record.png')}}" >
				</a>
			</div>
		</div>
		<div class="w3-row point-tab w3-center">
			<div class="w3-third w3-center" style="margin-top: 10px">
				<a href="{{ route('point-transfer', ['users_id' => $users_id]) }}">交易點數</a>
			</div>
			<div class="w3-third w3-center" style="margin-top: 10px">
				<img src="{{asset('ico_HiPoint_word.png')}}" style="width: 65%">
			</div>
			<div class="w3-third w3-center" style="margin-top: 10px">
				<a href="{{ route('point-list-all-transaction', ['users_id' => $users_id]) }}">交易紀錄</a>
			</div>
		</div>
		<div class="w3-row point-tab" style="padding-bottom: 10px">
			<div class="w3-third w3-center">
				&nbsp;
			</div>
			<div class="w3-third w3-center">
				{{ $RemainedPoint }}
			</div>
			<div class="w3-third w3-center">
				&nbsp;
			</div>
		</div>
		@yield('content')
	</body>
</html>
