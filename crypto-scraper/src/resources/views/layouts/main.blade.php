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
                <div class="col-3 text-left">
                    <button class="btn-lg btn-primary"><i class="fas fa-bars"></i></button>
                </div>
                <div class="col-6 text-center">
                    <h1>Crypto Corvid</h1>
                </div>
                <div class="col-3 text-right">
                    <button type="button" class="btn-sm btn-secondary mr-3">Settings</button>
                    <button class="btn-lg btn-secondary btn-circle"><i class="far fa-user"></i></button>
                </div>
            </div>
            
            {{-- search bar --}}
            <div class="container d-flex">
                <div class="col-12 align-self-center">
                    <form class="form-inline" action="/search/@yield('searchRoute', '')">
                        <input 
                            class="form-control w-100 text-center search-input" 
                            type="search" 
                            placeholder="Search" 
                            aria-label="Search" 
                            name="search" 
                            value="@yield('searchValue', '')"
                        >
                    </form>
                </div>
            </div>
            
            {{-- result info --}}
            <div class="container">
                @yield('info')
            </div>
            
            {{-- result data --}}
            <div class="container-lg">
                @yield('resultTableInfo')
                @yield('resultTable')
            </div>
        </div>
    </body>
</html>