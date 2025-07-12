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
  	Schema::create('user_profile', function(Blueprint $table) {
  		$table->id();
  		$table->unsignedBigInteger('user_id');
  		$table->foreign('user_id')->references('id')->on('users');
  		$table->text('avatar')->nullable();
  		$table->text('bio')->nullable();
  		$table->text('address_1')->nullable();
  		$table->text('address_2')->nullable();
  		$table->text('city')->nullable();
  		$table->text('state')->nullable();
  		$table->text('country')->nullable();
  		$table->text('zip')->nullable();
  		$table->text('telephone')->nullable();
  		$table->text('mobile')->nullable();
  		$table->text('profile_status')->nullable();
  		$table->timestamp('last_login')->nullable();
  		$table->timestamps();
  	});
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
  	Schema::dropIfExists('user_profile');
  }
};
