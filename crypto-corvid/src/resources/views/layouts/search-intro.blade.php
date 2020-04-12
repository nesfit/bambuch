@extends('layouts.search')

@section('info')
    <div class="container text-center">
        <h3>Here you can search for {{ $searchType }} or pick one from the following.</h3>
    </div>
@endsection

@section('tableDescription')
    Table of available {{ $searchType }}
@endsection
