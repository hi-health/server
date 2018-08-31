@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    影片管理 - 課程列表
</h3>
<hr />
<div id="plans-table" class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>交易序號</th>
                <th>服務類型</th>
                <th>服務人員</th>
                <th>服務對象</th>
                <th>服務費用</th>
                <th>課程數量</th>
                <th>影片數量</th>
                <th>建立時間</th>
                <th>更新時間</th>
                <th>功能</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td class="text-right" colspan="7">{{ $service_plans_group }}</td>
            </tr>
        </tfoot>
        <tbody>
            @forelse ($service_plans_group as $service_plan)
                <?php
                    $service = $service_plan->service;
		    if(!$service->members_id) continue;
                    $plans = $service_plans->where('services_id', $service->id);
                    $video_count = $plans->sum(function($plan) {
                        return $plan->videos->count();
                    });
                ?>
                <tr>
                    <td>{{ $service->order_number }}</td>
                    <td>{{ $service->treatment_type_text }}</td>
                    <td>
                        <a href="{{ route('admin-services-list-by-doctor', ['doctor_id' => $service->doctor->id]) }}">{{ $service->doctor->name }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin-services-list-by-member', ['member_id' => $service->member->id]) }}">{{ $service->member->name }}</a>
                    </td>
                    <td>${{ number_format($service->charge_amount, 0) }}</td>
                    <td>{{ $plans->count() }}</td>
                    <td>{{ $video_count }}</td>
                    <td>{{ $service->created_at }}</td>
                    <td>{{ $service->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin-videos-detail', ['service_id' => $service->id]) }}">明細</a>
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
@endsection
@push('scripts')

@endpush
