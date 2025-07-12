<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Fundraiser extends Model
{
  use HasFactory;
  protected $casts = [
    'admin_id' => 'integer',
    'user_id' => 'integer',
    'fundraiser_category_id' => 'integer',
    'school_id' => 'integer',
    'start_date' => 'date',
    'end_date' => 'date',
  ];

  protected $appends = ['state_slug', 'fundraiser_subtotal', 'fundraiser_fee'];

  /**
  The attributes that are mass assignable.*
  @var array<int, string>*/
  protected $fillable = ['title','sub_title','slug','featured_image','banner_image','admin_id','user_id','fundraiser_category_id','description','short_description','address_line_1','address_line_2','city','state','postalcode','country','team_location','total','total_collected','status','color','start_date','end_date','school_id','fundraiser_type','ticket_max_qty','ticket_price'];

  public function allSchools(){
    return $this->belongsTo(User::class, 'school_id');
  }

  public function allManager(){
    return $this->belongsTo(User::class, 'user_id');
  }

  public function allAdmin(){
    return $this->belongsTo(User::class, 'admin_id')->with('userInfo');
  }

  public function FundraiserComment(){
    return $this->hasMany(FundraiserComment::class,'fundraiser_id');
  }

  // public function fundraiserCategories()
  // {
  //   return $this->hasMany(FundraiserCategory::class, 'id');
  // }

  public function fundraiserCategory()
  {
    return $this->belongsTo(FundraiserCategory::class,'fundraiser_category_id','id')->select(['id','title','slug','description']);
  }

  public function FundraiserSeller()
  {
    return $this->hasMany(FundraiserSeller::class, 'fundraiser_id');
  }

  public function Orders()
  {
    return $this->hasMany(Order::class, 'fundraiser_page_id');
  }

  public function Raffles()
  {
    return $this->hasMany(Raffle::class, 'fundraiser_id');
  }

  public function getStateSlugAttribute()
  {
    return strtolower(str_replace(' ', '-', $this->attributes['state']));
  }

  public function getFundraiserSubtotalAttribute()
  {
  	$total = $this->attributes['total_collected'];
  	$total_fee = Order::where('fundraiser_page_id',$this->attributes['id'])->get()->sum(function($t){ 
	    return (($t->order_fee?$t->order_fee:0) + ($t->order_fee_2?$t->order_fee_2:0)); 
	});
    return number_format( ($total - $total_fee), 2);
  }

  public function getFundraiserFeeAttribute()
  {
  	// $total = $this->attributes['total_collected'];
  	$total_fee = Order::where('fundraiser_page_id',$this->attributes['id'])->get()->sum(function($t){ 
	    return (($t->order_fee?$t->order_fee:0) + ($t->order_fee_2?$t->order_fee_2:0)); 
	});
    return number_format( $total_fee, 2 );
  }

  public static function upload($request, $filename, $storePath){
    $path = Storage::disk('public')->putFile( $storePath , $request->file($filename));
    return $path;
  }
}