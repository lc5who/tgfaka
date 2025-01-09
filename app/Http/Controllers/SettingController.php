<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    //
    public function index()
    {
        $data = Cache::get('faka',config('faka'));
        return $this->success($data);
    }
    public function update(Request $request)
    {
        $data =$request->all();
        Cache::forever('faka',$data);
        return $this->success();
    }
}
