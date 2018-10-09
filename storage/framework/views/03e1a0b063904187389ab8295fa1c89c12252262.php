<?php $__env->startSection('contents'); ?>
    <h3>
        <i class="fa fa-cogs"></i>
        系統管理 - 設定
    </h3>
    <hr />
    <div id="settings-form" class="row">
        <div class="col-md-4">
            <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-group">
                    <label for="<?php echo e($setting->key); ?>"><?php echo e($setting->getValue('name')); ?></label>
                    <?php if($setting->getValue('type') === 'text'): ?>
                        <input type="text" class="form-control" id="<?php echo e($setting->key); ?>" v-model="settings.<?php echo e($setting->key); ?>" placeholder="<?php echo e($setting->getValue('placeholder')); ?>" />
                    <?php elseif($setting->getValue('type') === 'email'): ?>
                        <input type="email" class="form-control" id="<?php echo e($setting->key); ?>" v-model="settings.<?php echo e($setting->key); ?>" placeholder="<?php echo e($setting->getValue('placeholder')); ?>" />
                    <?php elseif($setting->getValue('type') === 'number'): ?>
                        <input type="number" class="form-control" id="<?php echo e($setting->key); ?>" v-model="settings.<?php echo e($setting->key); ?>" placeholder="<?php echo e($setting->getValue('placeholder')); ?>" />
                    <?php endif; ?>
                    <div class="error" v-if="messages.<?php echo e($setting->key); ?>">{{ messages.<?php echo $setting->key; ?>.join(', ') }}</div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                <button class="btn btn-primary" v-on:click="submit">儲存</button>
            </div>
        </div>
    </div>
    <div id="banners-form" class="row">
        <div class="col-md-8">
            <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-group">
                    <label for="banner[<?php echo e($banner->key); ?>]">Banner <?php echo e($banner->key); ?></label>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo e($banner->getValue('image')); ?>" class="img-responsive" />
                        </div>
                        <div class="col-md-8">
                            <input type="file" id="banner<?php echo e($banner->key); ?>" data-key="<?php echo e($banner->key); ?>" v-on:change="processBanner" /><br /><br />
                            <input type="text" class="form-control" v-model="banners.key<?php echo e($banner->key); ?>.redirect_url" />
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
                <button class="btn btn-primary" v-on:click="submit">儲存</button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    new Vue({
        el: '#settings-form',
        data: {
            settings: 
                <?php
                    echo $settings->map(function ($setting) {
                        return [
                            $setting->key => $setting->getValue('value'),
                        ];
                    })->collapse()
                    ->toJson();
                ?>,
            messages: {}
        },
        methods: {
            submit: function(event) {
                var self = this;
                var post_data = this.$data.settings;
                axios.post('/api/settings', post_data)
                    .then(function(response) {
                        alert('修改完成');
                        window.location.reload();
                    })
                    .catch(function(error) {
                        var response = error.response;
                        self.messages = response.data.reason;
                    });
            }
        }
    });
    new Vue({
        el: '#banners-form',
        data: {
            banners: 
                <?php
                    echo $banners->map(function ($banner) {
                        return [
                            'key'.$banner->key => [
                                'image' => null,
                                'redirect_url' => $banner->getValue('redirect_url'),
                            ],
                        ];
                    })->collapse()
                    ->toJson();
                ?>,
            messages: {}
        },
        methods: {
            processBanner: function(event) {
                var key = event.target.getAttribute('data-key');
                this.$data.banners['key' + key].image = event.target.files[0];
            },
            submit: function(event) {
                var self = this;
                var post_data = this.banners;
                var form_data = new FormData()
                for (var key in post_data) {
                    var index = key.replace('key', '');
                    form_data.append('redirect_url[' + index + ']', post_data[key].redirect_url);
                    form_data.append('images[' + index + ']', post_data[key].image);
                }
                axios.post('/api/settings/banners', form_data, {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }).then(function(response) {
                    alert('修改完成');
                    window.location.reload();
                }).catch(function(error) {
                    var response = error.response;
                    self.messages = response.data.reason;
                });
            }
        }
    });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>