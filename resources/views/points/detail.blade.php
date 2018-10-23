

@extends('points.master')
@section('content')
	<div class="w3-center" style="padding-top: 100px">
		<em style="font-size: 16px;">HiPoint當前兌台幣匯率為 1:{{$HiPointRate}}</em>
	</div>
	<div class="w3-center" style="padding-top: 30px">
		<em style="font-size: 18px;">您當前有 {{ $RemainedPoint }} HiPoint</em>

	</div>
	<div class="w3-center">
		<em style="font-size: 18px;">共值 {{$RemainedPoint*$HiPointRate}} 台幣</em>
	</div>
	
@endsection
