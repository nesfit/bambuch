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
        <th class="col-medium">Address</th>
        <th class="col-small">Category</th>
        <th class="col-small">Currency</th>
        <th class="col-timestamp">Created</th>
        <th class="col-timestamp">Updated</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($addresses as $item)
        <tr>
            <th class="col-medium">
                @include('components.clickable', [
                    'linkValue' => $item->address,
                    'linkRoute' => 'address' 
                ])
            </th>
            <td class="col-small">{{ $item->category }}</td>
            <td class="col-small">{{ $item->currency }}</td>
            <td class="col-timestamp">{{ $item->created }}</td>
            <td class="col-timestamp">{{ $item->updated }}</td>
        </tr>
    @endforeach
@endsection
