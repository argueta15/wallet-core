<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Food', 'user_id' => 1]);
        Category::create(['name' => 'Entertainment', 'user_id' => 1]);
        Category::create(['name' => 'Bills', 'user_id' => 1]);
        Category::create(['name' => 'Transport', 'user_id' => 1]);
        Category::create(['name' => 'Shopping', 'user_id' => 1]);
    }
}
