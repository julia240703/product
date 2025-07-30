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
        Schema::create('motor_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->bigInteger('price');
            $table->string('image_url')->nullable();
            $table->text('compatibility_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_parts');
    }
};
