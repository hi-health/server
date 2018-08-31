<!doctype html>
<html lang="<?php echo e(config('app.locale')); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo e(asset('css/lucius.css')); ?>" rel="stylesheet" type="text/css">
        <title>Laravel</title>

    </head>
    <body style='background: #E8F1F2'>
        <div>
            <?php if(Route::has('login')): ?>
                <div class="top-right links">
                    <?php if(Auth::check()): ?>
                        <a href="<?php echo e(url('/home')); ?>">Home</a>
                    <?php else: ?>
                        <a href="<?php echo e(url('/login')); ?>">Login</a>
                        <a href="<?php echo e(url('/register')); ?>">Register</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="clinic_list">
                <div class="title">診所列表</div>
                <div class="list_group">
                    <?php $id = 0; ?>
                    <?php $__currentLoopData = $clinic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $id++; ?>
                    <div class="title_container" role="tab" id="heading<?php echo e($id); ?>">
                        <h4 class="list_title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo e($id); ?>" aria-expanded="false" aria-controls="collapse<?php echo e($id); ?>">
                                <?php echo e($location); ?>

                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?php echo e($id); ?>" class="list_content collapse" role="tabpanel" aria-labelledby="heading<?php echo e($id); ?>">
                        <div class="list_body">
                            <?php $__currentLoopData = $val; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="clinic_content">
                                <div class="name">
                                    <a href="<?php echo e($v->web); ?>">
                                        <?php echo e($v->name); ?>

                                    </a>
                                </div>
                                <!--<div class="time">
                                    週一到週五：08:00 ~ 21:30<br>
                                    週　　　六：08:00 ~ 17:00
                                </div>-->
                                <div class="tel">電話：<?php echo e($v->phone); ?></div>
                                <div class="mail">地址：<?php echo e($v->address); ?></div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
