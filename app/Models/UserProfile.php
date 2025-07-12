<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
	use HasFactory;

	protected $table = 'user_profile';

  /**
	* The attributes that are mass assignable.
	*
	* @var array<int, string>
	*/
	protected $hidden = [
		'payment_api_key',
		'payment_api_secret'
	];


	protected $fillable = [
		'address_1',
		'address_2',
		'city',
		'state',
		'zip',
		'country',
		'mobile',
		'telephone',
		'avatar',
		'status',
		'bio',
		'fee_label',
		'fee_type',
		'fee_amount',
		'fee_status',
		'jetpay_fee_type',

		'fee_2_label',
		'fee_2_type',
		'fee_2_amount',
		'fee_2_status',
		'jetpay_fee_2_type'
	];

	public function User()
	{
		return $this->belongsTo( User::class );
	}
}
