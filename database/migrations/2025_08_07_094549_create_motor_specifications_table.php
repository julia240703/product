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
        Schema::create('motor_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained('motors')->onDelete('cascade');
            $table->string('category'); // e.g., Rangka, Mesin, Dimensi, Kelistrikan, Kapasitas
            $table->string('atribut'); // e.g., Tipe Mesin
            $table->string('detail'); // e.g., 4 Langkah, SOHC, eSP
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_specifications');
    }
};
