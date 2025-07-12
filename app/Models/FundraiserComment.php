<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FundraiserComment extends Model
{
  use HasFactory;
  protected $table = 'fundraiser_comments';
  
  public function Fundraiser(){
    return $this->belongsTo(Fundraiser::class,'fundraiser_id', 'id');
  }
}
