<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundraiserCategory extends Model
{
	use HasFactory;
	protected $table = 'fundraiser_categories';

	protected $appends = ['cat_slug'];
	/**	
	 * 
	The attributes that are mass assignable.*
	@var array<int, string>*/
	protected $fillable = ['title','sub_title','slug','status','banner_image','logo_image','description'];

	public function fundraiser()
	{
		return $this->hasMany(Fundraiser::class, 'fundraiser_category_id','id');
	}

	public function getCatSlugAttribute()
	{
		return $this->attributes['slug'] . '-fundraising';
	}
}