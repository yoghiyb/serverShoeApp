<?php

namespace App\Http\Controllers\Api;

use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|numeric',
            'service' => 'required|string',
            'unit' => 'required|string',
            'price' => 'required|numeric'
        ]);

        // jika tidak lolos validasi maka kirim pesan error
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        try {
            // buat jasa baru
            $service = $request->all();
            Service::create($service);
        } catch (\Throwable $th) {
            // jika terjadi kesalahan saat membuat jasa baru
            return response()->json(['error' => $th]);
        }

        // kirim pesan berhasil
        return response()->json('berhasil membuat jasa', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($partner_id)
    {
        try {
            // cari data jasa berdasarkan partner_id
            $service = Service::where('partner_id', $partner_id)->get();
        } catch (\Throwable $th) {
            // jika gagal kirim pesan erro
            return response()->json('tidak dapat menemukan jasa', 500);
        }

        // kirim data jasa
        return response()->json($service, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service, $id)
    {
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|numeric',
            'service' => 'required|string',
            'unit' => 'required|string',
            'price' => 'required|numeric'
        ]);

        // jika tidak lolos validasi maka kirim pesan error
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            // tampung semua request kedalam variabel update
            $update = $request->all();

            // update data jasa berdasarkan id
            Service::where('id', $id)->update($update);
        } catch (\Throwable $th) {
            // jika gagal kirim pesan error
            return response()->json($th, 500);
        }

        // kirim pesan berhasil update jasa
        return response()->json('berhasil update jasa', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // cari data jasa berdasarkan id dan hapus
            $service = Service::findOrFail($id)->delete();
        } catch (\Throwable $th) {
            // jika gagal kirim pesan error
            return response()->json(['error' => $th]);
        }

        // kirim pesan jika berhasil
        return response()->json(['success' => 'berhasil menghapus jasa'], 200);
    }
}
