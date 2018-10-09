<?php $__env->startSection('contents'); ?>
<h3>
    <i class="fa fa-list-alt"></i>
    服務管理 - 服務明細
</h3>
<hr />
<div id="services-detail">
    <dl class="dl-horizontal">
        <dt>交易序號</dt>
        <dd><?php echo e($service->order_number); ?></dd>
        <dt>服務對象</dt>
        <dd><?php echo e($service->member->name); ?></dd>
        <dt>服務人員</dt>
        <dd><?php echo e($service->doctor ? $service->doctor->name : ''); ?></dd>
        <dt>服務類型</dt>
        <dd><?php echo e($service->treatment_type_text); ?></dd>
        <dt>服務費用</dt>
        <dd>$<?php echo e(number_format($service->charge_amount, 0)); ?></dd>
        <dt>付款方式</dt>
        <dd><?php echo e($service->payment_method_text); ?></dd>
        <dt>付款狀態</dt>
        <dd><?php echo e($service->payment_status_text); ?></dd>
        <dt>付款時間</dt>
        <dd><?php echo e(isset($service->paid_at) ? $service->paid_at : 'none'); ?></dd>
        <dt>開始時間</dt>
        <dd><?php echo e(isset($service->started_at) ? $service->started_at : 'none'); ?></dd>
        <dt>結束時間</dt>
        <dd><?php echo e(isset($service->stopped_at) ? $service->stopped_at : 'none'); ?></dd>
        <dt>建立時間</dt>
        <dd><?php echo e($service->created_at); ?></dd>
        <dt>更新時間</dt>
        <dd><?php echo e($service->updated_at); ?></dd>

    </dl>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <?php if($service->invoice): ?>
                    <div>
                        <img src="<?php echo e($service->invoice); ?>" width="150" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label id="invoice">發票</label>
                <input type="file" id="invoice" class="form-control" v-on:change="processInvoice" />
                <div class="error" v-if="messages.invoice">{{ messages.invoice.join(', ') }}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" />
        <button class="btn btn-primary" v-on:click="submit">儲存</button>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    new Vue({
        el: '#services-detail',
        data: {
            districts: [],
            form: {
                
            },
            messages: {}
        },
        created: function() {
            this.selectCity(null);
        },
        methods: {
            processInvoice: function(event) {
                this.$data.form.invoice = event.target.files[0];
            },
            submit: function(event) {
                var self = this;
                var post_data = this.$data.form;

                var form_data = new FormData()
                for (var key in post_data) {
                    form_data.append(key, post_data[key]);
                }
                if (this.$data.form.invoice) {
                   form_data.append('invoice', this.$data.form.invoice);
                }
                axios.post('/api/services/<?php echo e($service->id); ?>/invoice', form_data, {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }).then(function(response) {
                    alert('修改完成');
                    window.location.href = '<?php echo e(route('admin-services-list')); ?>';
                })
                .catch(function(error) {
                    var response = error.response;
                    self.messages = response.data.reason;
                });
            }
        }
    });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>