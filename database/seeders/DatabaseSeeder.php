<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()->create(['name' => 'Category 1']);
        Category::factory()->create(['name' => 'Category 2']);
        Category::factory()->create(['name' => 'Category 3']);
        Category::factory()->create(['name' => 'Category 4']);

        Status::factory()->create(['name' => 'Open', 'class' => 'bg-gray-200']);
        Status::factory()->create(['name' => 'Considering', 'class' => 'bg-purple text-white']);
        Status::factory()->create(['name' => 'In Progress', 'class' => 'bg-yellow text-white']);
        Status::factory()->create(['name' => 'Implemented', 'class' => 'bg-green text-white']);
        Status::factory()->create(['name' => 'Closed', 'class' => 'bg-red text-white']);

        Idea::factory(30)->create();
    }
}
