<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
	use HasFactory;

	public function Order(){
		return $this->belongsTo(Order::class,'order_id');
	}
	public function Fundraiser(){
		return $this->belongsTo(Fundraiser::class,'fundraiser_id');
	}
}
