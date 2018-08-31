@extends('layouts.admin')
@push('head')
<style type="text/css">

</style>
@endpush
@section('contents')
<h3>
    <i class="fa fa-dashboard"></i>
    總覽
</h3>
<hr />
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>{{ $services->count() }}</h3>
                <p>成交交易</p>
            </div>
            <div class="icon">
                <i class="fa fa-star"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3>${{ number_format($services->chargeAmount()) }}</h3>
                <p>成交總金額</p>
            </div>
            <div class="icon">
                <i class="fa fa-dollar"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $service_plans->count() }}</h3>
                <p>已上傳課程</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-aqua-active">
            <div class="inner">
                <h3>{{ $service_plans->videosCount() }}</h3>
                <p>已上傳影片</p>
            </div>
            <div class="icon">
                <i class="fa fa-video-camera"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3>{{ $member_requests->where('treatment_type', 1)->count() }}</h3>
                <p>諮詢神經方面</p>
            </div>
            <div class="icon">
                <i class="fa fa-heart"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-orange-active">
            <div class="inner">
                <h3>{{ $member_requests->where('treatment_type', 2)->count() }}</h3>
                <p>諮詢骨科方面</p>
            </div>
            <div class="icon">
                <i class="fa fa-child"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $members->where('online', 1)->count() }}</h3>
                <p>線上會員</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-green-active">
            <div class="inner">
                <h3>{{ $members->count() }}</h3>
                <p>會員人數</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-gray">
            <div class="inner">
                <h3>{{ $doctors->where('online', 1)->count() }}</h3>
                <p>線上員工</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-gray-active">
            <div class="inner">
                <h3>{{ $doctors->count() }}</h3>
                <p>員工人數</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>交易序號</th>
                    <th>服務對象</th>
                    <th>服務人員</th>
                    <th>服務類型</th>
                    <th>服務費用</th>
                    <th>付款方式</th>
                    <th>付款狀態</th>
                    <th>建立時間</th>
                    <th>更新時間</th>
                    <th>功能</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services->take(5) as $service)
                    <tr>
                        <td>{{ $service->order_number }}</td>
                        <td>
                            <a href="{{ route('admin-services-list-by-member', ['member_id' =>  $service->member ? $service->member->id : '']) }}">{{ $service->member ? $service->member->name : '' }}</a>
                        </td>
                        <td>
                            <a href="{{ route('admin-services-list-by-doctor', ['doctor_id' => $service->doctor ? $service->doctor->id : '']) }}">{{ $service->doctor ? $service->doctor->name :'' }}</a>
                        </td>
                        <td>{{ $service->treatment_type_text }}</td>
                        <td>${{ number_format($service->charge_amount, 0) }}</td>
                        <td>{{ $service->payment_method_text }}</td>
                        <td>{{ $service->payment_status_text }}</td>
                        <td>{{ $service->created_at }}</td>
                        <td>{{ $service->updated_at }}</td>
                        <td>
                            <a href="{{ route('admin-services-detail', ['service_id' => $service->id]) }}">明細</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="10">目前沒有資料</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')

@endpush
