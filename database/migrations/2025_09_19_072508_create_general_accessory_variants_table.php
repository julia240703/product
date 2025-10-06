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
        Schema::create('general_accessory_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_accessory_id')->constrained()->onDelete('cascade');
            $table->string('variant_name')->nullable();
            $table->string('sku')->nullable();
            $table->string('color')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->string('image')->nullable(); // gambar khusus varian (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_accessory_variants');
    }
};
