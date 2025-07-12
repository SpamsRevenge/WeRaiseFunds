<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
  * Run the migrations.
  */
  public function up(): void
  {
    Schema::create("fundraisers", function (Blueprint $table) {
      $table->id();
      // $table->string('uuid')->unique();
      $table->string("title");
      $table->string("sub_title")->nullable();
      $table->string("slug")->nullable();
      $table->string("featured_image")->nullable();
      $table->string("banner_image")->nullable();
      $table->string("admin_id")->nullable();
      $table->string("user_id")->nullable();
      $table->json("fundraiser_category_id")->nullable();
      $table->string("description")->nullable();
      $table->string("short_description")->nullable();

      $table->string("address_line_1")->nullable();
      $table->string("address_line_2")->nullable();
      $table->string("city")->nullable();
      $table->string("state")->nullable();
      $table->string("postalcode")->nullable();
      $table->string("country")->nullable();

      $table->string("team_location")->nullable();
      $table->string("total")->nullable();
      $table->string("total_collected")->nullable();
      $table->string("status")->nullable();
      $table->timestamps();
    });
  }

  /**
  * Reverse the migrations.
  */
  public function down(): void
  {
    Schema::dropIfExists("fundraisers");
  }
};