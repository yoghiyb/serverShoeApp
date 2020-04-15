<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('store_name')->nullable();
            $table->string('store_image')->nullable();
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('start_working_time')->nullable();
            $table->string('end_working_time')->nullable();
            $table->string('start_working_days')->nullable();
            $table->string('end_working_days')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
}
