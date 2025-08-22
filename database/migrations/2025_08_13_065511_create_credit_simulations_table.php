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
        Schema::create('credit_simulations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('motor_type_id')->nullable();
            $table->foreign('motor_type_id')
                  ->references('id')
                  ->on('motor_types')
                  ->onDelete('cascade');
            $table->string('motorcycle_variant'); // Varian motor, misalnya Deluxe, Standard
            $table->decimal('otr_price', 15, 2); // Harga on-the-road
            $table->decimal('minimum_dp', 15, 2); // Uang muka minimum
            $table->integer('loan_term'); // Jangka waktu pinjaman dalam bulan
            $table->decimal('interest_rate', 5, 2)->default(0); // Persentase suku bunga
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_simulations');
    }
};
