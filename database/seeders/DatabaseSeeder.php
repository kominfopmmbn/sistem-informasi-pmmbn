<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin PMMBN',
            'email' => 'admin@pmmbn.com',
            'password' => Hash::make('admin123'),
        ]);
        $admin->assignRole('Administrator');

        $this->call(CategorySeeder::class);
        // $this->call(TagSeeder::class);
        // $this->call(ArticleSeeder::class);
    }
}
