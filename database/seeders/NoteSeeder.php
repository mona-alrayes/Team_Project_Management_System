<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Note::factory()->create([
            'note' => 'note 1',
            'task_id' => '1',
            'user_id' => '4', //ayham
        ]);

        Note::factory()->create([
            'note' => 'note 2',
            'task_id' => '2',
            'user_id' => '6', //yosef
        ]);

        Note::factory()->create([
            'note' => 'note 3',
            'task_id' => '3',
            'user_id' => '3', //hani
        ]);

        Note::factory()->create([
            'note' => 'note 4',
            'task_id' => '4',
            'user_id' => '2', //mona
        ]);

        Note::factory()->create([
            'note' => 'note 5',
            'task_id' => '5',
            'user_id' => '3', //hani
        ]);

        Note::factory()->create([
            'note' => 'note 6',
            'task_id' => '6',
            'user_id' => '5', //somar
        ]);
    }
}
