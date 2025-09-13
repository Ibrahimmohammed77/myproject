<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function boom()
    {
        throw new \Exception('Boom!');
    }
}
