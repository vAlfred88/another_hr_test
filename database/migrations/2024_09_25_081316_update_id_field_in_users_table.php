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
        Schema::table('users', function (Blueprint $table) {
            // Drop the current id column
            $table->dropColumn('id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Add the uuid column and make it the primary key
            $table->uuid('id')->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the uuid column and add back the auto-incrementing id
            $table->dropColumn('id');
            $table->id();
        });
    }
};
