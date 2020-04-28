<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Order;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * fungsi membuat pesanan baru
     */
    public function create(Request $request)
    {
        // mengubah timzone ke "Asia/Jakarta"
        date_default_timezone_set("Asia/Jakarta");

        // membuat nomor pesanan secara acak
        $order_no = rand(1, 1000000000);

        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'partner_id' => 'required|numeric',
            'service_id' => 'required|numeric',
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:100',
            'phone_number' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'price' => 'required|numeric',
            'status' => 'required|string'
        ]);

        // jika tidak lolos validasi
        if ($validator->fails()) {
            // kirim pesan error
            return response()->json($validator->errors()->toJson());
        }

        // jika lolos validasi
        // tampung semua request kedalam variabel order
        $order = $request->all();
        // kondisi status pesanan
        if ($order['status'] == 'Belum dikonfirmasi') {
            // jika status pesanan "Belum dikonfirmasi" maka buat nomor pesanan baru
            $order['order_no']        = $order_no;
        } else {
            // jika status pesanan bukan "Belum dikonfirmasi" maka ambil nomor pesanan lama dan ganti status berdasarkan request
            $order['order_no']        = $request->order_no;
            $order['status']          = $request->status;
        }

        try {
            // buat pesanan baru
            Order::create($order);
        } catch (\Throwable $th) {
            // jika gagal kirim pesan error
            return response()->json($th);
        }

        // kirim pesan berhasil membuat pesanan
        return response()->json(['status' => 'berhasil membuat pesanan'], 200);
        // return response()->json(['status' => $order], 200);
    }

    /**
     * fungsi menampilakn data pesanan untuk akun partner
     */
    public function orderListPartner($partner_id)
    {

        // cari data pesanan bedasarakan partner_id dan urutkan secara descending berdasarkan kolom created_at
        $orders = Order::where('partner_id', $partner_id)->orderByDesc('created_at')->get();

        try {
            // looping data
            $collection = collect($orders)->map(function ($data) {
                // panggil relasi user
                $data->user;

                // panggil relasi jasa
                $data->service;

                return $data;
            });

            // mengambil data unik dari variabel collection berdasarkan order_no
            $unique_order = $collection->unique('order_no')->values()->all();
        } catch (\Throwable $th) {
            // jika gagal kirim pesan error
            return response()->json($th);
        }

        // kirim respon 
        return response()->json($unique_order, 200);
    }

    /**
     * fungsi menampilkan semua list pesanan
     */
    public function showAllLastOrder($user_id)
    {
        // mencari data order berdasarkan user_id dan diururtkan secara descending berdasarkan kolom created_at
        $orders = Order::where('user_id', $user_id)->orderByDesc('created_at')->get();

        try {
            // looping data order 
            $collection = collect($orders)->map(function ($data) {

                // memangil relasi service
                $data->service;

                // memanggil relasi partner
                $data->service->partner;

                // inisiasi untuk url gambar
                $data->imgUrl = null;

                // kondisi jika alamat gambar tidak kosong
                if ($data->service->partner->store_image != null) {
                    // inisiasi alamat gambar
                    $imgUrl = Storage::url($data->service->partner->store_image);

                    // masukkan alamat gambar kedalam variabel imgUrl agar tidak null
                    $data->imgUrl = $imgUrl;
                };

                // kembalikan data ke variabel collection
                return $data;
            });

            // mengambil data unik dari variabel collection berdasarkan order_no
            $unique_order = $collection->unique('order_no')->values()->all();
        } catch (\Throwable $th) {
            // jika gagal kirim pesan error
            return response()->json($th);
        }

        // kirim respon 
        return response()->json($unique_order, 200);
    }

    /**
     * fungsi menampilkan pesanan berdasarkan nomor pesanan
     */
    public function showByOrderNo($order_no)
    {

        try {
            // cari pesanan berdasarkan nomor pesanan dan urutkan berdasarkan tanggal dibuat secara ascending
            $order = Order::where('order_no', $order_no)->orderBy('created_at', 'asc')->get();

            // looping data order
            $collection = collect($order)->map(function ($data) {
                // memanggil relasi service
                $data->service;

                // memanggil relasi user
                $data->user;

                //kembalikan data ke variabel collection
                return $data;
            });
        } catch (\Throwable $th) {
            return response()->json($th, 400);
        }

        // kirim data pesanan
        return response()->json($collection, 200);
    }
}
