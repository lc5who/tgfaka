<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    //
    public function login( Request $request)
    {
        $validData = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('name', $validData['username'])->first();

        if (!$user || !Hash::check($validData['password'], $user->password)) {
            throw ValidationException::withMessages([
                'name' => ['提供的凭证不正确。'],
            ]);
        }
        $token = $user->createToken($user->name)->plainTextToken;
        $res = array_merge($user->toArray(), ['token'=>$token]);
        return $this->success($res, '登录成功');
    }
}
