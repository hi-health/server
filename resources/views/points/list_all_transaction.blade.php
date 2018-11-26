@extends('points.master')
@section('content')


<style type="text/css">
	em{
		padding: 0px;
		margin: 0px;
	}
	li{
		border:none !important;
	}
	#record{
		background: #ffffff url('{{asset('HiPoint/bg_record.png')}}') no-repeat center;
		background-size: cover;
		width: 100%;
	}
	#title{
		padding-top: 20%;
		color: white;
		text-align: center;
	}
</style>

<div id="record">
	<div id="title">
		<em style="font-size: 25px;">歷史紀錄</em>
	</div>
	<ul class="w3-ul">
		@foreach($Transaction as $Transactions)
		<li class="w3-bar" style="padding-top: 0px; padding-bottom: 0px">
			<div class="w3-bar-block w3-bar-item" style="padding-top: 15px; padding-bottom: 0px">
				<span  class="w3-bar-item" style="padding-bottom: 0px; color: #3b6279">
					{{ $Transactions->created_at->format('Y-m-d') }}
				</span>
				<span  class="w3-bar-item" style="padding-top: 0px; color: #3b6279">
					{{ $Transactions->created_at->format('H:i') }}
				</span>
			</div>
			<div class="w3-bar-block w3-bar-item w3-right w3-center" style="padding-top: 0px; padding-bottom: 0px">
				@if($Transactions->point>=0)
					<span class="w3-bar-item" style="padding-bottom: 0px;">
						<b style="font-size: 22px; color: #fe7168">+{{ $Transactions->point }}</b>
					</span>
					<span class="w3-bar-item" style="padding-bottom: 0px; padding-top: 0px; color: #3b6279">
						<em style="font-size: 11px;">來自</em>
					</span>
					<span  class="w3-bar-item" style="padding-top: 0px; font-size: 13px; color: #3b6279">
						@if(isset($Transactions->pointconsume_id))
							{{ $Transactions->transaction->user->account }}
						@else
							您好健康APP
						@endif
					</span>
				@elseif($Transactions->point<0)
					<span class="w3-bar-item" style="padding-bottom: 0px;">
						<b style="font-size: 22px; color: #628194">{{ $Transactions->point }}</b>
					</span>
					<span class="w3-bar-item" style="padding-bottom: 0px; padding-top: 0px; color: #3b6279">
						<em style="font-size: 11px;">給予</em>
					</span>
					<span  class="w3-bar-item" style="padding-top: 0px; font-size: 13px; color: #3b6279">
						@if(isset($Transactions->transaction->user->account))
							{{ $Transactions->transaction->user->account }}
						@else
							未知
						@endif
					</span>
				@endif
			</div>
		</li>
		@endforeach

	</ul>
</div>

@endsection
  