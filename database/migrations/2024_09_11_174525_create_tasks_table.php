<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('description');
            $table->dateTime('due_date'); 
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->enum('priority', ['high', 'low', 'medium']);
            $table->softDeletes(); 
            $table->timestamp('status_changed_at')->nullable(); // would use it to track the status changes dates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
