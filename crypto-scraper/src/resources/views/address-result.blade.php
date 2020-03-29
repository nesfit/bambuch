@extends('layouts.search-result')

@section('title', '| Address')
@section('searchRoute', 'address')
@section('searchValue', $searchValue)

@section('infoContent')
    <tbody>
        <tr>
            <td>
                <span class="d-table text-secondary">Owner</span>
                <span id="owner">
                    @include('components.clickable', [
                        'linkValue' => $address->owner,
                        'linkRoute' => 'owner' 
                    ])
                </span>
            </td>
            <td>
                <span class="d-table text-secondary">Currency</span>
                <span id="currency">{{ $address->currency }}</span>
            </td>
            <td>
                <span class="d-table text-secondary">Category</span>
                <span id="category">{{ $address->category }}</span>
            </td>
            <td>
                <span class="d-table text-secondary">Created</span>
                <span id="created">{{ $address->created }}</span>
            </td>
            <td>
                <span class="d-table text-secondary">Updated</span>
                <span id="updated">{{ $address->updated }}</span>
            </td>
        </tr>
    </tbody>
@endsection

@section('discoveredType', 'references')

@section('headContent')
    <tr>
        <th class="col-small">Source</th>
        <th class="col-medium">URL</th>
        <th class="col-medium">Label</th>
        <th class="col-timestamp">Created</th>
        <th class="col-timestamp">Updated</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($identities as $item)
        <tr>
            <th class="col-small">{{ $item->source }}</th>
            <td class="col-medium"><a href="{{ $item->url }}" target="_blank">{{ $item->url }}</a></td>
            <td class="col-medium">{{ $item->label }}</td>
            <td class="col-timestamp">{{ $item->created }}</td>
            <td class="col-timestamp">{{ $item->updated }}</td>
        </tr>
    @endforeach
@endsection
