<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Review::factory()->count(100)->create();
    }
}
