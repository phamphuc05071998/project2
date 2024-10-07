<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles and permissions
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Create a sample user
        $user = User::firstOrCreate(
            ['email' => 'phamphuc05071998@gmail.com'],
            [
                'name' => 'testuser',
                'password' => bcrypt('123456'), // Change this to a secure password
            ]
        );

        // Create an editor user
        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'editoruser',
                'password' => bcrypt('123456'), // Change this to a secure password
            ]
        );

        // Assign roles to users
        $user->assignRole('author');
        $editor->assignRole('editor');

        // Create a sample category
        $category = Category::firstOrCreate([
            'name' => 'Sample Category',
        ]);

        // Create 10 sample posts for the sample user
        Post::factory(10)->create([
            'user_id' => $user->id,
            'status' => 'approved', // Ensure the post is approved
            'category_id' => $category->id, // Assign the sample category
        ]);
    }
}
