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
        Schema::create('motor_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained()->onDelete('cascade');
            $table->string('color_name');
            $table->string('hex_color')->nullable(); // optional
            $table->string('image_url'); // gambar warna motor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_colors');
    }
};
