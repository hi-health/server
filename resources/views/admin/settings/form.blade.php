@extends('layouts.admin')
@section('contents')
    <h3>
        <i class="fa fa-cogs"></i>
        系統管理 - 設定
    </h3>
    <hr />
    <div id="settings-form" class="row">
        <div class="col-md-4">
            @foreach ($settings as $key => $setting)
                <div class="form-group">
                    <label for="{{ $setting->key }}">{{ $setting->getValue('name') }}</label>
                    @if ($setting->getValue('type') === 'text')
                        <input type="text" class="form-control" id="{{ $setting->key }}" v-model="settings.{{ $setting->key }}" placeholder="{{ $setting->getValue('placeholder') }}" />
                    @elseif ($setting->getValue('type') === 'email')
                        <input type="email" class="form-control" id="{{ $setting->key }}" v-model="settings.{{ $setting->key }}" placeholder="{{ $setting->getValue('placeholder') }}" />
                    @elseif ($setting->getValue('type') === 'number')
                        <input type="number" class="form-control" id="{{ $setting->key }}" v-model="settings.{{ $setting->key }}" placeholder="{{ $setting->getValue('placeholder') }}" />
                    @endif
                    <div class="error" v-if="messages.{{ $setting->key }}">{{ messages.<?php echo $setting->key; ?>.join(', ') }}</div>
                </div>
            @endforeach
            <div class="form-group">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <button class="btn btn-primary" v-on:click="submit">儲存</button>
            </div>
        </div>
    </div>
    <div id="banners-form" class="row">
        <div class="col-md-8">
            @foreach ($banners as $banner)
                <div class="form-group">
                    <label for="banner[{{ $banner->key }}]">Banner {{ $banner->key }}</label>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ $banner->getValue('image') }}" class="img-responsive" />
                        </div>
                        <div class="col-md-8">
                            <input type="file" id="banner{{ $banner->key }}" data-key="{{ $banner->key }}" v-on:change="processBanner" /><br /><br />
                            <input type="text" class="form-control" v-model="banners.key{{ $banner->key }}.redirect_url" />
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="form-group">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <button class="btn btn-primary" v-on:click="submit">儲存</button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
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
@endpush
