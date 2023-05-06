<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'User not found'], 401);
        }
        
        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function changePassword(Request $request){
        $oldPassword = $request->oldPassword;            
        $newPassword = $request->newPassword;        
        $confirmPassword = $request->confirmPassword;        

        $this->validate($request, [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required|required_with:newPassword|same:newPassword'
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $currentPassword = $user->password;
        $user_id = $user->id;
        
        if (!Hash::check($oldPassword, $currentPassword)) {
            return response()->json(['success' => false, "message" => "The given data was invalid.",
            "errors" => [
                "oldPassword" => ["Old Password doesn't match"]
            ]], 422);
        }

        $hashed = Hash::make($newPassword, [
            'memory' => 1024,
            'time' => 2,
            'threads' => 2,
        ]);
        
        DB::table('users')->where('id', $user_id)->update([
            'password' => $hashed,
        ]);
        return response()->json(['success' => true], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'is_admin' => Auth::user()->is_admin,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
