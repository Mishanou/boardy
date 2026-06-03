<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Тестовый пользователь для входа
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@boardy.local',
            'password' => bcrypt('password'),
        ]);

        // 2. Ещё 4 случайных пользователя
        $users = User::factory()->count(4)->create();

        // Добавляем тестового пользователя в общую коллекцию,
        // чтобы посты и комментарии могли создаваться и от его имени тоже
        $users->push($testUser);

        // 3. Создаём 10 постов от случайных пользователей
        $posts = Post::factory()->count(10)->create([
            'user_id' => fn () => $users->random()->id,
        ]);

        // 4. Создаём 25 комментариев к случайным постам от случайных пользователей
        Comment::factory()->count(25)->create([
            'post_id' => fn () => $posts->random()->id,
            'user_id' => fn () => $users->random()->id,
        ]);
    }
}
