@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    會員管理 - 會員列表
</h3>
<hr />
<div id="doctors-list" class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>編號</th>
                <th>姓名</th>
                <th>帳號</th>
                <th>縣市</th>
                <th>區域</th>
                <th>服務開始</th>
                <th>剩餘天數</th>
                <th>狀態</th>
                <th>功能</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td class="text-right" colspan="7">{{ $users }}</td>
            </tr>
        </tfoot>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <a href="{{ route('admin-services-list-by-member', ['member_id' => $user->id]) }}">{{ $user->name }}</a>
                    </td>
                    <td>{{ $user->account }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->district }}</td>
                    <td>{{ $user->service->started_at??'---'}}</td>
                    <td>{{ $user->service && $user->service->leave_days > 0 ? $user->service->leave_days:'0'}}</td>
                    <td>{{ $user->status ? '啟用' : '停用' }}</td>
                    <td>
                        <a href="{{ route('admin-members-detail', ['member_id' => $user->id]) }}">查看</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="7">目前沒有資料</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
@push('scripts')

@endpush
