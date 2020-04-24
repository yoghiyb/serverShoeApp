<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Location;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{

    /**
     * fungsi menambah lokasi 
     */
    public function create(Request $request){
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|numeric',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        // jika tidak lolos validasi
        if($validator->fails()){
            // kirim pesan error
            return response()->json($validator->errors()->toJson());
        }

        // jika lolos validasi
        try {
            // buat data lokasi baru
            $location = Location::create([
                'partner_id' => $request->partner_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th, 'data' => $request->all()]);
        }

        // kirim pesa sukses
        return response()->json(['sukses' => 'berhasil menyimpan lokasi'], 200);
    }

    /**
     * fungsi untuk menampilkan data lokasi berdasarkan partner id
     */
    public function show($partner_id){

        try {
            // cari lokasi berdasarkan partner id
        $location = Location::where('partner_id', $partner_id)->first();
        } catch (\Throwable $th) {
            return response()->json('gagal');
        }

        // kirim data lokasi
        return response()->json($location, 200);
    }

    /**
     * fungsi update lokasi berdasarkan partner id
     */
    public function update(Request $request, $partner_id){
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        // jika tidak lolos validasi
        if($validator->fails()){
            // kirim pesan error
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        // update data lokasi berdasarkan partner id
        $location = Location::where('partner_id', $partner_id)->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        // kirim pesan sukses
        return response()->json(['sukses' => 'lokasi berhasil diperbarui'], 200);
    }
}
