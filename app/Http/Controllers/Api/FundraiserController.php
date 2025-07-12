<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFundraiser;
use App\Models\Fundraiser;
use App\Models\FundraiserSeller;
use App\Models\FundraiserCategory;
use App\Models\User;
use App\Mail\FundraiserNotification;
use App\Mail\SellerApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\DB;


class FundraiserController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth:sanctum', ['only' => ['create', 'store', 'update', 'destroy']]);
	}
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  { 
  	$funds = Fundraiser::select('*');
  	if($request->category){
  		$funds = $funds->whereHas('fundraiserCategory', function ($query) use ($request) {
  			$query->where('slug', $request->category);
  		});     
  	}
  	if($request->state){
  		$funds = $funds->where('state', $request->state);     
  	}
  	$funds = $funds->with(['fundraiserCategory','allManager','allAdmin' ])->get();
  	if( count($funds) > 0 ){
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraisers fecthed successfully.',
  			'data' => $funds,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found',
  			'data' => null,
  		]);
  	}
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreFundraiser $request)
  {
  	$nextjs_app_url = app('global.nextjs_app_url');

  	$fundraiser = Fundraiser::create($request->all());
    // if($request->new_category_title != ''){
    //  $fundraiser_category = FundraiserCategory::where('slug', (Str::slug($request->new_category_title , "_")))->first();
    //  if(!$fundraiser_category){
    //    $fundraiser_category = new FundraiserCategory();
    //  }
    //  $fundraiser_category->title = $request->new_category_title;
    //  $fundraiser_category->slug = (Str::slug($request->new_category_title , "_"));
    //  $fundraiser_category->status = 'active';
    //  $fundraiser_category->save();
    //  $fundraiser->fundraiser_category_id = $fundraiser_category->id;
    //  $fundraiser->save(); 
    // }
  	$slug = $request->slug;
  	$state = strtolower(str_replace(' ', '-', $request->state));
  	$category = \App\Models\FundraiserCategory::where('id',$request->fundraiser_category_id)->first();
  	$catSlug = '';
  	if($category && isset($category->slug)){
  		$catSlug = $category->slug;
  	}
  	$fundraiser->front_page_url = $nextjs_app_url.$catSlug. '/' .$state. '/' .$slug ;

  	if($request->file('featured_image')){
  		$featured_image = Fundraiser::upload( $request, 'featured_image', 'fundraise' );
  		$fundraiser->featured_image = $featured_image; 
  	}
  	if($request->file('banner_image')){
  		$banner_image = Fundraiser::upload( $request, 'banner_image', 'fundraise' );
  		$fundraiser->banner_image = $banner_image;
  	}

  	$fundraiser->save();
  	if($fundraiser){
  		$fundraisernew = Fundraiser::where('id',$fundraiser->id)->with('fundraiserCategory')->first();
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser created successfully.',
  			'data' => $fundraisernew,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'failed ',
  			'message' => 'Fundraiser not created.',
  			'data' => null,
  		]);
  	}
  }

  /**
   * Display the specified resource.
   */
  public function show(string $slug)
  {
  	$fund = Fundraiser::where('slug', $slug)->with(['fundraiserCategory','allManager','allAdmin'])->first();
  	if(!$fund){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser fetched successfully.',
  			'data' => $fund,
  		]);
  	}

  }

  /**
   * Display the all fundraiser categories.
   */
  public function categories()
  {
  	$fundsCats = FundraiserCategory::select(['id','title','slug','description'])->get();
  	if($fundsCats){
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser categories fetched successfully.',
  			'data' => $fundsCats,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser categories not found.',
  			'data' => null,
  		]);
  	}
  }

  /**
   * Display the specified fundraiser category.
   */
  public function categoryBySlug(string $slug)
  {
  	$fundsCatsBySlug = FundraiserCategory::select(['id','title','slug','description'])->where('slug', $slug)->first();
  	if(!$fundsCatsBySlug){ 
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser category by slug not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser category by slug fetched successfully.',
  			'data' => $fundsCatsBySlug,
  		]);
  	}

  }

  /**
   * Update the specified resource in storage.
   */
  public function update( Request $request, $slug)
  {
  	$nextjs_app_url = app('global.nextjs_app_url');
  	$fundraiser = Fundraiser::where( 'slug', $slug )->with('fundraiserCategory')->first();			

    // print_r($request->file('featured_image'));die;
  	if($fundraiser){
  		if($fundraiser->end_date != '' && $request->end_date != '' && strtotime($fundraiser->end_date) != strtotime($request->end_date) && $fundraiser->end_date_extended != 1){
  			$fundraiser->end_date_old = $fundraiser->end_date;
  			$fundraiser->end_date_extended = 1;
  			$fundraiser->end_date = $request->end_date;
  		}else if($fundraiser->end_date == '' && $request->end_date != '' && $fundraiser->end_date_extended != 1){
  			$fundraiser->end_date = $request->end_date;
  		}
  		$fundraiser->fill( $request->except(['end_date', 'slug']) );

      // if($request->new_category_title != ''){
      //  $fundraiser_category = FundraiserCategory::where('slug', (Str::slug($request->new_category_title , "_")))->first();
      //  if(!$fundraiser_category){
      //    $fundraiser_category = new FundraiserCategory();
      //  }
      //  $fundraiser_category->title = $request->new_category_title;
      //  $fundraiser_category->slug = (Str::slug($request->new_category_title , "_"));
      //  $fundraiser_category->status = 'active';
      //  $fundraiser_category->save();
      //  $fundraiser->fundraiser_category_id = $fundraiser_category->id;
      //  $fundraiser->save(); 
      // }
  		$slug = $fundraiser->slug;
  		$state = strtolower(str_replace(' ', '-', $fundraiser->state));
  		$category = \App\Models\FundraiserCategory::where('id',$fundraiser->fundraiser_category_id)->first();
  		$catSlug = '';
  		if($category && isset($category->slug)){
  			$catSlug = $category->slug;
  		}
  		$fundraiser->front_page_url = $nextjs_app_url.$catSlug. '/' .$state. '/' .$slug ;
  		if($request->file('featured_image')){
  			$featured_image = Fundraiser::upload( $request, 'featured_image', 'fundraise' );
  			$fundraiser->featured_image = $featured_image;
  			$fundraiser->save(); 
  		}
  		if($request->file('banner_image')){
  			$banner_image = Fundraiser::upload( $request, 'banner_image', 'fundraise' );
  			$fundraiser->banner_image = $banner_image;
  		}
  		$fundraiser->save();	
  		$fundraiserUpdated = Fundraiser::where('id',$fundraiser->id)->with('fundraiserCategory')->first();

  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser updated successfully.',
  			'data' => $fundraiserUpdated,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found.',
  			'data' => null,
  		]);
  	}
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
  //
  }

  /**
   * Display a listing of the fundraiser by user.
   */
  public function fundraiserByUser(Request $request)
  { 
  	$funds = Fundraiser::select('*');
  	if($request->category){
  		$funds = $funds->whereHas('fundraiserCategory', function ($query) use ($request) {
  			$query->where('slug', $request->category);
  		});     
  	}
  	if($request->state){
  		$funds = $funds->where('state', $request->state);     
  	}
  	if($request->user()->hasRole('school')){
  		$funds = $funds->where('school_id', $request->user()->id );
  	}else if( $request->user()->hasRole('seller') ){
  		$funds = $funds->whereHas('FundraiserSeller', function ($query) use ($request) {
  			$query->where('user_id', $request->user()->id );
  		});     
  	}else if($request->user()->hasRole('admin') || $request->user()->hasRole('super-admin')){
      // $funds = $funds->where('user_id', $request->user()->id );
  	}else{
  		$funds = $funds->where('user_id', $request->user()->id );
  	}
  	$funds = $funds->with(['fundraiserCategory','allManager'])->get();
  	if(!$funds){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser for user not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser by user fetched successfully.',
  			'data' => $funds,
  		]);
  	}
  }

  /**
   * Display a listing of the fundraiser by user.
   */
  public function fundraiserCategoriesByUser(Request $request)
  { 
  	$fundsCats = FundraiserCategory::with('fundraiser')->get(); 
  	if(!$fundsCats){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser categories not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Fundraiser categories fetched successfully.',
  			'data' => $fundsCats,
  		]);
  	}
  }
  /**
   * Display the specified resource.
   */
  public function fundraiserSellers(Request $request)
  {
  	if($request->slug){
  		$fund_ids = Fundraiser::where( 'slug', $request->slug )->get()->pluck('id');
  		// die('dwqedwqdwq');
  	}else{
  		// die('dsf');
  		$userId = request()->user()->id;
  		$fund_ids = Fundraiser::where( 'user_id', $userId )->get()->pluck('id');
  	}
  	if($fund_ids){
  		$fund_seller = FundraiserSeller::whereIn('fundraiser_id', $fund_ids);
  		if($request->status){
  			$fund_seller = $fund_seller->where('status', $request->status);
  		}
  		$fund_seller = $fund_seller->with(['User','Fundraiser'])->get();
  		if(!$fund_seller){
  			return response()->json([
  				'status' => 'failed',
  				'message' => 'Fundraiser sellers not found.',
  				'data' => null,
  			]);
  		}else{
  			return response()->json([
  				'status' => 'success',
  				'message' => 'Fundraiser sellers fetched successfully.',
  				'data' => $fund_seller,
  			]);
  		}
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found.',
  			'data' => null,
  		]);
  	}

  }
  /**
   * Display the specified resource.
   */
  public function schoolFundraiserManagers(Request $request)
  {
  	$fund_managers = Fundraiser::where( 'school_id', $request->user()->id )->get()->pluck('user_id');
  	if($fund_managers){
  		$fund_managers_all = User::whereIn('id',$fund_managers)->get();
  	}
  	if(!$fund_managers){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Schools Fundraiser Managers not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Schools Fundraiser Managers fetched successfully.',
  			'data' => $fund_managers_all,
  		]);
  	}
  }

  /**
   * Sent out a notification to all sellers in the specified fundraiser.
   */
  public function sendFundraiserNotification(Request $request)
  {
  	$fundraiser = Fundraiser::where( 'slug', $request->slug )->first();
  	if($fundraiser){
  		$sellers = FundraiserSeller::where( 'fundraiser_id',$fundraiser->id )->get()->pluck('user_id');
  		if($sellers){
  			$sellerEmails = User::whereIn( 'id', $sellers )->get()->pluck('email');
  			if( count($sellers) > 0 ){
  				Mail::to( 'noreply@weraisefunds.org' )->bcc($sellerEmails)->send( new FundraiserNotification($request) );
  				return response()->json([
  					'status' => 'success',
  					'message' => 'Fundraiser Notification sent.',
  					'data' => $sellerEmails,
  				]);
  			}else{
  				return response()->json([
  					'status' => 'failed',
  					'message' => 'User not found.',
  					'data' => null,
  				]);
  			}
  		}else{
  			return response()->json([
  				'status' => 'failed',
  				'message' => 'Fundraiser seller not sent.',
  				'data' => null,
  			]);
  		}
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found.',
  			'data' => null,
  		]);
  	}
  }
  public function updateSellerFundraiser(Request $request)
  {
    // if($request->username){
    //   $user = User::where('username',$request->username)->first();
    // }
    // if(!$user){
    //   $user = $request->user();
    // }
  	$fundraiser_seller_id = $request->fundraiser_seller_id;
    // $fundraiser = Fundraiser::where('slug', $slug)->first();
    // if($fundraiser){
  	$findFundSeller = FundraiserSeller::where('id', $fundraiser_seller_id)->with(['fundraiser','user'])->first();
  	if($findFundSeller){
  		if( $request->amount_to_raise && is_numeric($request->amount_to_raise) ){
  			$findFundSeller->amount_to_raise = $request->amount_to_raise;
  		}
  		if($request->status){
  			if($request->status != $findFundSeller->status && $request->status == 'approved'){
  				$sellerEmail = User::where( 'id', $findFundSeller->user_id )->first();
  				Mail::to( $sellerEmail->email )->send( new SellerApproved($findFundSeller) );
  			}
  			$findFundSeller->status = $request->status;

  		}
  		if($request->seller_bio){
  			$findFundSeller->seller_bio =  $request->seller_bio;
  		}
  		$findFundSeller->save();
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Sellers fundraiser updated successfully.',
  			'data' => $findFundSeller,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Sellers fundraiser not found.',
  			'data' => null,
  		]);
  	}
    // }else{
    //   return response()->json([
    //    'status' => 'failed',
    //    'message' => 'Fundraiser not found.',
    //    'data' => null,
    //  ]);
    // }
  }

  public function getSellerFundraiser(Request $request)
  {
  	$slug = $request->slug;
  	$username = $request->username;

  	$user = User::where( 'username', $username )->first();
  	$fundraiser = Fundraiser::where('slug', $slug)->first();
  	if($fundraiser && $user){
  		$findFundSeller = FundraiserSeller::where('fundraiser_id', $fundraiser->id)->where('user_id',$user->id)->with(['user','fundraiser'])->first();
  		if($findFundSeller){
  			return response()->json([
  				'status' => 'success',
  				'message' => 'Sellers fundraiser fetched successfully.',
  				'data' => $findFundSeller,
  			]);
  		}else{
  			return response()->json([
  				'status' => 'failed',
  				'message' => 'Sellers fundraiser not found.',
  				'data' => null,
  			]);
  		}
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found.',
  			'data' => null,
  		]);
  	}
  }

  public function getSellerFundraiserAll(Request $request)
  {
  	$slug = $request->slug;
  	$status = $request->status;
  	$user = User::where( 'id', $request->user()->id )->first();
  	// print_r($user);
  	if( $user ){
  		$findFundSeller = FundraiserSeller::select('*');
  		if($request->user()->hasRole('seller')){
  			if($slug){
  				$fundraiser = Fundraiser::where('slug', $slug)->first();
  				if($fundraiser){
  					$findFundSeller = $findFundSeller->where('fundraiser_id', $fundraiser->id);
  				}
  			}
  			$findFundSeller = $findFundSeller->where('user_id',$user->id);
  		}else{
  			if($slug){
  				$fundraiser = Fundraiser::where('slug', $slug)->get()->pluck('id');
  			}else{
  				$fundraiser = Fundraiser::where('user_id', $user->id)->get()->pluck('id');
  			}
  			if($status){
  				$findFundSeller = $findFundSeller->where('status', $status);
  			}
  			$findFundSeller = $findFundSeller->whereIn('fundraiser_id', $fundraiser);
  		}

  		$findFundSeller = $findFundSeller->with(['fundraiser','user'])->get();
  		if($findFundSeller){
  			return response()->json([
  				'status' => 'success',
  				'message' => 'Sellers all fundraiser fetched successfully.',
  				'data' => $findFundSeller,
  			]);
  		}else{
  			return response()->json([
  				'status' => 'failed',
  				'message' => 'Sellers fundraiser not found.',
  				'data' => null,
  			]);
  		}
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Fundraiser not found.',
  			'data' => null,
  		]);
  	}
  }
}
