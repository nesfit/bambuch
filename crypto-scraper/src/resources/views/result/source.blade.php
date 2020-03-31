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
        <th class="col-medium text-center">Address</th>
        <th class="col-small text-center">Category</th>
        <th class="col-small text-center">Currency</th>
        <th class="col-timestamp text-center">Created</th>
        <th class="col-timestamp text-center">Updated</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($addresses as $address)
        <tr>
            <th class="col-medium text-center">
                @include('components.clickable', [
                    'linkValue' => $address->address,
                    'linkRoute' => 'address' 
                ])
            </th>
            <td class="col-small text-center">{{ $address->category }}</td>
            <td class="col-small text-center">{{ $address->currency }}</td>
            <td class="col-timestamp text-center">{{ $address->created }}</td>
            <td class="col-timestamp text-center">{{ $address->updated }}</td>
        </tr>
    @endforeach
@endsection
