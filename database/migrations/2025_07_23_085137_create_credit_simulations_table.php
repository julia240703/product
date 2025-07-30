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
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('province');
            $table->string('city');
            $table->foreignId('motor_category_id')->constrained('motor_categories')->onDelete('cascade');
            $table->string('motor_type');
            $table->string('motor_variant');
            $table->bigInteger('otr_price');
            $table->bigInteger('down_payment');
            $table->integer('tenor'); // bulan
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
