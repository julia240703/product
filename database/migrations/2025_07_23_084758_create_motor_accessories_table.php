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
        Schema::create('motor_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('accessory_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('function')->nullable();
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('part_number')->nullable();
            $table->bigInteger('price')->nullable();
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_accessories');
    }
};
