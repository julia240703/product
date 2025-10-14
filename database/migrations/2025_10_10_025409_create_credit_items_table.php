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
        Schema::create('credit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')
                ->constrained('credit_headers')   // <-- perbaiki nama tabel parent
                ->cascadeOnDelete();
            $table->unsignedBigInteger('dp_amount');
            $table->unsignedSmallInteger('tenor_months');
            $table->unsignedBigInteger('installment');
            $table->timestamps();

            $table->unique(['header_id','dp_amount','tenor_months']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_items');
    }
};
