<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Crypto Corvid @yield('title')</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        <div class="corvid-grid-container">
        
            {{-- navbar --}}
            <div class="navbar bg-primary">

                {{-- menu --}}
                <div class="col-3 text-left">
                    <div class="dropdown">
                        <button 
                            class="btn-lg btn-primary"
                            type="button"
                            id="dropdownMenu2"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <a class="dropdown-item" href="/search/address">Search address</a>
                            <a class="dropdown-item" href="/search/owner">Search owner</a>
                            <a class="dropdown-item" href="/search/source">Search source</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/scheduler">Scheduler</a>
                        </div>
                    </div>
                </div>

                {{-- name --}}
                <div class="col-6 text-center">
                    <h1>Crypto Corvid</h1>
                </div>

                {{-- settings --}}
                <div class="col-3 text-right">
                    <button type="button" class="btn-sm btn-secondary mr-3">Settings</button>
                    <button class="btn-lg btn-secondary btn-circle"><i class="far fa-user"></i></button>
                </div>
            </div>
        
            {{-- body --}}
            @yield('body')
        </div>
    </body>
</html>