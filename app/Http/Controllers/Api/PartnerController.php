<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Partner;
use App\Order;

use Config;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class PartnerController extends Controller
{


    /**
     * fungsi untuk ambil semua data partner
     */
    public function partners()
    {
        // ambil semua data partner
        $partners = Partner::All();

        // kirim semua data partner
        return response()->json($partners, 200);
    }


    /**
     * fungsi untuk daftar menjadi partner
     */
    public function register(Request $request)
    {
        // memvalidasi semua request
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
        

        // jika tidak lolos validasi maka kirim pesan error
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        // buat data partner baru
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

        // membuat token untuk hak akses
        $token = JWTAuth::fromUser($partner);

        // kirim data user dan token yang telah dibuat
        return response()->json(compact('partner','token'), 201);
    }

    public function login(Request $request){
        // ambil request email dan password
        $credentials = $request->only('email', 'password');

        // eksekusi
        try {
            // kondisi pencocokan data
            if (! $token = Auth::guard('partner')->attempt($credentials)) {
                // kirim respon error
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            // kirim respon error
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // jika lolos kondisi pencocokan data maka cari data tersebut berdasarkan email
        $partner = Partner::where('email', $request->email)->first();

        // kirim data partner
        return $this->responseWithToken($token, $partner);

        // return response()->json(["status" => "oke jancok!"]);
    }

    /**
     * fungsi respon data login
     */
    public function responseWithToken($token, $partner){
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('partner')->factory()->getTTL() * 60,
            'user' => $partner
        ]);
    }
    
    /**
     * fungsi untuk menampilkan 1 data partner
     */
    public function show($id){
        // mencari data partner berdasarkan id
        $partner = Partner::findOrFail($id);

        // tambahkan data lokasi berdasarkan id partner
        $partner->location;

        // tambahkan data jasa/layanan berdasarkan id partner
        $partner->service;

        // kirim data partner
        return response()->json(compact('partner'),200);
    }

    /**
     * fungsi untuk menangani gambar
     */
    public function handleImage(Request $request,$id){

        // tampung request(string) kedalam variabel
        $image = $request->image;

        // membagi string base64 menjadi 2 array berdasarkan koma
        $image = explode(",",$image);

        // mencari format gambar dari string base64
        $format = explode("/",$image[0]);
        $fixFormat = explode(";",$format[1]);

        // mencari data partner berdasarkan id
        $partner = Partner::findOrFail($id);

        //eksekusi
        try {
            // membuat nama acak untuk gambar
            $imageName = bin2hex(random_bytes(10)).".".$fixFormat[0];

            // membuat path untuk gambar
            $newPath = storage_path().'/'.$imageName;
            
            // cek kondisi apakah partner sudah memiliki gambar toko atau belum
            if($partner->store_image == null) {

                // Jika belum maka buat gambar baru
                \File::put($newPath,base64_decode($image[1]));

                // update data partner
                Partner::where('id', $id)->update([
                    'store_image' => $imageName
                ]);
            }else{
                // Jika sudah maka hapus gambar lama dan buat gambar baru

                // ambil data gambar lama
                $oldImage = $partner->store_image;

                // mendefinisikan path gambar lama
                $oldPath = storage_path().'/'.$oldImage;

                // hapus gambar lama sesuai dengan path gambar lama
                unlink($oldPath);

                // buat gambar baru
                \File::put($newPath,base64_decode($image[1]));
                
                // update data partner
                Partner::where('id', $id)->update([
                    'store_image' => $imageName
                ]);
            }
        } catch (\Throwable $th) {
            // jika gagal kirim pesan gagal
            return response()->json(['error'=> 'gagal']);
        }

        // kirim respon jika data gambar berhasil disimpan
        $response = ['imageUrl' => Storage::url($imageName)];
        return response()->json($response, 200);
    }


    /**
     * fungsi untuk memberikan url gambar
     */
    public function showImage($id){
        // mencari data partner berdasarkan id
        $partner = Partner::findOrFail($id);

        // mendefinisikan path gambar pertamakali
        $imgPath = null;

        // cek jika ada data gambar maka isi variabel imgPath dengan url gambar
        if ($partner->store_image != null) $imgPath = Storage::url($partner->store_image);

        // kirim respon
        return response()->json(['imageUrl' => $imgPath], 200);
    }


    /**
     * fungsi untuk update data partner
     */
    public function update(Request $request, $id)
    {
        // memvalidasi semua request
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

        // jika tidak lolos validasi maka kirim pesan error
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // jika lolos validasi
        // update data partner
        $updatePartner = Partner::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'start_working_time' => $request->start_working_time ,
            'end_working_time' => $request->end_working_time,
            'start_working_days' => $request->start_working_days,
            'end_working_days' => $request->end_working_days,
            'phone_number' => $request->phone_number,
        ]);

        // kirim data partner bahwa data erhasil di perbarui
        return response()->json(compact('updatePartner'), 200);
    }

    /**
     * fungsi untuk update data toko partner
     */
    public function updateShop(Request $request, $id){
        // memvalidasi semua request
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:50',
            'address' => 'required|string|max:100',
            'start_working_time' => 'required|string',
            'end_working_time' => 'required|string',
            'start_working_days' => 'required|string',
            'end_working_days' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        // jika tidak lolos validasi maka kirim pesan error
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // eksekusi jika lolos validasi
        try {
            // tampung semua request kedalam variabel updateShop
            $updateShop = $request->all();

            // cari data partner berdasarkan id dan perbarui data
            Partner::where('id', $id)->update($updateShop);
        } catch (\Throwable $th) {
            // jika error kirim pesan error
            return response()->json(['error' => $th]);
        }

        // kirim pesan success jika data berhasil di perbarui
        return response()->json(['success' => 'berhasil update toko'], 200);
    }
}
