@extends('layouts.main')
  
@section('body')
    <div class="container d-flex">
        <div class="col-12 align-self-center text-center">
            <h3>Here you can schedule scraping jobs.</h3>
        </div>
    </div>
    
    {{-- result data --}}
    <div class="container-lg">

        {{-- table desctiption --}}    
        <p class="text-secondary mb-2 ml-2">Bitcointalk jobs</p>

        {{-- fixed result head --}}
        <table class="table table-light hidden-overflow-table">
            <thead>
                <tr>
                    <th class="col-medium">Name</th>
                    <th class="col-large">Description</th>
                    <th class="col-small text-center">Frequency</th>
                    <th class="col-small text-center">Starting</th>
                    <th class="col-small text-center">Submit</th>
                </tr>
            </thead>
        </table>

        {{-- scrollable result body --}}
        <div class="table-limit-50">
            @each('components.task', $tasks, 'task')
        </div>
    </div>
@endsection
