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
        Schema::table('apparels', function (Blueprint $table) {
            //
            $table->string('apparel_url')->nullable()->after('part_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apparels', function (Blueprint $table) {
            //
            $table->dropColumn('apparel_url');
        });
    }
};
