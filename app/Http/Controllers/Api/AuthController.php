<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function send_otp_register(Request $request){
        $request->validate([
            'mobile'=>'required',
            'signature'=>'required',
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

        return response()->json([
            'message'=>'Otp Send',
            'data'=>$otp
        ]);

//        return response()->json($response) ;
    }
}
