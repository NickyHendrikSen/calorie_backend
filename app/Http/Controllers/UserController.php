<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Meal;

class UserController extends Controller
{
    function createDefaultMeals($id){
        Meal::create([
            'user_id' => $id,
            'name' => 'Breakfast',
            'has_maximum' => false,
            'maximum' => 0,
        ]);
        Meal::create([
            'user_id' => $id,
            'name' => 'Lunch',
            'has_maximum' => false,
            'maximum' => 0,
        ]);
        Meal::create([
            'user_id' => $id,
            'name' => 'Dinner',
            'has_maximum' => false,
            'maximum' => 0,
        ]);
    }

    function register(Request $request){
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $hashed = Hash::make($password, [
            'memory' => 1024,
            'time' => 2,
            'threads' => 2,
        ]);

        try{
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $hashed,
                'is_admin' => false,
            ]);
        
            //Default meals
            self::createDefaultMeals($user->id);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }   
    
    function searchUser(Request $request){
        $search = $request->search;
        $users = User::where('email', 'like', '%' . $search . '%' )
            ->orWhere('email', 'like', '%' . $search . '%' )->limit(5)->get();
            
        return response()->json(['success' => true, 'users' => $users]);
    }

    function generateRandomPassword(){
        $length = 10;
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@!#$%^&*()';
        $max = mb_strlen($keyspace, '8bit') - 1;
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
    function inviteFriend(Request $request){
        $name = $request->name;
        $email = $request->email;
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $password = self::generateRandomPassword();

        $hashed = Hash::make($password, [
            'memory' => 1024,
            'time' => 2,
            'threads' => 2,
        ]);

        try{
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $hashed,
                'is_admin' => false,
            ]);
        
            //Default meals
            self::createDefaultMeals($user->id);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true, 'token' => $password]);
    }   
}
