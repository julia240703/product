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
        Schema::create('banner_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Misalnya "Home", "Produk"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banner_templates');
    }
};
