@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-plus"></i>
    業務管理 - 新增業務
</h3>
<hr />
<div id="manager-add">
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label for="account">帳號</label>
                <input type="text" class="form-control" id="account" v-model="form.account" placeholder="輸入帳號">
                <div class="error" v-if="messages.account">@{{ messages.account.join(', ') }}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label for="password">密碼</label>
                <input type="password" class="form-control" id="password" v-model="form.password" placeholder="輸入密碼">
                <div class="error" v-if="messages.password">@{{ messages.password.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label for="password-confirmation">再次輸入密碼</label>
                <input type="password" class="form-control" id="password-confirmation" v-model="form.password_confirmation" placeholder="再次輸入密碼">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label>狀態</label>
                <div class="radio">
                <label>
                    <input type="radio" v-model="form.status" name="status" value="1" checked="checked" />啟用
                </label>
                <label>
                    <input type="radio" v-model="form.status" name="status" value="0" />停用
                </label>
            </div>
            <div class="error" v-if="messages.status">@{{ messages.status.join(', ') }}</div>
            </div>

            {{--<div class="col-md-3">
                <label>在線</label>
                <div class="radio">
                    <label>
                        <input type="radio" v-model="form.online" name="online" value="1" checked="checked" />是
                    </label>
                    <label>
                        <input type="radio" v-model="form.online" name="online" value="0" />否
                    </label>
                </div>
                <div class="error" v-if="messages.online">@{{ messages.online.join(', ') }}</div>
            </div>--}}
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label for="name">姓名</label>
                <input type="text" class="form-control" id="name" v-model="form.name" placeholder="輸入姓名">
                <div class="error" v-if="messages.name">@{{ messages.name.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label>性別</label>
                <div class="radio">
                    <label>
                        <input type="radio" v-model="form.male" name="male" value="0" checked="checked" />男
                    </label>
                    <label>
                        <input type="radio" v-model="form.male" name="male" value="1" />女
                    </label>
                </div>
                <div class="error" v-if="messages.male">@{{ messages.male.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label id="birthday">生日</label>
                <datepicker v-model="form.birthday" format="yyyy-MM-dd" input-class="form-control"></datepicker>
                <div class="error" v-if="messages.birthday">@{{ messages.birthday.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label id="avatar">頭像</label>
                <input type="file" id="avatar" class="form-control" v-on:change="processAvatar" />
                <div class="error" v-if="messages.avatar">@{{ messages.avatar.join(', ') }}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label for="name">銀行帳號</label>
                <input type="text" class="form-control" id="bank_account" v-model="form.bank_account" placeholder="輸入銀行帳號">
                <div class="error" v-if="messages.bank_account">@{{ messages.bank_account.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label for="name">聯絡電話</label>
                <input type="text" class="form-control" id="phone" v-model="form.phone" placeholder="輸入聯絡電話">
                <div class="error" v-if="messages.phone">@{{ messages.phone.join(', ') }}</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label for="city">縣市</label>
                <select v-model="form.city_id" id="city" class="form-control" v-on:change="selectCity">
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" data-districts="{{ json_encode($city->districts) }}">{{ $city->name }}</option>
                    @endforeach
                </select>
                <div class="error" v-if="messages.city_id">@{{ messages.city_id.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label for="district">區域</label>
                <select v-model="form.district_id" id="district" class="form-control">
                    <option v-for="district in districts" v-bind:value="district.id">@{{ district.name }}</option>
                </select>
                <div class="error" v-if="messages.district_id">@{{ messages.district_id.join(', ') }}</div>
            </div>
            {{--<div class="col-md-3">
                <label for="longitude">經度</label>
                <input type="text" class="form-control" id="longitude" v-model="form.longitude" placeholder="輸入經度">
                <div class="error" v-if="messages.longitude">@{{ messages.longitude.join(', ') }}</div>
            </div>
            <div class="col-md-3">
                <label for="latitude">緯度</label>
                <input type="text" class="form-control" id="latitude" v-model="form.latitude" placeholder="輸入緯度">
                <div class="error" v-if="messages.latitude">@{{ messages.latitude.join(', ') }}</div>
            </div>--}}
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            {{-- 
            <div class="col-md-3">
                <label for="treatment_type">服務類型</label>
                <select v-model="form.treatment_type" id="city" class="form-control">
                    @foreach(config('define.treatment_types') as $value => $treatment_type)
                        <option value="{{ $value }}">{{ $treatment_type }}</option>
                    @endforeach
                </select>
                <div class="error" v-if="messages.treatment_type">@{{ messages.treatment_type.join(', ') }}</div>
            </div>
             --}}
            {{-- 
            <div class="col-md-3">
                <label for="education_bonus">學歷加給</label>
                <input type="number" class="form-control" id="education_bonus" v-model="form.education_bonus" placeholder="輸入職務加給" value="0" />
                <div class="error" v-if="messages.education_bonus">@{{ messages.education_bonus.join(', ') }}</div>
            </div>
            --}}
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        </div>
    </div>
    <div class="form-group">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <button class="btn btn-primary" v-on:click="submit">建立</button>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    new Vue({
        el: '#manager-add',
        data: {
            districts: [],
            form: {
                account: null,
                password: null,
                status: true,
                online: true,
                name: null,
                male: 1,
                birthday: null,
                avatar: null,
                city_id: null,
                district_id: null,
                bank_account:null,
                phone:null
            },
            messages: {}
        },
        methods: {
            processAvatar: function(event) {
                this.$data.form.avatar = event.target.files[0];
            },
            selectCity: function(event) {
                this.$data.districts = $('[value="' + this.$data.form.city_id + '"]', event.target).data('districts');
            },
            submit: function(event) {
                var self = this;
                var post_data = this.$data.form;
                if (post_data.birthday && _.isObject(post_data.birthday)) {
                    var year = post_data.birthday.getFullYear();
                    var month = (post_data.birthday.getMonth() + 1);
                    var day = post_data.birthday.getDate();
                    post_data.birthday = year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
                }
                if (post_data.due_at && _.isObject(post_data.due_at)) {
                    var year = post_data.due_at.getFullYear();
                    var month = (post_data.due_at.getMonth() + 1);
                    var day = post_data.due_at.getDate();
                    post_data.due_at = year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
                }
                var form_data = new FormData()
                for (var key in post_data) {
                    form_data.append(key, post_data[key]);
                }
                form_data.append('avatar', this.$data.form.avatar);
                axios.post('/api/managers', form_data, {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }).then(function(response) {
                    alert('建立完成');
                    window.location.href = '{{ route('admin-managers-list') }}';
                })
                .catch(function(error) {
                    var response = error.response;
                    self.messages = response.data.reason;
                });
            }
        },
        components: {
            Datepicker
        }
    });
    </script>
@endpush
