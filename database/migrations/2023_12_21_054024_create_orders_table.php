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
    	Schema::create('orders', function (Blueprint $table) {
    		$table->id();
    		$table->text('fundraiser_page_id')->nullable();
    		$table->text('seller_id')->nullable();
    		$table->text('donor_first_name')->nullable();       
    		$table->text('donor_last_name')->nullable();
    		$table->text('donor_email')->nullable();

    		$table->text('donor_address_1')->nullable();
    		$table->text('donor_address_2')->nullable();
    		$table->text('donor_city')->nullable();
    		$table->text('donor_state')->nullable();
    		$table->text('donor_country')->nullable();
    		$table->text('donor_zip')->nullable();
    		$table->text('donor_mobile')->nullable();

    		$table->text('donation_total')->nullable();
    		$table->text('donation_name')->nullable();
    		$table->text('transaction_id')->nullable();
    		$table->text('transaction_unique')->nullable();
    		$table->text('transaction_mode')->nullable();
    		$table->text('order_status')->nullable();

    		$table->text('order_data')->nullable();
    		$table->text('refund_data')->nullable();
    		$table->text('order_tip')->nullable();
    		$table->text('order_subtotal')->nullable();
    		$table->text('order_fee')->nullable(); 
    		$table->timestamps();
    	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    	Schema::dropIfExists('orders');
    }
  };
