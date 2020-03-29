@extends('layouts.main')

@section('searchValue', $searchValue)

@section('info')
    <table class="table table-light hidden-overflow-table">
        <tbody>
            @yield('infoContent')
        </tbody>
    </table>
@endsection

@section('tableDescription')
    Table of discovered @yield('discoveredType')
@endsection