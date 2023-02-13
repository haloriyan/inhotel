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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');

            $table->string('bio')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            $table->string('photo');
            $table->string('cover');

            // Preferences
            $table->string('accent_color');
            $table->string('font_family');
            
            $table->dateTime('premium_until')->nullable();
            $table->string('token')->nullable();
            $table->tinyInteger('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
