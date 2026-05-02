<?php

namespace Database\Seeders;

use App\Models\Auditoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Auditoria::factory()
            ->count(2000)
            ->create();
    }
}
