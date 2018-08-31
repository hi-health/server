<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/lucius.css') }}" rel="stylesheet" type="text/css">
        <title>Laravel</title>

    </head>
    <body style='background: #E8F1F2'>
        <div>
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>
            @endif
            <div class="clinic_list">
                <div class="title">診所列表</div>
                <div class="list_group">
                    <?php $id = 0; ?>
                    @foreach($clinic as $location => $val)
                    <?php $id++; ?>
                    <div class="title_container" role="tab" id="heading{{$id}}">
                        <h4 class="list_title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$id}}" aria-expanded="false" aria-controls="collapse{{$id}}">
                                {{$location}}
                            </a>
                        </h4>
                    </div>
                    <div id="collapse{{$id}}" class="list_content collapse" role="tabpanel" aria-labelledby="heading{{$id}}">
                        <div class="list_body">
                            @foreach($val as $v)
                            <div class="clinic_content">
                                <div class="name">
                                    <a href="{{$v->web}}">
                                        {{$v->name}}
                                    </a>
                                </div>
                                <!--<div class="time">
                                    週一到週五：08:00 ~ 21:30<br>
                                    週　　　六：08:00 ~ 17:00
                                </div>-->
                                <div class="tel">電話：{{$v->phone}}</div>
                                <div class="mail">地址：{{$v->address}}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
