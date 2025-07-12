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
	    Schema::table('orders', function (Blueprint $table) {
				$table->string('donation_type')->nullable();
				$table->string('raffle_quantity')->nullable();
				$table->string('order_fee_2')->nullable();
				$table->string('creator')->nullable();
	    });	
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
	  Schema::table('orders', function (Blueprint $table) {
	    $table->dropColumn("donation_type");
	    $table->dropColumn("raffle_quantity");
	    $table->dropColumn("order_fee_2");
	    $table->dropColumn("creator");
	  });
	}
};
