<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hi-Health</title>
        <link href="<?php echo e(mix('css/app.css')); ?>" rel="stylesheet" type="text/css">
        <?php echo $__env->yieldPushContent('head'); ?>
    </head>
    <body class="hold-transition login-page">
        <?php if($errors and count($errors) > 0): ?>
            <div class="alert alert-danger">
                <strong>Whoops!</strong><br><br>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('contents'); ?>
        <script type="text/javascript">
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token()
            ]); ?>;
        </script>
        <script src="<?php echo e(mix('js/app.js')); ?>" type="text/javascript"></script>
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
