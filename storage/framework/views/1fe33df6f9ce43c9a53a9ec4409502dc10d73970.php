<body onload="document.purchase.submit()">
<!--    <h3>訂單編號: <?php echo e($order_number); ?></h3>
    <h3>付款金額: <?php echo e($amount); ?></h3>-->
    <form name="purchase" method="post" action="<?php echo e($action); ?>">
        <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <!--<label><?php echo e($key); ?></label>-->
                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>" />
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <!--<button type="submit">Submit</button>-->
    </form>
</body>
