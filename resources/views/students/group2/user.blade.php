@extends('layouts.app')

@section('title','USER HOME')
@section('content')

<h1><?php echo $name; ?></h1>
<h3>{{ $age }}</h3>
@endsection
