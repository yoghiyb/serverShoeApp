<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ShoeController extends Controller
{
    public function shoe() {
        $data = "Data all Shoes";
        return response()->json($data, 200);
    }

    public function shoeAuth() {
        $data = "Welcome ". Auth::user()->name;
        return response()->json($data, 200);
    }

}
