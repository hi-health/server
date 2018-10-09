<!DOCTYPE html>
<html>
<title>HI-Health</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1 {font-family: "Raleway", Arial, sans-serif}
h1 {letter-spacing: 6px}
.w3-row-padding img {margin-bottom: 12px}
</style>
<body>

<!-- !PAGE CONTENT! -->
<div class="w3-content" style="max-width:1500px">

<!-- Header -->
<header class="w3-panel w3-center w3-opacity" style="padding:128px 16px">
  <h1 class="w3-xlarge">歡迎來到點數系統</h1>
  <h1>您的點數 : {{$RemainedPoint}} </h1>
  
  <div class="w3-padding-32">
    <div class="w3-bar w3-border">
      <a href="{{ route('point-list-all-transaction', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">交易紀錄</a>
      <a href="{{ route('point-list-consume', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">使用紀錄</a>
      <a href="{{ route('point-list-produce', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">獲得紀錄</a>
      <a href="{{ route('point-transfer', ['users_id' => $users_id]) }}" class="w3-bar-item w3-button">點數轉移</a>
    </div><br>

    <h3>從使用者獲得的點數：</h3>
<<<<<<< HEAD
    <table align="center">
=======

    <table align="center">

>>>>>>> c188eaedbd37d12db97ac2efe7b674a58b89c96d
      <thead>
        <tr>
          <td>點數</td>
          <td>使用者</td>
          <td>時間</td>
        </tr>
      </thead>
      @foreach($PointProduce_FromUser as $PointProduce_FromUsers)
      <tbody>
        <tr>
          <td>{{ $PointProduce_FromUsers->point }}</td>
          <td>{{ $PointProduce_FromUsers->pointconsume_id }}</td>
          <td>{{ $PointProduce_FromUsers->created_at->format('Y-m-d') }}</td>
        </tr>
      </tbody>
      @endforeach
    </table>

    <h3>從每日復健獲得的點數</h3>
<<<<<<< HEAD
    <table align="center">
=======

    <table align="center">

>>>>>>> c188eaedbd37d12db97ac2efe7b674a58b89c96d
      <thead>
        <tr>
          <td>點數</td>
          <td>復健</td>
          <td>時間</td>
        </tr>
      </thead>
      @foreach($PointProduce_FromDaily as $PointProduce_FromDailys)
      <tbody>
        <tr>
          <td>{{ $PointProduce_FromDailys->point }}</td>
          <td>{{ $PointProduce_FromDailys->service_plan_daily_id }}</td>
          <td>{{ $PointProduce_FromDailys->created_at->format('Y-m-d') }}</td>
        </tr>
      </tbody>
      @endforeach
    </table>
  </div>
</header>

</body>
</html>