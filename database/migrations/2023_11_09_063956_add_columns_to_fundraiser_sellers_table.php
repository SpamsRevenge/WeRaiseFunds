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
        Schema::table('fundraiser_sellers', function (Blueprint $table) {
            $table->text('seller_bio')->nullable();
            $table->text('front_page_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fundraiser_sellers', function (Blueprint $table) {
            $table->dropColumn("seller_bio");
            $table->dropColumn("front_page_url");
        });
    }
};
