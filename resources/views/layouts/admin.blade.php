<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hi-Health</title>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">
        @stack('head')
    </head>
    <body class="skin-blue sidebar-mini">
        <div id="app">
            <div class="wrapper">
                @include('partials.header')
                @include('partials.sidebar')
                <div class="content-wrapper">
                    <section class="content">
                        @yield('contents')
                    </section>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token()
            ]) !!};
        </script>
        <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
        @stack('scripts')
    </body>
</html>
