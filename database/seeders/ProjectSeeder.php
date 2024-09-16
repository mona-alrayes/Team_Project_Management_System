<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory()->create([
            'name' => 'project 1',
            'description' => 'info about project 1',
        ]);

        Project::factory()->create([
            'name' => 'project 2',
            'description' => 'info about project 2',
        ]);

        Project::factory()->create([
            'name' => 'project 3',
            'description' => 'info about project 2',
        ]);
    }
}
