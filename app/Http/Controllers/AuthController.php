<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('access_token')->plainTextToken;
            $user->setRememberToken($token);
            $user->save();
            return response()->json(["status" => "success", "remember_token" => $token, "admin" => $user->admin, "id" => $user->id], 200);
        }else{
            if(User::findByEmail($request->only('email'))){
                return response()->json(["status" => "failure", "message" => "Invalid password"], 401);
            }else{
                return response()->json(["status" => "failure", "message" => "Invalid credentials"], 401);
            }

        }
    }

    public function logout(Request $request)
    {
        $userId = $request['user_id'];
        $user = User::findOrFail($userId);
        $user->tokens()->delete();
        $user->setRememberToken(null);
        $user->save();
        return response()->json(["status" => "success"], 200);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            "first_name" => "required|string",
            "last_name" => "required|string",
            "language_id" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/u",
        ]);

        $user = new User();
        foreach($request->all() as $key => $val){
            $user->$key = $val;
        }
        if($user->save()){
            $token = $user->createToken('access_token')->plainTextToken;
            $user->setRememberToken($token);
            $user->save();
            return response()->json(["status" => "success", "remember_token" => $token], 200);
        }

        return response()->json(["status" => "error", "message" => "Something went wrong, try again later."], 400);
    }

    public function token(Request $request){
        $token = $request->bearerToken();

        if($token) {
            $exists  = User::where('remember_token', $token)->exists();

            return response()->json(["token" => $token, "exists" => $exists]);
        }

        return response()->json(["exists" => false]);
    }
}
