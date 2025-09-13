@extends('layouts.dashboard')
@section('title', 'Create New Products')
@section('contents')
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3">Product Create</h1>

            <div class="row">

                <!-- hhh -->
                <div class="col-md-12 col-xl-12">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="account" role="tabpanel">

                            <div class="card">
                                <div class="card-header">

                                    <h5 class="card-title mb-0">Public info</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label" for="productName">product Name</label>
                                                    <input type="text" name="productName" class="form-control"
                                                        id="productName" placeholder="productName">
                                                </div>
                                                <input type="file" name="image" id="">
                                                <x-input-error :messages="$errors->get('productName')" class="mt-2" />

                                                <div class="mb-3">
                                                    <label class="form-label" for="inputUsername">description</label>
                                                    <textarea rows="2" name="deacription" class="form-control" id="inputBio"
                                                        placeholder="Tell something about product"></textarea>
                                                </div>
                                            </div>

                                        </div>

                                        <button type="submit" class="btn btn-primary">Save </button>
                                    </form>

                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection
