@extends('points.master')
@section('content')


<style type="text/css">
	em{
		padding: 0px;
		margin: 0px;
	}
	.one-transaction{
		border:none !important;

	}
	#record{
		background: #ffffff url('{{asset('HiPoint/bg_record.png')}}') no-repeat center;
		background-size: cover;
		width: 100%;
		min-height: 100%;
		padding-bottom: 120px
	}
	#title{
		padding-top: 20%;
		color: white;
		text-align: center;
		margin-bottom: 50px;
	}
	.date{
		margin-left: 32px;
		margin-top: 40px;
		font-weight: 900;
	}
	.time{
		margin-top: 20px
	}

</style>

<div id="record">
	<div id="title">
		<em style="font-size: 25px;">歷史紀錄</em>
	</div>
	<ul class="w3-ul" style="color: white">
		@foreach($Transactions as $oneDayTransaction_key => $oneDayTransaction)
			<div class="date">
				{{$oneDayTransaction_key}}
			</div>
			@foreach($oneDayTransaction as $Transaction)
			<li class="one-transaction w3-bar" style="padding-top: 0px; padding-bottom: 0px;">
				<div class="time w3-bar-block w3-bar-item">
					{{ $Transaction->created_at->format('H:i') }}
				</div>
				<div class="w3-bar-block w3-bar-item w3-right" style="padding-top: 0px; padding-bottom: 0px">
					@if($Transaction->point>=0)
						<span class="w3-bar-item" style="padding: 0px;text-align: right;">
							<b style="font-size: 18px; color: #ffc888">+{{ $Transaction->point }}</b>
						</span>
						<span class="w3-bar-item" style="padding:0px;text-align: right;">
							<em style="font-size: 10px;">來自</em>
						</span>
						<span  class="w3-bar-item" style="padding:0px 0px 10px; font-size: 13px;text-align: right;">
							@if(isset($Transaction->pointconsume_id))
								{{ $Transaction->transaction->user->account }}
							@else
								您好健康APP
							@endif
						</span>
					@elseif($Transaction->point<0)
						<span class="w3-bar-item" style="padding: 0px;text-align: right;">
							<b style="font-size: 18px; color: #affff3">{{ $Transaction->point }}</b>
						</span>
						<span class="w3-bar-item" style="padding: 0px;text-align: right;">
							<em style="font-size: 10px;">給予</em>
						</span>
						<span  class="w3-bar-item" style="padding:0px 0px 10px; font-size: 13px;text-align: right;">
							@if(isset($Transaction->transaction->user->account))
								{{ $Transaction->transaction->user->account }}
							@else
								未知
							@endif
						</span>
					@endif
				</div>
			</li>
			@endforeach
		@endforeach

	</ul>
</div>

@endsection
  