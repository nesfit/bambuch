@extends('layouts.search-result')

@section('title', '| Source')
@section('searchRoute', 'source')

@section('infoContent')
    <tbody>
        <tr>
            <td>
                @include('components.info-item', [
                    'caption' => 'URL',
                    'value' => $source->url
                ])
            </td>
        </tr>
    </tbody>
@endsection

@section('discoveredType', 'addresses')

@section('headContent')
    <tr>
        <th class="col-medium">Address</th>
        <th class="col-small">Category</th>
        <th class="col-small">Currency</th>
        <th class="col-timestamp">Created</th>
        <th class="col-timestamp">Updated</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($addresses as $address)
        <tr>
            <th class="col-medium">
                @include('components.clickable', [
                    'linkValue' => $address->address,
                    'linkRoute' => 'address' 
                ])
            </th>
            <td class="col-small">{{ $address->category }}</td>
            <td class="col-small">{{ $address->currency }}</td>
            <td class="col-timestamp">{{ $address->created }}</td>
            <td class="col-timestamp">{{ $address->updated }}</td>
        </tr>
    @endforeach
@endsection
