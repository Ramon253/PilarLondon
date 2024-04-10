<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        \App\Models\User::factory(10)->create();
        \App\Models\Teacher::factory()->create(['full_name' => 'Pilar', 'surname' => 'Gallardo Arana']);
        \App\Models\Group::factory(10)->create();
        \App\Models\Student_group::factory(10)->create();


    }   
}
