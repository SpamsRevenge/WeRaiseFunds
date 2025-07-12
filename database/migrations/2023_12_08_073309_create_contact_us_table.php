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
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
        	$table->string('name')->nullable();
    		$table->string('school')->nullable();
    		$table->string('program_name')->nullable();	
    		$table->string('city')->nullable();
    		$table->string('state')->nullable();
    		$table->string('program_start_date')->nullable();
    		$table->string('501c')->nullable();
    		$table->text('other_details')->nullable();
    		$table->string('referer_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us');
    }
};
