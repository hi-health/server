@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    服務管理 - 服務明細
</h3>
<hr />
<div id="services-detail">
    <dl class="dl-horizontal">
        <dt>交易序號</dt>
        <dd>{{ $service->order_number }}</dd>
        <dt>服務對象</dt>
        <dd>{{ $service->member->name }}</dd>
        <dt>服務人員</dt>
        <dd>{{ $service->doctor ? $service->doctor->name : '' }}</dd>
        <dt>服務類型</dt>
        <dd>{{ $service->treatment_type_text }}</dd>
        <dt>服務費用</dt>
        <dd>${{ number_format($service->charge_amount, 0) }}</dd>
        <dt>付款方式</dt>
        <dd>{{ $service->payment_method_text }}</dd>
        <dt>付款狀態</dt>
        <dd>{{ $service->payment_status_text }}</dd>
        <dt>付款時間</dt>
        <dd>{{ $service->paid_at or 'none' }}</dd>
        <dt>開始時間</dt>
        <dd>{{ $service->started_at or 'none' }}</dd>
        <dt>結束時間</dt>
        <dd>{{ $service->stopped_at or 'none' }}</dd>
        <dt>建立時間</dt>
        <dd>{{ $service->created_at }}</dd>
        <dt>更新時間</dt>
        <dd>{{ $service->updated_at }}</dd>

    </dl>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                @if ($service->invoice)
                    <div>
                        <img src="{{ $service->invoice }}" width="150" />
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label id="invoice">發票</label>
                <input type="file" id="invoice" class="form-control" v-on:change="processInvoice" />
                <div class="error" v-if="messages.invoice">@{{ messages.invoice.join(', ') }}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <button class="btn btn-primary" v-on:click="submit">儲存</button>
    </div>
</div>
@endsection
@push('scripts')
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
                axios.post('/api/services/{{ $service->id }}/invoice', form_data, {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }).then(function(response) {
                    alert('修改完成');
                    window.location.href = '{{ route('admin-services-list') }}';
                })
                .catch(function(error) {
                    var response = error.response;
                    self.messages = response.data.reason;
                });
            }
        }
    });
    </script>
@endpush
