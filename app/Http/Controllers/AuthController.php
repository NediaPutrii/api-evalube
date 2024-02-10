<?php

namespace App\Http\Controllers;

use App\Models\User;
// use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;




use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\Firebase;


class AuthController extends Controller
{
    public function register(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:users',
            'telp' => 'required',
            'email' => 'required|unique:users|email',
            'address' => 'required',
            'password' => 'required|min:6'
        ]);
        $name = $request->input('name');
        $telp = $request->input('telp');
        $email = $request->input('email');
        $address = $request->input('address');
        $password = Hash::make($request->input('password'));

        $user = User::create([
            'name' => $name,
            'telp' => $telp,
            'email' => $email,
            'address' => $address,
            'password' => $password
            
        ]);

        return response()->json(['message' => 'Pendaftaran pengguna berhasil dilaksanakan',
        'data' => $user]);
        // return response()->json(['data' => $user]);

    }

    // public function login(Request $request)
    // {
    //     $this->validate($request, [
    //         'email' => 'required|email',
    //         'password' => 'required|min:6'
    //     ]);

    //     $email = $request->input('email');
    //     $password = $request->input('password');

    //     $user = User::where('email', $email)->first();
    //     if (!$user) {
    //         return response()->json(['message' => 'Login failed'], 401);
    //     }

    //     $isValidPassword = Hash::check($password, $user->password);
    //     if (!$isValidPassword) {
    //         return response()->json(['message' => 'Login failed'], 401);
    //     }

    //     $credentials = $request->only(['email', 'password']);

    //     $token = Auth::attempt($credentials);

    //     if ($token) {
    //         //berhasil login, kirim notifikasi
    //         // $this->sendNotification();
    //         if ($request->filled('device_id')) {
    //             Firebase::unSubscribeAllTopic(auth('web')->user()->fcm_token);
    //             Firebase::unSubscribeAllTopicByDeviceId($request->device_id);
    //             Firebase::updateDeviceId(auth('web')->user()->fcm_token, $request->device_id);

    //             if (auth('api')->user()->user) {
    //                 $classrooms = User::where('nim', auth('api')->user()->nim)
    //                     // ->select('classroom_id')
    //                     ->get();

    //                 // foreach ($classrooms as $classroom) {
    //                 //     Firebase::subscribeTopic($classroom->classroom_id, $request->device_id);
    //                 // }
    //             }
    //         }
    //     }

    //     if(!$token){
    //         return response()->json(['message' => 'Unauthorized'], 401);
           
    //     }

    //     // $generateToken = bin2hex(random_bytes(40));
    //     $user->update([
    //         'token' => $token
    //     ]);
    //     // return response()->json($user);
    //     return response()->json([
    //         'data' => $user,
    //         'token_type' => 'bearer'
    //         // 'expires_in' => Auth::factory()->getTTL() * 60
    //     ],200);
    
        
    // }

//     public function login(Request $request)
// {
//     // Validasi input
//     $this->validate($request, [
//         'email' => 'required|email',
//         'password' => 'required|min:6'
//     ]);

//     // Ambil kredensial dari request
//     $credentials = $request->only(['email', 'password']);

//     // Lakukan otentikasi pengguna
//     if (Auth::attempt($credentials)) {
//         // Otentikasi berhasil, dapatkan objek pengguna yang diautentikasi
//         $user = Auth::user();

//         // Buat token OAuth menggunakan Laravel Passport
//         $token = $user->createToken('Personal Access Token')->accessToken;

//         // Lakukan tindakan lain setelah otentikasi berhasil (jika ada)
//         // ...

//         // Kembalikan respons dengan token dan data pengguna
//         return response()->json([
//             'user' => $user,
//             'access_token' => $token,
//             'token_type' => 'Bearer',
//         ], 200);
//     } else {
//         // Otentikasi gagal
//         return response()->json(['message' => 'Login failed'], 401);
//     }
// }

public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Login failed'], 401);
        }

        $isValidPassword = Hash::check($password, $user->password);
        if (!$isValidPassword) {
            return response()->json(['message' => 'Login failed'], 401);
        }

        $credentials = $request->only(['email', 'password']);

        $token = Auth::attempt($credentials);

        if ($token) {
            //berhasil login, kirim notifikasi
            // $this->sendNotification();
            if ($request->filled('device_id')) {
                Firebase::unSubscribeAllTopic(auth('web')->user()->fcm_token);
                Firebase::unSubscribeAllTopicByDeviceId($request->device_id);
                Firebase::updateDeviceId(auth('web')->user()->fcm_token, $request->device_id);

                if (auth('api')->user()->user) {
                    $classrooms = User::where('nim', auth('api')->user()->nim)
                        // ->select('classroom_id')
                        ->get();

                    // foreach ($classrooms as $classroom) {
                    //     Firebase::subscribeTopic($classroom->classroom_id, $request->device_id);
                    // }
                }
            }
        }

        if(!$token){
            return response()->json(['message' => 'Unauthorized'], 401);
           
        }

        $generateToken = bin2hex(random_bytes(40));
        $user->update([
            'token' => $generateToken
        ]);
        // return response()->json($user);
        return response()->json([
            'data' => $user,
            'token_type' => 'bearer',
            // 'expires_in' => Auth::factory()->getTTL() * 60
        ],200);
    
        
    }

    // public function logout(){
    //     $user = \Auth::user();
    //     $user->token = null;
    //     $user->save();

    //     return response()->json(['message' => 'Pengguna telah logout']);
    // }

    public function logout()
    {
        auth()->guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // public function sendNotification(){

    //     $curl = curl_init();

    //     curl_setopt_array($curl, array(
    //       CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
    //       CURLOPT_RETURNTRANSFER => true,
    //       CURLOPT_ENCODING => '',
    //       CURLOPT_MAXREDIRS => 10,
    //       CURLOPT_TIMEOUT => 0,
    //       CURLOPT_FOLLOWLOCATION => true,
    //       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //       CURLOPT_CUSTOMREQUEST => 'POST',
    //       CURLOPT_POSTFIELDS =>'{
    //         "to" : "/topics/pengumuman",
    //         "notification" :{
    //             "title" : "Hai Maniez", 
    //             "body" : "Ada travel baru loh"
    //         }
    //     }',
    //       CURLOPT_HTTPHEADER => array(
    //         'Authorization: key=AAAAiAVppSo:APA91bGc9cz6NXrZpotwqwSCMDf6n-yk4wrZmJFfQCwMZI83vMUzQji4sXFANniuEmfLxZlb--uAXQ6mKocICs3BColCOGkbvZ2g6sJU_G-9JtdXITXuEyGpnlvIs0HWcEpFLD7nYRZo',
    //         'Content-Type: application/json'
    //       ),
    //     ));
        
    //     $response = curl_exec($curl);
        
    //     curl_close($curl);
      
        
    // }
    
}
