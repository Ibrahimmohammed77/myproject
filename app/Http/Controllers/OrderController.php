<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
   public function index() {}

   public function store(Request $request)
   {
      $order = new  Order();
      $order->products()->updateExistingPivot(1,[]);
   }
}
