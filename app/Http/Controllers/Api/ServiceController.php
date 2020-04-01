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
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|numeric',
            'service' => 'required|string',
            'unit' => 'required|string',
            'price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $service = $request->all();
        Service::create($service);

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
            $service = Service::where('partner_id', $partner_id)->get();
        } catch (\Throwable $th) {
            return response()->json('tidak dapat menemukan jasa', 500);
        }

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
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|numeric',
            'service' => 'required|string',
            'unit' => 'required|string',
            'price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $update = $request->all();
            Service::where('id', $id)->update($update);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }

        return response()->json('berhasil update jasa', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        
    }
}
