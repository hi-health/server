@extends('points.master')
@section('content')
	<form action="{{ url('api/point/'.$users_id.'/transfer') }}" method="post" class="w3-container w3-card-4 w3-light-grey w3-margin w3-center">
		<div class="w3-row w3-section">
			<b style="font-size: 25px">交易點數</b>
		</div>

		<div class="w3-row w3-section">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<font face="monospace" size="4">密碼 : </font>
			<input type="password" name="password" class="form-control" placeholder="請輸入您的密碼">
		</div>
		
		<div class="w3-row w3-section">
			<font face="monospace" size="4">轉移給 : </font>
			<input type="text" name="receiver_account" class="form-control" placeholder="請輸入轉入者之手機號碼">
		</div>

		<div class="w3-row w3-section">
			<font face="monospace" size="4">欲轉移的點數 : </font>
			<input type="text" name="transferred_point" class="form-control" placeholder="請輸入欲轉出點數" style="ime-mode:disabled" onkeyup="return ValidateNumber(this,value)">
		</div>

		<div class="w3-row w3-section w3-center">
			<input type="submit" onClick="return confirm('點數一旦轉移則無法收回，確定要轉移嗎？');" value="送出" class="w3-button w3-block w3-section w3-ripple w3-padding" style="background-color: #628194; color: white;">
		</div>

	</form>
  
@endsection




<script>
	function ValidateNumber(e, pnumber)
	{
	  if (!/^\d+$/.test(pnumber))
	  {
		e.value = /^\d+/.exec(e.value);
	  }
	  return false;
	}
</script>
