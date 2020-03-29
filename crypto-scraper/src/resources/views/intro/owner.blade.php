@extends('layouts.search-intro')

@section('title', '| Owners')
@section('searchRoute', 'owner')

@section('headContent')
    <tr>
        <th class="col-small">Name</th>
        <th class="col-small">Category</th>
        <th class="col-timestamp">Created</th>
        <th class="col-timestamp">Updated</th>
    </tr>
@endsection

@section('bodyContent')
    @foreach ($owners as $owner)
        <tr>
            <th class="col-small">
                @include('components.clickable', [
                    'linkValue' => $owner->name,
                    'linkRoute' => 'owner' 
                ])
            </th>
            <td class="col-small">{{ $owner->category }}</td>
            <td class="col-timestamp">{{ $owner->created }}</td>
            <td class="col-timestamp">{{ $owner->updated }}</td>
        </tr>
    @endforeach
@endsection