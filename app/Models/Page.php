<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fundraiser;
use App\Models\Blog;

class Page extends Model
{
	use HasFactory;
	// protected $casts = [
  //   'data->funndraisers_near_you' => 'array',
  // ];
	// public function fundraisers(){
	// 	return $this->belongsTo( Fundraiser::class ,'data->funndraisers_near_you','id');
	// }
	// public function blogs(){
	// 	return $this->hasMany( Blog::class ,'id','data->featured_blogs');
	// }

}