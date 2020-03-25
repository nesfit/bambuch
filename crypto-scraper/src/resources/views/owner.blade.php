@extends('layouts.main')

@section('title', 'Owner')
@section('searchRoute', 'owner')
@section('searchValue', $searchValue)

@section('info')
    <table class="table table-light hidden-overflow-table">
        <tbody>
            <tr>
                <td>
                    <span class="d-table text-secondary">Category</span>
                    <span id="owner">{{ $owner->category }}</span>
            </tr>
        </tbody> 
    </table>
@endsection

@section('resultTableInfo')
    <span class="text-secondary">Table of discovered addresses</span>
@endsection

@section('resultTable')
    <table class="table table-light hidden-overflow-table">
        <thead>
        <tr>
            <th class="col-medium">Address</th>
            <th class="col-small">Category</th>
            <th class="col-small">Currency</th>
            <th class="col-timestamp">Created</th>
            <th class="col-timestamp">Updated</th>
        </tr>
        </thead>
    </table>
    <div class="table-limit">
        <table class="table table-light hidden-overflow-table">
            <tbody>
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
            </tbody>
        </table>
    </div>
@endsection
