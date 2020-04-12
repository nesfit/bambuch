@extends('layouts.search-intro')

@section('title', '| Sources')
@section('searchRoute', 'source')

@section('headContent')
    <tr>
        <th class="col-medium">Name</th>
        <th class="col-medium">Url</th>
    </tr>
@endsection
        
@section('bodyContent')
    @foreach ($sources as $source)
        <tr>
            <th class="col-medium">
                @include('components.clickable', [
                    'linkValue' => $source->name,
                    'linkRoute' => 'source' 
                ])
            </th> 
            <td class="col-medium">
                @include('components.link', [
                    'url' => $source->url 
                ])
            </td>
        </tr>
    @endforeach
@endsection