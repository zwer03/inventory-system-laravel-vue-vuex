<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        if (!auth()->attempt($request->only(['email', 'password']))) {
            return response(['message' => 'You have inputted incorrect user credentials.'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response([
            'user' => [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                '_token' => $accessToken,
            ]
        ])
        ->cookie('_token', $accessToken, config('session.lifetime'));
    }

    public function logout (Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $cookie = Cookie::forget('_token');
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200)->withCookie($cookie);;
    }
}