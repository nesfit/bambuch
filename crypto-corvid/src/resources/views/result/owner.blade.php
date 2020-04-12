@extends('layouts.search-result')

@section('title', '| Owner')
@section('searchRoute', 'owner')

@section('infoContent')
    <tr>
        <td>
            @include('components.info-item', [
                'caption' => 'Category',
                'value' => $owner->category
            ])
        </td>
    </tr>
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
    @foreach ($addresses as $item)
        <tr>
            <th class="col-medium text-center">
                @include('components.clickable', [
                    'linkValue' => $item->address,
                    'linkRoute' => 'address' 
                ])
            </th>
            <td class="col-small text-center">{{ $item->category }}</td>
            <td class="col-small text-center">{{ $item->currency }}</td>
            <td class="col-timestamp text-center">{{ $item->created }}</td>
            <td class="col-timestamp text-center">{{ $item->updated }}</td>
        </tr>
    @endforeach
@endsection
