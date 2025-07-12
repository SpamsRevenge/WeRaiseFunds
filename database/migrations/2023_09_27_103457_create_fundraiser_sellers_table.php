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
  	Schema::create('fundraiser_sellers', function (Blueprint $table) {
  		$table->id();
  		$table->string('seller_first_name')->nullable();
  		$table->string('seller_last_name')->nullable();;
  		$table->string('seller_email')->nullable();;
  		$table->string('fundraiser_id');
  		$table->string('user_id');
  		$table->string('amount_to_raise')->nullable();;
  		$table->string('total_collected')->nullable();;
  		$table->string('invite_status')->nullable();;
  		$table->string('status')->nullable();;
  		$table->timestamps();
  	});
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
  	Schema::dropIfExists('fundraiser_sellers');
  }
};
