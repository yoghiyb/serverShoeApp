<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    /**
     * fungsi login user
     */
    public function login(Request $request)
    {
        // ambil request email dan password
        $credentials = $request->only('email', 'password');

        // eksekusi
        try {
            // kondisi pencocokan data
            if (! $token = JWTAuth::attempt($credentials)) {
                // kirim respon error
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            // kirim respon error
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // jika lolos kondisi pencocokan data maka cari data user berdasarkan email
        $user = User::where('email', $request->get('email'))->first();

        // kirim data user dan token
        return response()->json(compact('token', 'user'),200);
    }

    /**
     * fungsi pendaftaran user baru
     */
    public function register(Request $request)
    {
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // jika tidak lolos validasi
        if($validator->fails()){
            // kirim pesan error
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        // buat data user baru
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        // buat token untuk hak akses user
        $token = JWTAuth::fromUser($user);

        // kirim data user dan token
        return response()->json(compact('user','token'),200);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
}
