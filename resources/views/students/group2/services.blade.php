@extends('layouts.app')
@section('title',content: 'service')
@section("content")
  <div class="content">

        @foreach ($services as $service )
        <h3>{{$service}}</h3>
        @endforeach
  
    </div>
@endsection