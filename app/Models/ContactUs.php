<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
	use HasFactory;

  /**
  The attributes that are mass assignable.*
  @var array<int, string>*/
  protected $fillable = ['name','school','program_name','city','state','program_start_date','501c','other_details','referer_url'];

}
