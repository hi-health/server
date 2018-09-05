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
  <h1 class="w3-xlarge">Welcome to Points System</h1>
  <h1>BoCheng    剩餘點數 : <?php echo e($RemainedPoint); ?> </h1>
  
  <div class="w3-padding-32">
    <div class="w3-bar w3-border">
      <a href="<?php echo e(route('point-list-all-transaction', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">All Transaction</a>
      <a href="<?php echo e(route('point-list-consume', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">Use History</a>
      <a href="<?php echo e(route('point-list-produce', ['users_id' => $users_id])); ?>" class="w3-bar-item w3-button">Earn History</a>
    </div>
    <table align="center" style="width:30%">
      <thead>
        <tr>
          <td>point</td>
          <td>time</td>
        </tr>
      </thead>
      <?php $__currentLoopData = $Transaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tbody>
        <tr>
          <td><?php echo e($Transactions->point); ?></td>
          <td><?php echo e($Transactions->created_at->format('Y-m-d')); ?></td>
        </tr>
      </tbody>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
  </div>
</header>

</body>
</html>