@extends('layouts.admin')
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    會員管理 - 會員明細
</h3>
<hr />
<div id="member-detail">
    <dl class="dl-horizontal">
        <dt>姓名</dt>
        <dd>{{ $member->name }}</dd>
        @if (!empty($member->avatar))
            <dt></dt>
            <dd>
                <img src="{{ $member->avatar }}" class="img-responsive" />
            </dd>
        @endif
        <dt>帳號</dt>
        <dd>{{ $member->account }}</dd>
        <dt>生日 </dt>
        <dd>{{ $member->birthday }}</dd>
        <dt>性別</dt>
        <dd>{{ $member->gender }}</dd>
        <dt>Email</dt>
        <dd>{{ $member->email }}</dd>
        <dt>縣市</dt>
        <dd>{{ $member->city }}</dd>
        <dt>鄉鎮</dt>
        <dd>{{ $member->district }}</dd>
        @if (!empty($member->facebook_id))
            <dt>Facebook編號</dt>
            <dd>{{ $member->facebook_id }}</dd>
            <dt>Facebook Token</dt>
            <dd>{{ $member->facebook_token }}</dd>
        @endif
        <dt>狀態</dt>
        <dd>{{ $member->status_text }}</dd>
        <dt>註冊日期</dt>
        <dd>{{ $member->created_at }}</dd>
        <dt>更新日期</dt>
        <dd>{{ $member->updated_at }}</dd>
    </dl>
</div>
@endsection
@push('scripts')

@endpush
