<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
  use HasFactory;
  protected $table = 'orders';

  public function User(){
    return $this->belongsTo(User::class,'seller_id');
  }
  public function Fundraiser(){
    return $this->belongsTo(Fundraiser::class,'fundraiser_page_id');
  }
}
