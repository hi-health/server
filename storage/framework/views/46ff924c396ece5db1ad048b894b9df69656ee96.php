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
  .button {background-color: #e7e7e7; color: black;} /* Gray */ 
</style>
<body>

<!-- !PAGE CONTENT! -->
<div class="w3-content" style="max-width:1500px">

<!-- Header -->
<header class="w3-panel w3-center w3-opacity" style="padding:128px 16px">
  <h1 class="w3-xlarge">歡迎來到點數系統</h1>
  <h1>您的點數 : <?php echo e($RemainedPoint); ?> </h1>
  
  <div class="w3-padding-32">
    <div class="w3-bar w3-border">
      <a href="<?php echo e(route('point-list-all-transaction', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">交易紀錄</a>
      <a href="<?php echo e(route('point-list-consume', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">使用紀錄</a>
      <a href="<?php echo e(route('point-list-produce', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">獲得紀錄</a>
      <a href="<?php echo e(route('point-transfer', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">點數轉移</a>
    </div><br><br>
    <table align="left">
        <!-- <?php if(count($errors) > 0): ?>
        <div class="alert alert-danger">
            <ul style="color:#fff;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?> -->  <!-- 需要web middleware -->
      <form action="<?php echo e(url('api/point/'.$users_id.'/transfer')); ?>" method="post">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
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
