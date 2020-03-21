<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Partner;
use App\Order;

use Config;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class PartnerController extends Controller
{

    // public function __construct(){
    //     Config::set('jwt.users', Partner::class);
    //     Config::set('auth.providers', ['users' => [
    //         'driver' => 'eloquent',
    //         'model' => Partner::class,
    //     ]]);
    // }


    /**
     * fungsi untuk ambil semua data partner
     */
    public function partners()
    {
        $partners = Partner::All();
        return response()->json($partners, 200);
    }


    /**
     * fungsi untuk daftar menjadi partner
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:partners',
            'address' => 'required|string|max:100',
            'start_working_time' => 'required|string',
            'end_working_time' => 'required|string',
            'start_working_days' => 'required|string',
            'end_working_days' => 'required|string',
            'phone_number' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $partner = Partner::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'start_working_time' => $request->start_working_time,
            'end_working_time' => $request->end_working_time,
            'start_working_days' => $request->start_working_days,
            'end_working_days' => $request->end_working_days,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($partner);

        return response()->json(compact('partner','token'), 201);
    }

    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        try {
            // if (! $token = JWTAuth::attempt($credentials)) {

            if (! $token = Auth::guard('partner')->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return $this->responseWithToken($token);

        // return response()->json(["status" => "oke jancok!"]);
    }

    public function responseWithToken($token){
        return response()->json([
            'access' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('partner')->factory()->getTTL() * 60
        ]);
    }
    /**
     * fungsi untuk menampilkan 1 data partner
     */
    public function show($id){
        $partner = Partner::findOrFail($id);
        $partner->location;

        return response()->json(compact('partner'),200);
    }


    /**
     * fungsi untuk update data partner
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:partners',
            'address' => 'required|string|max:100',
            'start_working_time' => 'required|string',
            'end_working_time' => 'required|string',
            'start_working_days' => 'required|string',
            'end_working_days' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $updatePartner = Partner::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'start_working_time' => $request->start_working_time ,
            'end_working_time' => $request->end_working_time,
            'start_working_days' => $request->start_working_days,
            'end_working_days' => $request->end_working_days,
            'phone_number' => 'required|string',
        ]);

        return response()->json(compact('updatePartner'), 200);
    }
}
