<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
  public $timestamps = false;

  use HasFactory;
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'role_id',
    'model_type',
    'model_id'
  ];
  public function userRole(){
    return $this->belongsTo( 'App\Models\Role' ,'role_id')->select('id','name');
  }
}
