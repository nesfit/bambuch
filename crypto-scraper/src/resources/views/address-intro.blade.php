@extends('layouts.search-intro')

@section('title', 'Addresses')
@section('searchRoute', 'address')

@section('headContent')
    <tr>
        <th class="col-small">Owner</th>
        <th class="col-medium">Address</th>
        <th class="col-small">Currency</th>
        <th class="col-small">Category</th>
        <th class="col-timestamp">Created</th>
        <th class="col-timestamp">Updated</th>
    </tr>
@endsection
        
@section('bodyContent')
    @foreach ($addresses as $address)
        <tr>
            <th class="col-small">
                @include('components.clickable', [
                    'linkValue' => $address->owner,
                    'linkRoute' => 'owner' 
                ])
            </th> 
            <td class="col-medium">
                @include('components.clickable', [
                    'linkValue' => $address->address,
                    'linkRoute' => 'address' 
                ])
            </td>
            <td class="col-small">{{ $address->currency }}</td>
            <td class="col-small">{{ $address->category }}</td>
            <td class="col-timestamp">{{ $address->created }}</td>
            <td class="col-timestamp">{{ $address->updated }}</td>
        </tr>
    @endforeach
@endsection