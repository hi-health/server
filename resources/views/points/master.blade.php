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

		<!--SWEET ALERT-->
		<script src="{{asset('js/sweetalert2.all.min.js')}}"></script>
		<link rel="stylesheet" href="{{asset('css/sweetalert2.min.css')}}">

		<style>
			html,body{
				height: 100%;
			}
			.one-third {
  				width:calc(100% * 0.33333);
  				height: 25vw;
  				z-index: 100;
  				display: inline-block;
  				vertical-align:middle;
  				position: relative;
  			}
			#nav img{
				width: 45%;
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%,-50%);
			}
			#nav{
				background: #ffffff url('{{asset('HiPoint/bg_nav.png')}}') no-repeat center;
				background-size: cover;
				width: 100%;
				position: fixed;
				bottom: 0px;
				right: 0px;
			}
		</style>
	</head>
	<body>
		@yield('content')
		<div id='nav'>
			<span class="one-third">
				<a href="{{ route('point-transfer', ['users_id' => $users_id]) }}">
					<img src="{{asset('HiPoint/ico_pay.png')}}" >
				</a>
			</span><span class="one-third">
				<a href="{{ route('point-index', ['users_id' => $users_id]) }}">
					<img src="{{asset('HiPoint/ico_HiPoint.png')}}" >
				</a>
			</span><span class="one-third">
				<a href="{{ route('point-list-all-transaction', ['users_id' => $users_id]) }}">
					<img src="{{asset('HiPoint/ico_record.png')}}" >
				</a>
			</span>
		</div>

		
	</body>
</html>
