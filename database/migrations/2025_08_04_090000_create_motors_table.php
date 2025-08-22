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
        Schema::create('motors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('motor_code_otr')->nullable();
            $table->string('motor_code_credit')->nullable();
            $table->string('wms_code')->nullable();
            $table->unsignedBigInteger('category_id')->nullable(); // -> motor_categories
            $table->unsignedBigInteger('type_id')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('accessory_thumbnail')->nullable();
            $table->enum('status', ['published', 'unpublished'])->default('unpublished');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('motor_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motors');
    }
};