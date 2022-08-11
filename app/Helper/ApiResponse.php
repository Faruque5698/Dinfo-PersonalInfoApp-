<?php

namespace App\Helper;

class ApiResponse
{
    public function success($data){
        return response()->json([
            'code'=>200,
            'status'=>true,
            'message'=>'successful',
            'data'=>$data
        ],200);
    }

    public function error($code,$message){
        return response()->json([
           'code'=>$code,
           'status'=>false,
           'error'=>$message
        ],$code);
    }

    public function validation($message){
        return response()->json([
           'code'=>422,
           'status'=>false,
            'error'=>$message
        ],422);
    }

    public function not_found(){

        return response()->json([
            'code'=>404,
            'status'=>false,
            'error'=>'data not found'
        ],404);
    }

    public function login($data, $accessToken){
        return response()->json([
           'code'=>200,
           'status'=>true,
            'data'=>$data,
            'token'=>$accessToken

        ],200);
    }

    public function login_credintial_check($message){
        return response()->json([
            'code'=>401,
            'status'=>false,
            'error'=>$message

        ],401);
    }

    public function create($data){
        return response()->json([
           'code'=>200,
           'status'=>true,
           'message'=>'data saved',
           'data'=>$data
        ],200);
    }

//    public function credintial_error(){
//
//    }

public function dataExsits(){
        return response()->json([
            'code'=>409,
            'status'=>true,
            'message'=>'data all ready exists',

        ],409);
}

}
