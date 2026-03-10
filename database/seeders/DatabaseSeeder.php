<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        
        User::factory(40)->create();
        
        $this->call([
            AuditoriaSeeder::class,
        ]);
        
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'brayan@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');
    }
}
