<?php

use Outl1ne\PageManager\NPM;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    $pagesTableName = NPM::getPagesTableName();
    $regionsTableName = NPM::getRegionsTableName();
    
    // Create pages table
    Schema::create($pagesTableName, function (Blueprint $table) use ($pagesTableName) {
      $table->id();
      $table->boolean('active')->default(true); // Active status
      $table->unsignedBigInteger('parent_id')->nullable();// Parent ID
      $table->string('template')->nullable(false);  // Template class
      $table->string('name', 255);
      $table->json('slug')->nullable(); // Translatable slug
      $table->json('seo')->nullable(); // Translatable SEO data
      $table->json('data')->nullable(); // Translatable page data
      $table->timestamps();      // created_at, updated_at
      $table->foreign('parent_id')->references('id')->on($pagesTableName);   // Foreign key
    });

    // Create regions table
    Schema::create($regionsTableName, function (Blueprint $table) {
      $table->id();
      $table->string('template')->nullable(false);
      $table->string('name', 255);
      $table->json('data')->nullable(); // Translatable and modifiable page data
      $table->timestamps();// Created at, updated at
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists(NPM::getPagesTableName());
    Schema::dropIfExists(NPM::getRegionsTableName());
  }
};
