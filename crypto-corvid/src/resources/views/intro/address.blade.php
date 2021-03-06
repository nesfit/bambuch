@extends('layouts.search-intro')

@section('title', '| Addresses')
@section('searchRoute', 'address')

@section('headContent')
    <tr>
        <th class="col-small text-center">Owner</th>
        <th class="col-large text-center">Address</th>
        <th class="col-small text-center">Currency</th>
        <th class="col-small text-center">Category</th>
        <th class="col-timestamp text-center">Created</th>
        <th class="col-timestamp text-center">Updated</th>
    </tr>
@endsection
        
@section('bodyContent')
    @foreach ($addresses as $address)
        <tr>
            <th class="col-small text-center">
                @include('components.clickable', [
                    'linkValue' => $address->owner,
                    'linkRoute' => 'owner' 
                ])
            </th> 
            <td class="col-large text-center">
                @include('components.clickable', [
                    'linkValue' => $address->address,
                    'linkRoute' => 'address' 
                ])
            </td>
            <td class="col-small text-center">{{ $address->currency }}</td>
            <td class="col-small text-center">{{ $address->category }}</td>
            <td class="col-timestamp text-center">{{ $address->created }}</td>
            <td class="col-timestamp text-center">{{ $address->updated }}</td>
        </tr>
    @endforeach
@endsection