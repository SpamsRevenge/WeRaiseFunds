<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
	use HasApiTokens, HasFactory, Notifiable, HasRoles;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
  	'name',
  	'email',
  	'password',
  	'parent_id',
  	'username'
  ];

  protected $appends = ['first_name', 'last_name'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
  	'password',
  	'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
  	'email_verified_at' => 'datetime',
  	'password' => 'hashed',
  ];


  /**
   * Determines if the User is a Super admin
   * @return null
  */
  public function isSuperAdmin()
  {
  	return $this->hasRole('super-admin');
  }

  public function userInfo(){
  	return $this->hasOne( 'App\Models\UserProfile' ,'user_id','id');
  }

  public function userRole(){
  	return $this->hasMany( 'App\Models\Role', 'model_has_roles' ,'model_id','role_id');
  }

  public function fundraiserSeller(){
  	return $this->hasMany( 'App\Models\FundraiserSeller', 'user_id','id');
  }
  
  public function userHasRole(){
  	return $this->hasMany( 'App\Models\ModelHasRole' ,'model_id')->with('userRole');
  }

  public function parent()
  {
  	return $this->belongsTo(User::class, 'parent_id')->with(['userInfo','parent','userHasRole']);
  }
  
  public function getFirstNameAttribute()
  {
  	if(isset($this->attributes['name'])){
  		return explode(' ', $this->attributes['name'])[0];
  	}else{
  		return false;
  	}
  }

  public function getLastNameAttribute()
  {
  	if(isset($this->attributes['name'])){
  		return isset(explode(' ', $this->attributes['name'])[1])?explode(' ', $this->attributes['name'])[1]:'';
  	}else{
  		return false;
  	}
  }
};
