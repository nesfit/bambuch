@extends('layouts.main')

@section('title', '| ' . ucfirst($searchRoute))
@section('searchRoute', $searchRoute)
@section('searchValue', $searchValue)

@section('info')
    <div class="container text-center">
        <h2>Nothing found!</h2>
    </div>
@endsection

