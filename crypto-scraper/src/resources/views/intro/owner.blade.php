@extends('layouts.search-intro')

@section('title', '| Owners')
@section('searchRoute', 'owner')

@section('headContent')
    <tr>
        <th class="col-small text-center">Name</th>
        <th class="col-small text-center">Category</th>
        <th class="col-timestamp text-center">Created</th>
        <th class="col-timestamp text-center">Updated</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($owners as $owner)
        <tr>
            <th class="col-small text-center">
                @include('components.clickable', [
                    'linkValue' => $owner->name,
                    'linkRoute' => 'owner' 
                ])
            </th>
            <td class="col-small text-center">{{ $owner->category }}</td>
            <td class="col-timestamp text-center">{{ $owner->created }}</td>
            <td class="col-timestamp text-center">{{ $owner->updated }}</td>
        </tr>
    @endforeach
@endsection