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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_template_id')->constrained('banner_templates')->onDelete('cascade');
            $table->string('title')->nullable(); // Judul banner
            $table->string('image_path')->nullable(); // Path gambar
            $table->string('status')->default('active'); // Status (active/inactive)
            $table->integer('order')->unsigned()->default(1); // Urutan
            $table->unique(['banner_template_id', 'order']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
