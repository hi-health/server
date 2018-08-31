<?php $__env->startSection('contents'); ?>
    <div class="login-box">
        <div class="login-logo">
            <a href="javascript:void(0);"><b>Hi </b>Health</a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to backend</p>
            <form action="<?php echo e(route('admin-login-auth')); ?>" method="post">
                <div class="form-group has-feedback">
                    <input type="text" name="account" class="form-control" placeholder="Account">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>