@extends('layouts.search-result')

@section('title', '| Address')
@section('searchRoute', 'address')

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
                @include('components.info-item', [
                    'caption' => 'Currency',
                    'value' => $address->currency
                ])
            </td>
            <td>
                @include('components.info-item', [
                    'caption' => 'Category',
                    'value' => $address->category
                ])
            </td>
            <td>
                @include('components.info-item', [
                    'caption' => 'Created',
                    'value' => $address->created
                ])
            </td>
            <td>
                @include('components.info-item', [
                    'caption' => 'Updated',
                    'value' => $address->updated
                ])
            </td>
        </tr>
    </tbody>
@endsection

@section('discoveredType', 'references')

@section('headContent')
    <tr>
        <th class="col-medium">URL</th>
        <th class="col-medium">Label</th>
        <th class="col-timestamp">Created</th>
        <th class="col-timestamp">Updated</th>
        <th class="col-small text-center">Show DOM</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($identities as $item)
        <tr class="center-line">
            <td class="col-medium">
                @include('components.link', [
                    'url' => $item->url 
                ])
            </td>
            <td class="col-medium ">{{ $item->label }}</td>
            <td class="col-timestamp">{{ $item->created }}</td>
            <td class="col-timestamp">{{ $item->updated }}</td>
            <td class="col-small text-center">
                @include('components.modal')
                @include('components.modal-button')
            </td>
        </tr>
    @endforeach
@endsection
