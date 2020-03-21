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

        $validator = Validator::make($request->all(), [
            'id_partner' => 'required|numeric',
            'latitude' => 'required|string',
            'longtitude' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $location = Location::create([
            'id_partner' => $request->id_partner,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude
        ]);

        return response()->json(['sukses' => 'berhasil menyimpan lokasi'], 200);
    }

    public function show($partner_id){
        $location = Location::findOrFail($partner_id);
        return response()->json($location, 200);
    }

    public function update(Request $request, $partner_id){

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longtitude' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $location = Location::where('partner_id', $partner_id)->update([
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude
        ]);

        return response()->json(['sukse' => 'lokasi berhasil diperbarui'], 200);
    }
}
