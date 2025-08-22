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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_location')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('follow_up')->nullable();
            $table->string('national_id')->nullable();
            $table->string('address')->nullable();
            $table->string('domicile')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('religion')->nullable();
            $table->string('applied_position')->nullable();
            $table->string('landline_phone')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('education')->nullable();
            $table->string('job_status')->nullable();
            $table->string('able_to_work')->nullable();
            $table->string('recruitment_source')->nullable();
            $table->string('cv')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->timestamp('processed_at')->nullable();

            // Foreign Key for users
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Foreign Key for branches
            $table->foreign('branch_location')
                ->references('id')
                ->on('branches')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};