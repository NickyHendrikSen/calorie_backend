<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', 'UserController@register');
Route::post('/inviteFriend', 'UserController@inviteFriend');

Route::middleware(['auth:api'])->group(function ($router){
    Route::get('/getFoodByUser', 'FoodController@getFoodByUser');
    Route::post('/getUserFoodByDate', 'FoodController@getUserFoodByDate');
    Route::post('/addFood', 'FoodController@addFood');
    Route::post('/addDetailedFood', 'FoodController@addDetailedFood');
    Route::post('/deleteFood', 'FoodController@deleteFood');
    Route::post('/editFood', 'FoodController@editFood');
    Route::get('/getReport', 'FoodController@getReport');
    Route::get('/getAllFood', 'FoodController@getAllFood');

    Route::get('/getMeal', 'MealController@getMeal');
    Route::get('/getMealByUser', 'MealController@getMealByUser');
    Route::post('/deleteMeal', 'MealController@deleteMeal');
    Route::post('/editMeal', 'MealController@editMeal');
    Route::post('/addMeal', 'MealController@addMeal');
    
    Route::post('/searchUser', 'UserController@searchUser');
});

Route::namespace("Auth")->group(function ($router){
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');
    Route::post('/refresh', 'AuthController@refresh');
    Route::post('/me', 'AuthController@me');
    Route::post('/changePassword', 'AuthController@changePassword');
});
