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
    Schema::table('fundraisers', function (Blueprint $table) {
      $table->string('fundraiser_type')->nullable();
      $table->string('ticket_price')->nullable();
      $table->string('ticket_max_qty')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('fundraisers', function (Blueprint $table) {
      $table->dropColumn('fundraiser_type');
      $table->dropColumn('ticket_price');
      $table->dropColumn('ticket_max_qty');
    });
  }
};
