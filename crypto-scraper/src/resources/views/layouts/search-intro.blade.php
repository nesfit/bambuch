@extends('layouts.main')

@section('info')
    <div class="container text-center">
        <h3>Here you can search for {{ $searchType }} or pick one from the following.</h3>
    </div>
@endsection

@section('resultTableInfo')
    <span class="text-secondary">Table of available {{ $searchType }}</span>
@endsection
