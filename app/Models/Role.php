<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  use HasFactory;

  public function users(){
    return $this->belongsToMany( 'App\Models\User', 'model_has_roles', 'role_id', 'model_id');
  }
}
