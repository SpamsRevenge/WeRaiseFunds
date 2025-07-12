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
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
    		$table->integer('fundraiser_id')->nullable();
    		$table->integer('order_id')->nullable();       
    		$table->text('donor_email')->nullable();
    		$table->text('position')->nullable();
    		$table->text('status')->nullable();
    		$table->text('giveaway_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffles');
    }
};
