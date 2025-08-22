<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('order')->nullable();
            $table->string('tax_number')->nullable(); // npwp
            $table->string('price_status');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('city_id');
            $table->string('ranking')->nullable();
            $table->string('service')->nullable();
            $table->text('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('url')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('phone3')->nullable();
            $table->string('fax')->nullable();
            $table->string('wanda_dealer_id')->nullable();
            $table->string('wanda_api_key')->nullable();
            $table->string('wanda_api_secret')->nullable();
            $table->string('ahass_code')->nullable();
            $table->timestamps();

            $table->foreign('area_id')->references('id')->on('branch_locations')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('branch_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
