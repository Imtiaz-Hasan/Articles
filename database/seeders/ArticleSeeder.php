<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $categories = Category::all();
        
        Article::updateOrCreate(
            ['slug' => 'first-article'],
            [
                'title' => 'First Article',
                'body' => 'This is the body of the first article.',
                'status' => 'published',
                'category_id' => $categories[0]->id ?? 1,
                'user_id' => $user->id,
            ]
        );
        
        Article::updateOrCreate(
            ['slug' => 'second-article'],
            [
                'title' => 'Second Article',
                'body' => 'This is the body of the second article.',
                'status' => 'draft',
                'category_id' => $categories[1]->id ?? 1,
                'user_id' => $user->id,
            ]
        );
    }
}
