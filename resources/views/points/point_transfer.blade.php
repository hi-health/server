@extends('points.master')
@section('content')
	<form action="{{ url('api/point/'.$users_id.'/transfer') }}" method="post" class="w3-container w3-card-4 w3-light-grey w3-margin w3-center">
		<div class="w3-row w3-section">
			<b style="font-size: 25px">交易點數</b>
		</div>

		<div class="w3-row w3-section">
			
			<div class="w3-quarter" style="width: 60px">
				<i class="fa fa-lock" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="password" name="password" class="w3-input w3-border" placeholder="請輸入您的密碼">
			</div>
			
		</div>
		
		<div class="w3-row w3-section">
			<div class="w3-quarter" style="width: 60px">
  				<i class="fas fa-phone fa-flip-horizontal" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="text" name="receiver_account" class="w3-input w3-border" placeholder="請輸入轉入者之手機號碼">
			</div>
		</div>

		<div class="w3-row w3-section">
			<div class="w3-quarter" style="width: 60px">
  				<i class="fas fa-coins" style="font-size:26px; margin-top: 5px"></i>
			</div>
			<div class="w3-rest">
				<input type="text" name="transferred_point" class="w3-input w3-border" placeholder="請輸入欲轉出點數" style="ime-mode:disabled" onkeyup="return ValidateNumber(this,value)">
			</div>
		</div>

		<div class="w3-row w3-section w3-center">
			<input type="submit" onClick="return confirm('點數一旦轉移則無法收回，確定要轉移嗎？');" value="送出" class="w3-button w3-block w3-section w3-ripple w3-padding" style="background-color: #628194; color: white;">
		</div>

	</form>
  
<<<<<<< HEAD
  <div class="w3-padding-32">
    <div class="w3-bar w3-border">
      <a href="{{ route('point-list-all-transaction', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">交易紀錄</a>
      <a href="{{ route('point-list-consume', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">使用紀錄</a>
      <a href="{{ route('point-list-produce', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">獲得紀錄</a>
      <a href="{{ route('point-transfer', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">點數轉移</a>
    </div><br><br>
    <table align="left">
        <!-- @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul style="color:#fff;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif -->  <!-- 需要web middleware -->
      <form action="{{ url('api/point/'.$users_id.'/transfer') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <font face="monospace" size="4">密碼 : </font>
        <input type="password" name="password" class="form-control" placeholder="請輸入您的密碼"><br><br>
        <font face="monospace" size="4">轉移給 : </font>
        <input type="text" name="receiver_account" class="form-control" placeholder="請輸入轉入者之手機號碼"><br><br>
        <font face="monospace" size="4">欲轉移的點數 : </font>
        <input type="text" name="transferred_point" class="form-control" placeholder="請輸入欲轉出點數" style="ime-mode:disabled" onkeyup="return ValidateNumber(this,value)"><br><br>
        <input type="submit" onClick="return confirm('點數一旦轉移則無法收回，確定要轉移嗎？');" value="送出" class="button">

      </form>
    </table>
  </div>
</header>

</body>
</html>
=======
@endsection


>>>>>>> origin/test


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
