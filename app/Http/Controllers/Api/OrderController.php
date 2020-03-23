<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Order;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function create(Request $request){
        date_default_timezone_set("Asia/Jakarta");
        $order_no = rand();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'partner_id' => 'required|numeric',
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:100',
            'phone_number' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'price' => 'required|numeric',
            'status' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $order = $request->all();
        if($order['status'] == 'Belum dikonfirmasi'){
            $order['order_no']        = $order_no;
        }else{
            $order['order_no']        = $request->order_no;
            $order['status']            = $request->status;
        }

        Order::create($order);

        return response()->json(['success' => 'berhasil membuat pesanan'], 200);
    }

    public function showAllLastOrder($user_id){
        $orders = Order::where('user_id',$user_id)->orderByDesc('created_at')->get();

        $collection = collect($orders);
        $unique_order = $collection->unique('order_no')->values()->all(); 
        return response()->json($unique_order, 200);
    }

    public function showByOrderNo($order_no){
        $order = Order::where('order_no', $order_no)->orderBy('created_at', 'asc')->get();

        return response()->json($order, 200);
    }
}
