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
    		$table->text('fee_2_label')->nullable();
    		$table->text('fee_2_type')->nullable();
    		$table->text('fee_2_amount')->nullable();
    		$table->text('fee_2_status')->nullable();            
    		$table->text('jetpay_fee_2_type')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profile', function (Blueprint $table) {
    		$table->dropColumn("fee_2_label");
    		$table->dropColumn("fee_2_type");
    		$table->dropColumn("fee_2_amount");
    		$table->dropColumn("fee_2_status");
    		$table->dropColumn("jetpay_fee_2_type");
        });
    }
};
