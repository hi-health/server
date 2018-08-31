@extends('layouts.admin')
@push('head')
<style type="text/css">
    .score .fa {
        font-size: 32px;
    }
    .score .fa.active {
        color: #3c8dbc;
    }
    .score .date {
        line-height: 32px;
    }
    .score hr {
        margin: 5px 0;
    }
    .col-md-6 {
        min-height: 240px;
    }
</style>
@endpush
@section('contents')
<h3>
    <i class="fa fa-list-alt"></i>
    影片管理 - 影片明細 - 交易序號 {{ $service->order_number }}
</h3>
<hr />
<div id="services-videos-detail">
    @foreach ($service_plans->chunk(2) as $chunk)
        <div class="row">
            @foreach ($chunk as $service_plan)
                <div class="col-md-12">
                    <div class="box box-solid box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">課程時間 {{ $service_plan->started_at }} ~ {{ $service_plan->stopped_at }}</h3>
                            <div class="box-tools">建立於{{ $service_plan->created_at }}</div>
                        </div>
                        <div class="box-body">
                            @foreach ($service_plan->videos as $service_plan_video)
                                <div class="row">
                                    <div class="col-md-3">
                                        <a href="{{ url($service_plan_video->video) }}" target="_blank">
                                            <img class="img-responsive" src="{{ url($service_plan_video->thumbnail) }}" alt="點此開啟播放" title="點此開啟播放" />
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <?php
                                            $days = $service_plan_video->score->count();
                                            $average = $service_plan_video->score->average();
                                        ?>
                                        <div class="score">
                                            <br />
                                            <div>
                                                <i class="fa fa-calendar"></i>
                                                <b>復健了{{ $days }} 天</b>
                                            </div>
                                            <br />
                                            <div>平均分數
                                                @for ($i = 1; $i <= 3; $i++)
                                                    @if ($i <= $average)
                                                        <i class="fa fa-smile-o active"></i>
                                                    @else
                                                        <i class="fa fa-smile-o"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 score">
                                        @foreach ($service_plan_video->score as $daily)
                                            <div class="row">
                                                <div class="col-md-5 date">{{ $daily->created_at->format('Y-m-d h:i A') }}</div>
                                                <div class="col-md-5">
                                                    @for ($i = 1; $i <= 3; $i++)
                                                        @if ($i <= $daily->score)
                                                            <i class="fa fa-smile-o active"></i>
                                                        @else
                                                            <i class="fa fa-smile-o"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <hr />
                                        @endforeach
                                    </div>
                                </div>
                                <br />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection
@push('scripts')

@endpush
