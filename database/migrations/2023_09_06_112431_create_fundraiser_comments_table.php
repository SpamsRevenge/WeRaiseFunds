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
		Schema::create("fundraiser_comments", function (Blueprint $table) {
			$table->id();
			$table->string("name")->nullable();
			$table->string("email")->nullable();
			$table->string("fundraiser_id")->nullable();
			$table->text("comment")->nullable();
			$table->string("donation_id")->nullable();
			$table->string("status")->nullable();
			$table->timestamps();
		});
	}

	/**
	* Reverse the migrations.
	*/
	public function down(): void
	{
		Schema::dropIfExists("fundraiser_comments");
	}
};
