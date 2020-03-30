@extends('layouts.main')
  
@section('body')
    <div class="search-grid-container">
        {{-- search bar --}}
        <div class="container d-flex">
            <div class="col-12 align-self-center">
                <form class="form-inline" action="/search/@yield('searchRoute', 'unknown')">
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
    
            {{-- table desctiption --}}    
            <p class="text-secondary mb-2 ml-2">@yield('tableDescription')</p>
    
            {{-- fixed result head --}}
            <table class="table table-light hidden-overflow-table">
                <thead>
                    @yield('headContent')
                </thead>
            </table>
    
            {{-- scrollable result body --}}
            <div class="table-limit-40">
                <table class="table table-light hidden-overflow-table">
                    <tbody>
                        @yield('bodyContent')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection