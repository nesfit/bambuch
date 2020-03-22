<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Crypto Corvid - @yield('title')</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        <div class="navbar">
            <div class="col-3">Menu</div>
            <div class="col-6">Crypto Corvid</div>
            <div class="col-3">
                <button type="button" class="btn btn-info">Settings</button>
                <i class="far fa-user"></i>
            </div>
        </div>
    
        <div class="container">
            @yield('content')
        </div>

        <h1>Example heading <span class="badge badge-secondary">New</span></h1>
    
    </body>
</html>