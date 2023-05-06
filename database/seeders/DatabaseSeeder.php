<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Food;
use App\Models\Meal;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@admin.admin',
            'password' => Hash::make('admins'),
            'is_admin' => true
        ]);
        User::create([
            'id' => 2,
            'name' => 'Foo',
            'email' => 'asd@asd.asd',
            'password' => Hash::make('asdasd'),
            'is_admin' => false
        ]);
        Meal::create([
            'user_id' => 1,
            'name' => 'Breakfast',
            'has_maximum' => false,
            'maximum' => 1,
        ]);
        Meal::create([
            'user_id' => 1,
            'name' => 'Lunch',
            'has_maximum' => false,
            'maximum' => 1,
        ]);
        Meal::create([
            'user_id' => 1,
            'name' => 'Dinner',
            'has_maximum' => false,
            'maximum' => 1,
        ]);
        Meal::create([
            'user_id' => 2,
            'name' => 'Breakfast',
            'has_maximum' => false,
            'maximum' => 1,
        ]);
        Meal::create([
            'user_id' => 2,
            'name' => 'Lunch',
            'has_maximum' => false,
            'maximum' => 1,
        ]);
        Meal::create([
            'user_id' => 2,
            'name' => 'Dinner',
            'has_maximum' => false,
            'maximum' => 1,
        ]);

        //Foods
        Food::create([
            'name' => 'Banana',
            'calorie' => 200,
            'meal_id' => 1,
            'taken_at' => Carbon::now()->subDays(1)
        ]);
        Food::create([
            'name' => 'Ice Cream',
            'calorie' => 350,
            'meal_id' => 1,
            'taken_at' => Carbon::now()->subDays(2)
        ]);
        Food::create([
            'name' => 'Coke',
            'calorie' => 300,
            'meal_id' => 2,
            'taken_at' => Carbon::now()->subDays(3)
        ]);
        Food::create([
            'name' => 'Apple',
            'calorie' => 250,
            'meal_id' => 2,
            'taken_at' => Carbon::now()->subDays(4)
        ]);
        Food::create([
            'name' => 'Pepsi',
            'calorie' => 350,
            'meal_id' => 3,
            'taken_at' => Carbon::now()->subDays(5)
        ]);
        Food::create([
            'name' => 'Chicken',
            'calorie' => 280,
            'meal_id' => 4,
            'taken_at' => Carbon::now()->subDays(6)
        ]);
        Food::create([
            'name' => 'Ketchup',
            'calorie' => 20,
            'meal_id' => 4,
            'taken_at' => Carbon::now()->subDays(7)
        ]);
        Food::create([
            'name' => 'Tomato',
            'calorie' => 80,
            'meal_id' => 5,
            'taken_at' => Carbon::now()->subDays(8)
        ]);
        Food::create([
            'name' => 'Pizza',
            'calorie' => 600,
            'meal_id' => 5,
            'taken_at' => Carbon::now()->subDays(9)
        ]);
        Food::create([
            'name' => 'Cola',
            'calorie' => 230,
            'meal_id' => 6,
            'taken_at' => Carbon::now()->subDays(9)
        ]);
        Food::create([
            'name' => 'Popcorn',
            'calorie' => 450,
            'meal_id' => 6,
            'taken_at' => Carbon::now()->subDays(9)
        ]);
        Food::create([
            'name' => 'Milkshake',
            'calorie' => 200,
            'meal_id' => 3,
            'taken_at' => Carbon::now()->subDays(9)
        ]);
        Food::create([
            'name' => 'Cookie',
            'calorie' => 150,
            'meal_id' => 1,
            'taken_at' => Carbon::now()->subDays(9)
        ]);
        
    }
}
