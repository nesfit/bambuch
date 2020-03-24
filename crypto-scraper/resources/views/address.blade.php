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
    <table class="table table-light result-table">
        <thead>
        <tr>
            <th class="result-small" scope="col">Source</th>
            <th class="result-medium" scope="col">URL</th>
            <th class="result-medium" scope="col">Label</th>
            <th class="result-timestamp" scope="col">Created</th>
            <th class="result-timestamp" scope="col">Updated</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($identities as $item)
                <tr>
                    <th class="result-small" scope="row">{{ $item->source }}</th>
                    <td class="result-medium"><a href="{{ $item->url }}" target="_blank">{{ $item->url }}</a></td>
                    <td class="result-medium">{{ $item->label }}</td>
                    <td class="result-timestamp">{{ $item->created }}</td>
                    <td class="result-timestamp">{{ $item->updated }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
