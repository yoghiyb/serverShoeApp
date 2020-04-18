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
    /**
     * fungsi membuat pesanan baru
     */
    public function create(Request $request){
        // mengubah timzone ke "Asia/Jakarta"
        date_default_timezone_set("Asia/Jakarta");

        // membuat nomor pesanan secara acak
        $order_no = rand();

        // memvalidasi semua request
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

        // jika tidak lolos validasi
        if($validator->fails()){
            // kirim pesan error
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        // tampung semua request kedalam variabel order
        $order = $request->all();
        // kondisi status pesanan
        if($order['status'] == 'Belum dikonfirmasi'){
            // jika status pesanan "Belum dikonfirmasi" maka buat nomor pesanan baru
            $order['order_no']        = $order_no;
        }else{
            // jika status pesanan tidak "Belum dikonfirmasi" maka ambil nomor pesanan lama dan ganti status berdasarkan request
            $order['order_no']        = $request->order_no;
            $order['status']            = $request->status;
        }

        // buat pesanan baru
        Order::create($order);

        // kirim pesan berhasil membuat pesanan
        return response()->json(['success' => 'berhasil membuat pesanan'], 200);
    }

    /**
     * fungsi menampilkan semua list pesanan
     */
    public function showAllLastOrder($user_id){
        $orders = Order::where('user_id',$user_id)->orderByDesc('created_at')->get();

        $collection = collect($orders);
        $unique_order = $collection->unique('order_no')->values()->all(); 
        return response()->json($unique_order, 200);
    }

    /**
     * fungsi menampilkan pesanan berdasarkan nomor pesanan
     */
    public function showByOrderNo($order_no){
        // cari pesanan berdasarkan nomor pesanan dan urutkan berdasarkan tanggal dibuat secara ascending
        $order = Order::where('order_no', $order_no)->orderBy('created_at', 'asc')->get();

        // kirim data pesanan
        return response()->json($order, 200);
    }
}
