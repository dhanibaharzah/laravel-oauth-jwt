<?php

namespace App\Http\Controllers;

use App\User;
use App\Utilities\ProxyRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Str;

class AuthController extends Controller
{
    protected $proxy;

    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'min:0', 'numeric'],
            'role' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return response([
                'id' => Str::uuid(),
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        } else {
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => bcrypt(request('password')),
                'role' => request('role'),
            ]);

            $resp = $this->proxy->grantPasswordToken(
                $user->email,
                request('password')
            );

            return response([
                'token' => $resp->access_token,
                'expiresIn' => $resp->expires_in,
                'message' => 'Your account has been created',
            ], 201);
        }
    }

    public function login()
    {
        $user = User::where('email', request('email'))->first();

        abort_unless($user, 404, 'This combination does not exists.');
        abort_unless(
            \Hash::check(request('password'), $user->password),
            403,
            'This combination does not exists.'
        );

        $resp = $this->proxy
            ->grantPasswordToken(request('email'), request('password'));

        return response([
            'status' => 200,
            'data' => [
                'user' => $user->email,
                'token' => $resp->access_token,
                'expiresIn' => $resp->expires_in,
                'message' => 'You have been logged in',
            ]
        ], 200);
    }

    public function refreshToken()
    {
        $resp = $this->proxy->refreshAccessToken();

        return response([
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'message' => 'Token has been refreshed.',
        ], 200);
    }

    public function logout()
    {
        $token = request()->user()->token();
        $token->delete();

        // remove the httponly cookie
        cookie()->queue(cookie()->forget('refresh_token'));

        return response([
            'message' => 'You have been successfully logged out',
        ], 200);
    }

    public function check()
    {
        return response([
            'status' => 200,
            'message' => 'Laravel is working fine...',
        ], 200);
    }
}
