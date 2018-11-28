

@extends('points.master')
@section('content')

<style type="text/css">
	#detail{
		background: #ffffff url('{{asset('HiPoint/bg_HiPoint.png')}}') no-repeat center;
		background-size: cover;
		width: 100%;
		height: 100%;
	}
	#title{
		padding-top: 20%;
		color: white;
		text-align: center;
	}
	#circle{
		background: rgba(255,255,255,0) url('{{asset('HiPoint/circle_HiPoint.png')}}') no-repeat center;
		background-size: cover;
		position: absolute;
		width: 95%;
		height: 95vw;
		top: 50%;
		left: 50%;
		transform: translate(-50%,-50%);
		color: white;
	}
	#circle-content{
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%,-50%);
	}
</style>
<div id="detail">
	<div id="title">
		<em style="font-size: 25px;">Hi Points</em>
	</div>
	<div id="circle">
		<div id="circle-content">
			<div class="w3-center" style="margin: 13px 0px">
				<em style="font-size: 12px;">回購價格每點</em><br>
				<em style="font-size: 12px;">NT$ </em><em style="font-size: 24px; color: #ffc888">{{$HiPointRate}}</em>
			</div>
			<div class="w3-center" style="margin: 13px 0px">
				<em style="font-size: 12px;">您目前共有</em><br>
				<em style="font-size: 24px; color: #ffc888">{{ $RemainedPoint }}</em><em style="font-size: 12px;"> 點</em>

			</div>
			<div class="w3-center" style="margin: 13px 0px">
				<em style="font-size: 12px;">回購總價格</em><br>
				<em style="font-size: 12px;">NT$ </em><em style="font-size: 24px; color: #ffc888">{{$RemainedPoint*$HiPointRate}}</em>

			</div>
		</div>
	</div>
	
</div>	
@endsection
