<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',

        {
            DB::table("categories")->insert([
                "id" => "SMARTPHONE",
                "name" => "Smartphone",
                "description" => "Description for Smartphone",
                "created_at" => "2020-10-10 10:10:10"
            ]);
            DB::table("categories")->insert([
                "id" => "FOOD",
                "name" => "Food",
                "description" => "Description for Food",
                "created_at" => "2020-10-10 10:10:10"
            ]);
            DB::table("categories")->insert([
                "id" => "FASHION",
                "name" => "Fashion",
                "description" => "Description for Fashion",
                "created_at" => "2020-10-10 10:10:10"
            ]);
            DB::table("categories")->insert([
                "id" => "LAPTOP",
                "name" => "Laptop",
                "description" => "Description for Laptop",
                "created_at" => "2020-10-10 10:10:10"
            ]);
        }
    }
}
