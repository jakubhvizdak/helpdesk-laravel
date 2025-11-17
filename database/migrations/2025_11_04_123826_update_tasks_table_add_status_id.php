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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('description');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('task_statuses')->onUpdate('cascade')->onDelete('restrict');
            $table->dropColumn('status');
            $table->unsignedBigInteger('status_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
