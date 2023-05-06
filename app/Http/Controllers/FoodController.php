<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Food;
use App\Models\Meal;
use Carbon\Carbon;

class FoodController extends Controller
{
    function addFood(Request $request){
        $name = $request->name;
        $calorie = $request->calorie;
        $taken_at = $request->taken_at;
        $meal_id = $request->meal_id;
        
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        
        $request->validate([
            'name' => 'required',
            'calorie' => 'required|numeric|min:1',
            'taken_at' => 'required',
            'meal_id' => 'required'
        ]);
        $meal = Meal::find($meal_id);

        if($meal->user_id != $user_id){
            return response()->json(['success' => false], 403);
        }

        $count = DB::table("foods")->where("meal_id", $meal_id)->whereDate("taken_at", $taken_at)->count();
        $has_maximum = $meal->first()->has_maximum;
        $maximum = $meal->first()->maximum;

        if($has_maximum && $count >= $maximum){
            return response()->json(['success' => false, "message" => "You have reach the maximum entries"]);
        }

        try{
            Food::create([
                'meal_id' => $meal_id,
                'name' => $name,
                'calorie' => $calorie,
                'taken_at' => $taken_at
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Food Successfully Added']);
    }

    function addDetailedFood(Request $request){
        $name = $request->name;
        $calorie = $request->calorie;
        $taken_at = $request->taken_at;
        $meal_id = $request->meal_id;
        
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;

        if(!$user->is_admin){
            return response()->json(['success' => false], 403);
        }
        
        $request->validate([
            'name' => 'required',
            'calorie' => 'required|numeric|min:1',
            'taken_at' => 'required',
            'meal_id' => 'required'
        ]);
        $meal = Meal::find($meal_id);
        $count = DB::table("foods")->where("meal_id", $meal_id)->whereDate("taken_at", $taken_at)->count();
        $has_maximum = $meal->first()->has_maximum;
        $maximum = $meal->first()->maximum;

        if($has_maximum && $count >= $maximum){
            return response()->json(['success' => false, "message" => "You have reach the maximum entries"]);
        }

        try{
            Food::create([
                'meal_id' => $meal_id,
                'name' => $name,
                'calorie' => $calorie,
                'taken_at' => $taken_at
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Food Successfully Added']);
    }

    function deleteFood(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $foodid = $request->id;
        $food = Food::join('meals', 'meals.id', '=', 'foods.meal_id')->select("foods.id", "meals.user_id as user_id")->find($foodid);

        if(!$food){
            return response()->json(['success' => false, 'message' => 'Food not found']);
        } 
        if(!$user->is_admin && $food->user_id != $user->id ){
            return response()->json(['success' => false], 403);
        }

        $food->delete();
        return response()->json(['success' => true, 'message' => 'Food Successfully Deleted'], 200);
    }

    function editFood(Request $request){
        $foodid = $request->id;
        $name = $request->name;
        $calorie = $request->calorie;
        $taken_at = $request->taken_at;

        $request->validate([
            'name' => 'required',
            'calorie' => 'required|numeric|min:1',
        ]);
        
        $food = Food::join('meals', 'meals.id', '=', 'foods.meal_id')->select("foods.id", "meals.user_id as user_id")->find($foodid);
        
        if(!$food){
            return response()->json(['success' => false, 'message' => 'Food not found'], 500);
        } 

        $user = JWTAuth::parseToken()->authenticate();
        if(!$user->is_admin && $food->user_id != $user->id){
            return response()->json(['success' => false], 403);
        }

        DB::table('foods')->where('id',$foodid)->update([
            'name' => $name,
            'calorie' => $calorie,
            'taken_at' => $taken_at,
        ]);
        return response()->json(['success' => true, 'message' => 'Food Successfully Updated'], 200);
    }

    function getUserFoodByDate(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->id;
        $taken_at = $request->taken_at;

        $meals = DB::table("meals")->where("user_id", $user_id)->get();

        $totalCalories = 0;
        for($i = 0; $i < $meals->count(); $i++){ 
            $foods = DB::table("foods")->where("meal_id", $meals[$i]->id)->whereDate("taken_at", $taken_at);
            $meals[$i]->foods = $foods->get();
            $totalCalories = $totalCalories + $foods->sum("calorie");
        }

        return response()->json(['success' => false, 'meals' => $meals, 'totalCalories' => $totalCalories, 'calorieLimit' => $user->calorie_limit]);
    }

    function getAllFood(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        if(!$user->is_admin){
            return response()->json(['success' => false], 403);
        }

        $foods = DB::table("foods")->join('meals', 'meals.id', '=', 'foods.meal_id')->join('users', 'users.id', '=', 'meals.user_id')
            ->select('foods.id as id', 'foods.name as name', 'users.name as user_name', "calorie", "foods.created_at as created_at", "taken_at", "email", "meals.name as meal")
            ->orderBy('created_at', 'DESC')->orderBy('taken_at', 'DESC')->orderBy('foods.name', 'ASC')->orderBy('users.email', 'ASC')->paginate(10); 
        return response()->json(['success' => true, 'foods' => $foods], 200);
    }
    
    function getReport(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        if(!$user->is_admin){
            return response()->json(['success' => false], 403);
        }
        
        $lastTwentyFourHours = Carbon::now()->subDays(1);
        $lastSevenDays = DB::table("foods")->where('taken_at', ">", Carbon::now()->subDays(7));
        $lastLastSevenDays = DB::table("foods")->where('taken_at', "<=", Carbon::now()->subDays(7))
        ->where('taken_at', ">", Carbon::now()->subDays(14));
        $count = $lastSevenDays->count();
        $previousCount = $lastLastSevenDays->count();
        $currentDay = DB::table("foods")->where('taken_at', ">=", $lastTwentyFourHours)->count();
        $percentage = ($count - $previousCount)/$previousCount*100;

        $grouped = $lastSevenDays->selectRaw("sum(calorie) as sum")->groupBy("taken_at")->get("sum");
        $avgCalories = $grouped->sum("sum")/$grouped->count();

        return response()->json([
            'success' => true,
            'entries' => [
                'amount' => $count,
                'percentage' => $previousCount == 0 ? "inf" : $percentage,
                'current' => $currentDay
            ], 
            'average' => $avgCalories,
        ], 200);
    }
}
