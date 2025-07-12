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
    		$table->text('end_date_old')->nullable();
    		$table->integer('end_date_extended')->default(0)->nullable();
    		$table->text('front_page_url')->nullable();
    		$table->text("description")->nullable()->change();
    		$table->text("short_description")->nullable()->change();
    	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    	Schema::table('fundraisers', function (Blueprint $table) {
    		$table->dropColumn("end_date_old");
    		$table->dropColumn("end_date_extended");
    		$table->dropColumn("front_page_url");
    	});
    }
};
