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
            'id_partner' => 'required|numeric',
            'latitude' => 'required|string',
            'longtitude' => 'required|string'
        ]);

        // jika tidak lolos validasi
        if($validator->fails()){
            // kirim pesan error
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        // buat data lokasi baru
        $location = Location::create([
            'id_partner' => $request->id_partner,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude
        ]);

        // kirim pesa sukses
        return response()->json(['sukses' => 'berhasil menyimpan lokasi'], 200);
    }

    /**
     * fungsi untuk menampilkan data lokasi berdasarkan partner id
     */
    public function show($partner_id){
        // cari lokasi berdasarkan partner id
        $location = Location::findOrFail($partner_id);

        // kirim data lokasi
        return response()->json($location, 200);
    }

    /**
     * fungsi update lokasi berdasarkan partner id
     */
    public function update(Request $request, $partner_id){
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longtitude' => 'required|string'
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
            'longtitude' => $request->longtitude
        ]);

        // kirim pesan sukses
        return response()->json(['sukse' => 'lokasi berhasil diperbarui'], 200);
    }
}
