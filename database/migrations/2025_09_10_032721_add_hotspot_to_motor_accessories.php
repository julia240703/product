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
        Schema::table('motor_accessories', function (Blueprint $table) {
            //
            $table->unsignedTinyInteger('x_percent')->nullable()->after('image'); // 0..100
            $table->unsignedTinyInteger('y_percent')->nullable()->after('x_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motor_accessories', function (Blueprint $table) {
            //
            $table->dropColumn(['x_percent', 'y_percent']);
        });
    }
};
