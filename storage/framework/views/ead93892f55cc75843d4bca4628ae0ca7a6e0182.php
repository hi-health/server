<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hi-Health</title>
        <link href="<?php echo e(mix('css/app.css')); ?>" rel="stylesheet" type="text/css">
        <?php echo $__env->yieldPushContent('head'); ?>
    </head>
    <body class="skin-blue sidebar-mini">
        <div id="app">
            <div class="wrapper">
                <?php echo $__env->make('partials.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('partials.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="content-wrapper">
                    <section class="content">
                        <?php echo $__env->yieldContent('contents'); ?>
                    </section>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token()
            ]); ?>;
        </script>
        <script src="<?php echo e(mix('js/app.js')); ?>" type="text/javascript"></script>
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
