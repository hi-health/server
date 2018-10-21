

@extends('points.master')
@section('content')
	<ul class="w3-ul w3-card-4">
		@foreach($Transaction as $Transactions)
		<li class="w3-bar" style="padding-top: 0px; padding-bottom: 0px">
			<div class="w3-bar-block w3-bar-item" style="padding-top: 0px; padding-bottom: 0px">
				<span  class="w3-bar-item">
					{{ $Transactions->created_at->format('Y-m-d') }}
				</span>
				<span  class="w3-bar-item" style="padding-top: 0px;"">
					{{ $Transactions->created_at->format('H:i') }}
				</span>
			</div>
			<span class="w3-right w3-bar-item">
				<em style="font-size: 22px">{{ $Transactions->point }}</em>
			</span>
			
		</li>
		@endforeach

	</ul>
@endsection
  