<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
    
        $response=[
            "error"=>[],
            "messages"=>"success",
            "data"=>$products
        ];
        return response()->json($response);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        
        $request->validate([
            'productName'=>['required']
        ],[
            "productName.required"=>"هذا الحقل مطلوب"
        ]);


        $product=new Product();
        $product->name=$request->productName;
        $product->save();
       $product= Product::create([
            'name' => $request->productName,
            'description' => $request->deacription,
                   'category_id'=> 1,
        'slug'=>'',
        'stock'=>1,
        'price',
        'is_active',
        'attributes'
        ]);


        return response()->json([
            $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
