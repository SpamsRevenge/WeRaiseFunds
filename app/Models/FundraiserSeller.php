<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FundraiserSeller extends Model
{
	use HasFactory; 


  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
  	'fundraiser_id',
  	'user_id',
  	'amount_to_raise',
  	'total_collected',
  	'invite_status',
  	'status',
  	'seller_bio'
  ];
  protected $hidden = [
  	'seller_first_name',
  	'seller_last_name',
  	'seller_email'
  ];

  public function User(){
  	return $this->belongsTo(User::class,'user_id');
  }
  public function Fundraiser(){
  	return $this->belongsTo(Fundraiser::class,'fundraiser_id')->with(['fundraiserCategory','allManager','allAdmin']);
  }

  public function Orders()
  {
  	return $this->hasMany(Order::class, 'fundraiser_page_id','fundraiser_id')->where('seller_id', $this->user_id);
  }

}
