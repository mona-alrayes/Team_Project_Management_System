<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::factory()->create([
            'title' => 'task1',
            'description' => 'info about task1',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '20-09-2024',
            'assigned_to' => '2',  // mona
            'project_id' => '1',
        ]);

        Task::factory()->create([
            'title' => 'task2',
            'description' => 'info about task2',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '22-09-2024',
            'assigned_to' => '3',  // hani
            'project_id' => '1',
        ]);

        Task::factory()->create([
            'title' => 'task3',
            'description' => 'info about task3',
            'status' => 'pending',
            'priority' => 'low',
            'due_date' => '25-09-2024',
            'assigned_to' => '4',  // ayham
            'project_id' => '2',
        ]);

        Task::factory()->create([
            'title' => 'task4',
            'description' => 'info about task4',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '20-09-2024',
            'assigned_to' => '5',  // somar
            'project_id' => '2',
        ]);

        Task::factory()->create([
            'title' => 'task5',
            'description' => 'info about task5',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '22-09-2024',
            'assigned_to' => '6',  // yosef
            'project_id' => '3',
        ]);

        Task::factory()->create([
            'title' => 'task6',
            'description' => 'info about task6',
            'status' => 'pending',
            'priority' => 'low',
            'due_date' => '25-09-2024',
            'assigned_to' => '2',  // mona
            'project_id' => '3',
        ]);
    }
}
