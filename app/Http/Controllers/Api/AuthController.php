<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthController extends Controller
{
    public function login(Request $request): Response
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!$token = auth()->attempt($credentials)) {
            throw new BadRequestHttpException('Invalid email or password');
        }


        return \response([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
