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
            $table->text('description')->nullable();
            $table->enum('status', ['priradena','v_rieseni','dokoncena','zamietnuta','pozastavena'])->default('priradena');
            $table->foreignId('project_id')->constrained('projects')->onUpdate('restrict')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onUpdate('restrict')->nullOnDelete();
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
