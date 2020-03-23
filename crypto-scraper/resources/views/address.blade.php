@extends('layouts.main')

@section('title', 'Address')

@section('info')
    <table class="table table-light">
        <tbody>
            <tr>
                <td>
                    <span class="d-table">Owner</span>
                    <span id="owner">{{ $address->owner }}</span>
                </td>
                <td>
                    <span class="d-table">Currency</span>
                    <span id="currency">{{ $address->currency }}</span>
                </td>
                <td>
                    <span class="d-table">Category</span>
                    <span id="category">{{ $address->category }}</span>
                </td>
            </tr>
        </tbody> 
    </table>
@endsection

@section('resultTable')
    <table class="table table-light">
        <thead>
        <tr>
            <th scope="col">Source</th>
            <th scope="col">URL</th>
            <th scope="col">Label</th>
            <th scope="col">Created</th>
            <th scope="col">Updated</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($identities as $item)
                <tr class="over">
                    <th scope="row">{{ $item->source }}</th>
                    <td><a href="{{ $item->url }}" target="_blank">{{ $item->url }}</a></td>
                    <td>{{ $item->label }}</td>
                    <td>{{ $item->created }}</td>
                    <td>{{ $item->updated }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
