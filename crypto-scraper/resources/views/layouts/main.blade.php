<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Crypto Corvid @yield('title')</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        <div class="corvid-grid-container">
            <div class="navbar text-light bg-dark">
                <div class="col-3">Menu</div>
                <div class="col-6 text-center">
                    <h1>Crypto Corvid</h1>
                </div>
                <div class="col-3">
                    <button type="button" class="btn btn-info">Settings</button>
                    <i class="far fa-user"></i>
                </div>
            </div>

            <div class="container d-flex">
                <div class="col-12 align-self-center">
                    <form class="form-inline">
                        <input class="form-control w-100" type="search" placeholder="Search" aria-label="Search">
                    </form>
                </div>
            </div>
            
            <div class="container">
                @yield('info')
            </div>
            
            <div class="container-lg">
                @yield('resultTable')
            </div>
        </div>
    </body>
</html>