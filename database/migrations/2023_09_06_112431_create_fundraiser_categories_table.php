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
		Schema::create("fundraiser_categories", function (Blueprint $table) {
			$table->id();
			$table->string("title");
			$table->string("sub_title")->nullable();
			$table->string("slug")->nullable();
			$table->string("logo_image")->nullable();
			$table->string("banner_image")->nullable();
			$table->string("description")->nullable();
			$table->string("status")->nullable();
			$table->timestamps();
		});
	}

  /**
  * Reverse the migrations.
  */
	public function down(): void
	{
		Schema::dropIfExists("fundraiser_categories");
	}
};
