<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //project 1 -----------------------------------
        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 5,  //somar
            'role' => 'manager',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 2,  //mona
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 3,  //hani
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 4,   //ayham
            'role' => 'tester',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 1,
            'user_id' => 6,  //yosef
            'role' => 'tester',
        ]);
        //--------------------------------------------------------------

        //project2 ------------------------------------------------------
        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 4,  //ayham
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 5,  //somar
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 2,  //mona
            'role' => 'tester',
        ]);
         
        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 3,     //hani
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 6,   //yosef
            'role' => 'manager',
        ]);
      //---------------------------------------------------------------------


      //project 3 -----------------------------------------------------------
        DB::table('project_user')->insert([
            'project_id' => 3,
            'user_id' => 2,  //mona
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 3,
            'user_id' => 6,   //yosef
            'role' => 'developer',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 3,
            'user_id' => 4,  //ayham
            'role' => 'manager', 
        ]);

        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 5,  //somar
            'role' => 'tester',
        ]);

        DB::table('project_user')->insert([
            'project_id' => 2,
            'user_id' => 3,   //hani
            'role' => 'tester',
        ]);
    }
}
