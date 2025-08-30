@extends('layouts.dashboard')

@section("title","Products")
@section('contents')
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>


            <div class="row">
                <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">

                            <h5 class="card-title mb-0">Latest Projects</h5>

                            <a href="{{route('products.create')}}" class="btn btn-success" >create</a>
                        </div>
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th class="d-none d-xl-table-cell">Name</th>
                                    <th class="d-none d-xl-table-cell">description</th>
                                    <th>Status</th>
                                    <th class="d-none d-md-table-cell" colspan="2" rowspan="2">actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->product_id }}</td>
                                        <td class="d-none d-xl-table-cell">{{ $product->name }}</td>
                                        <td class="d-none d-xl-table-cell">{{ $product->description }}</td>
                                        <td><span class="badge bg-success">{{ $product->status }}</span></td>
                                        <td class="d-none d-md-table-cell"><a href=""><button class="btn btn-danger">edit</button></a></td>
                                        <td class="d-none d-md-table-cell"><a href=""><button class="btn btn-danger">delete</button></a></td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </main>
@endsection
