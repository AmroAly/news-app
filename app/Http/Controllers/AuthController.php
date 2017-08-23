<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ConfirmationEmail;
use App\User;
use Hash;
use Mail;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')
            ->only('logout');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->verfication_token = str_random(60);
        $user->api_token = str_random(60);

        $user->save();

        Mail::to($user->email)->send(new ConfirmationEmail($user));

        return response()
            ->json([
                'registered' => true
            ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
                ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->verified) {
                return response()
                    ->json([
                        'verified' => false
                    ], 422);
            }
            $user->api_token = str_random(60);
            $user->save();

            return response()
                ->json([
                    'authenticated' => true,
                    'api_token'     => $user->api_token,
                    'user_id'       => $user->id
                ]);
        }

        return response()
            ->json([
                'email' => ['Provided email and password doesn\'t match']
            ], 422);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()
            ->json(['logged_out' => true]);
    }

    public function confirmEmail(Request $request, $token)
    {
        $user = User::where('verfication_token', $token)
            ->first();
        if (!$user) {
            return response()->json(["Your account has already been verified. Please Login using your credentials"],422);
        }

        $user->hasVerified();
        return response()
                ->json([
                     "verified" => true
                 ]);
    }

    public function checkApiToken(Request $request)
    {
        if ($request->token && $request->user_id) {
            $user = User::where([
                'id' => $request->user_id,
                'api_token' => $request->token
            ])->first();

            if (!$user) {
                return response()
                        ->json([
                            'authenticated' => false
                        ], 401);
            }

            return response()
                    ->json([
                        'authenticated' => true
                    ]);
        }
    }
}
