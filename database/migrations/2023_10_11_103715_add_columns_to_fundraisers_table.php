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
      $table->text('color')->nullable();
      $table->text('start_date')->nullable();
      $table->text('end_date')->nullable();
      $table->integer("school_id")->nullable();
      $table->integer("admin_id")->nullable()->change();
      $table->integer("user_id")->nullable()->change();
      $table->integer("fundraiser_category_id")->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('fundraisers', function (Blueprint $table) {
      $table->dropColumn('color');
      $table->dropColumn('start_date');
      $table->dropColumn('end_date');
      $table->dropColumn("school_id");
    });
  }
};
