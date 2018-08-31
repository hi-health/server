@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    服務管理 - {{ $doctor->name }}的服務列表
</h3>
<hr />
<div id="services-table" class="table-responsive">
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
                <th>狀態</th>
                <th>功能</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td class="text-right" colspan="7">{{ $services }}</td>
            </tr>
        </tfoot>
        <tbody>
            @forelse ($services as $service)
                <tr @if( $service->payment_status==1 && strlen($service->invoice)==0)style="color:#F00"@endif>
                    <td>{{ $service->order_number }}</td>
                    <td>{{ $service->member->name or '---' }}</td>
                    <td>{{ $service->doctor->name or '---' }}</td>
                    <td>{{ $service->treatment_type_text }}</td>
                    <td>${{ number_format($service->charge_amount, 0) }}</td>
                    <td>{{ $service->payment_method_text }}</td>
                    <td>{{ $service->payment_status_text }}</td>
                    <td>{{ $service->created_at }}</td>
                    <td>{{ $service->updated_at }}</td>
                    <td>{{ $service->invoice_status_text }}</td>
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
@endsection
@push('scripts')

@endpush
