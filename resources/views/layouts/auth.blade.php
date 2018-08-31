<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hi-Health</title>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">
        @stack('head')
    </head>
    <body class="hold-transition login-page">
        @if ($errors and count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong><br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('contents')
        <script type="text/javascript">
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token()
            ]) !!};
        </script>
        <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
        @stack('scripts')
    </body>
</html>
