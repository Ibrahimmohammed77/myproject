<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

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
            'description' => $request->deacription
        ]);

        return redirect()->route('products.index');
    }
}
