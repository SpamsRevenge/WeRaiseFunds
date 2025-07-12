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
    	Schema::table('user_profile', function (Blueprint $table) {
    		$table->text('fee_label')->nullable();
    		$table->text('fee_type')->nullable();
    		$table->text('fee_amount')->nullable();
    		$table->text('fee_status')->nullable();
    		$table->text('payment_api_key')->nullable();
    		$table->text('payment_api_secret')->nullable(); 
    		$table->dropForeign(['user_id']);
           	$table->foreign('user_id')
               ->references('id')->on('users')
               ->onDelete('cascade');
    	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    	Schema::table('user_profile', function (Blueprint $table) {
    		$table->dropColumn("fee_label");
    		$table->dropColumn("fee_type");
    		$table->dropColumn("fee_amount");
    		$table->dropColumn("fee_status");
    		$table->dropColumn("payment_api_key");
    		$table->dropColumn("payment_api_secret");
    		$table->dropForeign(['user_id']);
           	$table->foreign('user_id')->references('id')->on('users');
    	});
    }
  };
