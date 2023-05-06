<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Meal;

class MealController extends Controller
{
    function getMeal(Request $request){
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        
        $meals = DB::table("meals")->where("user_id", $user_id)->get();

        return response()->json(['success' => true, 'meals' => $meals]);
    }

    function getMealByUser(Request $request){
        $user_id = $request->id;
        
        $meals = DB::table("meals")->where("user_id", $user_id)->get();

        return response()->json(['success' => true, 'meals' => $meals]);
    }

    function deleteMeal(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $mealid = $request->id;
        $meal = Meal::find($mealid);

        if(!$meal){
            return response()->json(['success' => false, 'message' => 'Meal not found']);
        } 
        if(!$user->is_admin && $meal->user_id != $user->id ){
            return response()->json(['success' => false], 403);
        }

        $meal->delete();
        return response()->json(['success' => true, 'message' => 'Meal Successfully Deleted'], 200);
    }

    function editMeal(Request $request){
        $mealid = $request->id;
        $name = $request->name;
        $has_maximum = $request->has_maximum;
        $maximum = $request->maximum;

        $request->validate([
            'name' => 'required',
            'has_maximum' => 'required',
        ]);
        if($has_maximum){
            $request->validate([
                'maximum' => 'required|numeric|min:1',
            ]);
        }
        
        $user = JWTAuth::parseToken()->authenticate();

        $mealid = $request->id;
        $meal = Meal::find($mealid);

        if(!$meal){
            return response()->json(['success' => false, 'message' => 'Meal not found']);
        } 
        if(!$user->is_admin && $meal->user_id != $user->id ){
            return response()->json(['success' => false], 403);
        }

        if($has_maximum){
            DB::table('meals')->where('id',$mealid)->update([
                'name' => $name,
                'has_maximum' => $has_maximum,
                'maximum' => $maximum,
            ]);
        }
        else{
            DB::table('meals')->where('id',$mealid)->update([
                'name' => $name,
                'has_maximum' => $has_maximum,
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Meal Successfully Updated'], 200);
    }

    function addMeal(Request $request){
        $name = $request->name;
        $has_maximum = $request->has_maximum;
        $maximum = $request->maximum;
        
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        
        $request->validate([
            'name' => 'required',
            'has_maximum' => 'required',
        ]);
        if($has_maximum){
            $request->validate([
                'maximum' => 'required|numeric|min:1',
            ]);
        }

        try{
            if($has_maximum){
                Meal::create([
                    'user_id' => $user_id,
                    'name' => $name,
                    'has_maximum' => $has_maximum,
                    'maximum' => $maximum,
                ]);
            }
            else{
                Meal::create([
                    'user_id' => $user_id,
                    'name' => $name,
                    'has_maximum' => $has_maximum,
                    'maximum' => 1,
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Meal Successfully Added']);
    }
}
