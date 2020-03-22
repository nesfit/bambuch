<html lang="en">
    <head>
        <title>Crypto Corvid - @yield('title')</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        <div class="navbar">
            @section('sidebar')
                This is the master sidebar.
            @show
        </div>
    
    <div class="container">
        @yield('content')
    </div>

        <h1>Example heading <span class="badge badge-secondary">New</span></h1>
    
    </body>
</html>