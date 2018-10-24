@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    業務管理 - 業務列表
</h3>
<hr />
<div class="row">
    <div class="col-md-12 links clearfix">
        <a href="{{ route('admin-managers-add-form') }}" class="pull-right"><i class="fa fa-plus"></i> 新增業務</a>
    </div>
</div>
<div id="doctors-table" class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>編號</th>
                <th>姓名</th>
                
                <th>帳號</th>
                <th>縣市</th>
                <th>區域</th>
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
                    <td>{{ $user->manager->id }}</td>
                    <td>
                        <a href="{{ route('admin-services-list-by-doctor', ['manager_id' => $user->id]) }}">{{ $user->name }}</a>
                    </td>
                    
                    <td>{{ $user->account }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->district }}</td>
                    <td>{{ $user->status ? '啟用' : '停用' }}</td>
                    <td>
                        <a href="{{ route('admin-managers-edit-form', ['manager_id' => $user->id]) }}">修改</a>
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
