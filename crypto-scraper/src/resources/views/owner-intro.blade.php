@extends('layouts.search-intro')

@section('title', 'Owners')
@section('searchRoute', 'owner')

@section('resultTable')
    <table class="table table-light hidden-overflow-table">
        <thead>
        <tr>
            <th class="col-small">Name</th>
            <th class="col-small">Category</th>
            <th class="col-timestamp">Created</th>
            <th class="col-timestamp">Updated</th>
        </tr>
        </thead>
    </table>
    <div class="table-limit">
        <table class="table table-light hidden-overflow-table">
            <tbody>
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
            </tbody>
        </table>
    </div>
@endsection