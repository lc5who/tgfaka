<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    protected function success($data=[],$msg="success")
    {
        return response()->json([
            'code'=>0,
            'message'=>$msg,
            'data'=>$data
        ]);
    }

    protected function fail($data=[],$msg="fail",$code=1)
    {
        return response()->json([
            'code'=>$code,
            'message'=>$msg,
            'data'=>$data
        ]);
    }
}
