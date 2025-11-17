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
        Schema::create('task_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onUpdate('restrict')->onDelete('cascade');
            $table->decimal('hours', 5, 2);
            $table->enum('activity', ['analyza', 'vyvoj', 'testovanie', 'oprava', 'konzultacia']);            $table->text('description')->nullable();
            $table->foreignId('service_hour_id')->nullable()->constrained('service_hours')->onUpdate('restrict')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_times');
    }
};
