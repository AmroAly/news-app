<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Post;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Post::truncate();

        foreach (range(1, 10) as $i) {
            Post::create([
                'user_id' => $i,
                'title' => $faker->sentence,
                'body' => $faker->paragraph(3),
                'image' => 'test.jpg'
            ]);
            Post::create([
                'user_id' => $i,
                'title' => $faker->sentence,
                'body' => $faker->paragraph(3),
                'image' => 'test.jpg'
            ]);
        }
    }
}
