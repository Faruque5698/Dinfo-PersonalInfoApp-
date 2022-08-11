<?php

namespace App\Http\Controllers\Api;

use App\Helper\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function send_otp_register(Request $request){
        $request->validate([
            'mobile'=>'required',
//            'signature'=>'required',
        ]);
        $data = User::where('mobile','=',$request->mobile)->first();
        if ($data){
            return response()->json([
                'message'=>'This mobile number  all ready used'
            ],409);
        }

        $user = Otp::where('mobile','=',$request->mobile)->first();
        if ($user){
            $user->delete();
        }

        $mobile = $request->mobile;
        $DOMAIN = env('DOMAIN');
        $SID = env('SID');
        $API_TOKEN  = env('API_TOKEN');
//        $bookData = $request->all();

//            return response($value['mobile']);

//        return response($bookData['contacts']);
//        $phone[] = $request->mobile;
        $code = rand(100000,999999);
        $message = "Your DSMS OTP is ".$code.". UID ".$request->signature;
//        $total = count($phone);
//        return response()->json($total);
        $messageData = [

            [
                "msisdn" => $mobile,
                "text" => $message,
                "csms_id" => uniqid(),
            ]
        ];

        $params = [
            "api_token" => $API_TOKEN,
            "sid" => $SID,
            "sms" => $messageData,
        ];

        $params = json_encode($params);
        $url = trim($DOMAIN, '/') . "/api/v3/send-sms/dynamic";

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'accept:application/json'
        ));


        $response[] = curl_exec($ch);
        curl_close($ch);

        // $code2 = $request->signature;
        // return $code2;

        $otp = new Otp();
        $otp->mobile = $mobile;
        $otp->otp = $code;
        $otp->signature = $request->signature;

        $otp->save();


        return ApiResponse::send_Otp($otp);
//        return response()->json([
//            'message'=>'Otp Send',
//            'data'=>$otp
//        ]);

//        return response()->json($response) ;
    }

    public function send_login_otp(Request $request){
        $request->validate([
            'mobile'=>'required',
            'signature'=>'required',
        ]);

        $u = User::where('mobile','=',$request->mobile)->first();
        if($u==null){
            return response()->json([
                'message'=>'please singup first'
            ],404);
        }

        $user = Otp::where('mobile','=',$request->mobile)->first();
        if ($user){
            $user->delete();
        }

        $mobile = $request->mobile;
        $DOMAIN = env('DOMAIN');
        $SID = env('SID');
        $API_TOKEN  = env('API_TOKEN');
//        $bookData = $request->all();

//            return response($value['mobile']);

//        return response($bookData['contacts']);
//        $phone[] = $request->mobile;
        $code = rand(100000,999999);
        $message = "Your DSMS OTP is ".$code.". UID ".$request->signature;
//        $total = count($phone);
//        return response()->json($total);
        $messageData = [

            [
                "msisdn" => $mobile,
                "text" => $message,
                "csms_id" => uniqid(),
            ]
        ];

        $params = [
            "api_token" => $API_TOKEN,
            "sid" => $SID,
            "sms" => $messageData,
        ];

        $params = json_encode($params);
        $url = trim($DOMAIN, '/') . "/api/v3/send-sms/dynamic";

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'accept:application/json'
        ));


        $response[] = curl_exec($ch);
        curl_close($ch);

        // $code2 = $request->signature;
        // return $code2;

        $otp = new Otp();
        $otp->mobile = $mobile;
        $otp->otp = $code;
        $otp->signature = $request->signature;

        $otp->save();

        return ApiResponse::send_Otp($otp);
//
//        return response()->json([
//            'message'=>'Otp Send',
//            'data'=>$otp
//        ]);

//        return response()->json($response) ;
    }

    public function otp_check(Request $request){
        $request->validate([
            'code'=>'required'
        ]);

        $otp = Otp::where('otp','=',$request->code)->first();
        if ($otp){
            $message="Otp Correct";
            return ApiResponse::successWitMessage($otp,$message);
        }else{
            $message="OTP is not valid";
            $code = 401;
            return ApiResponse::error($code,$message);
        }



    }
    public function pin_set(Request $request){
        $request->validate([
            'pin'=>'required',
            'otp'=>'required'
        ]);

        $otp = Otp::where('otp','=',$request->otp)->first();
        if ($otp){
            $mobile = $otp->mobile;

            $user = User::where('mobile','=',$mobile)->first();
            if ($user == null){
                $u = new User();
                $u->mobile = $mobile;
                $u->password = Hash::make($request->pin);
                $u->save();
                $otp->delete();
                $message= "Pin set Successfully";
                return ApiResponse::successWitMessage($otp,$message);

            }else{
                $user->password = Hash::make($request->pin);
                $user->save();

                $otp->delete();

                return  response()->json([
                    'message'=>'Pin Change Successful',
                ],200);
            }
        }else{
            return  response()->json([
                'otp'=>'Otp expired. please try again'
            ],404);
        }


    }

    public function login(Request $request){


        $loginData = $request->validate([
            'mobile' => 'required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()-> json(['message' => 'Invalid Credentials'],401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);

    }
}


