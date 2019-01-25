@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    員工管理 - 員工列表
</h3>
<hr />
<div class="row">
    <div class="col-md-12 links clearfix">
        <a href="{{ route('admin-doctors-add-form') }}" class="pull-right"><i class="fa fa-plus"></i> 新增員工</a>
    </div>
</div>

@if($users)
<div id="doctors-table" class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>編號</th>
                <th>姓名</th>
                <th>職稱</th>
                <th>帳號</th>
                <th>縣市</th>
                <th>區域</th>
                <th>狀態</th>
                <th>會員期限</th>
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
                    <td>{{ $user->doctor->id }}</td>
                    <td>
                        @if(Auth::user() -> manager)
                            {{ $user->name }}
                        @else
                             <a href="{{ route('admin-services-list-by-doctor', ['doctor_id' => $user->id]) }}">{{ $user->name }}</a>
                        @endif
                    </td>
                    <td>{{ $user->doctor->title }}</td>
                    <td>{{ $user->account }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->district }}</td>
                    <td>{{ $user->status ? '啟用' : '停用' }}</td>
                    <td>
                        @if ($user->doctor->is_valid)
                            @if ($user->doctor->due_at)
                                {{ $user->doctor->due_at->toDateString() }}
                                剩餘{{ $user->doctor->due_at->diffInDays(\Carbon\Carbon::today())}}天
                            @endif
                        @else
                            已過期
                        @endif
                            
                    </td>
                    <td>
                        <a href="{{ route('admin-doctors-edit-form', ['doctor_id' => $user->id]) }}">修改</a>
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

@else
<div style="text-align:center; width:100%">
    <pre>尚未有註冊員工</pre>
</div>
@endif

@endsection
@push('scripts')

@endpush
