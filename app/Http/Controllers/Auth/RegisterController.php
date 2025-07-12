<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Fundraiser;
use App\Models\ModelHasRole;
use App\Models\FundraiserSeller;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use App\Mail\WelcomeEmail;
use App\Mail\NewSellerRegistered;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
  /**
   * Handle the incoming request.
   */
  public function __invoke(RegisterRequest $request)
  {
  	$user = User::create($request->getData());
  		// print_r($user);
  	if( $user ){

  		$userProfile = UserProfile::where( 'user_id',$user->id )->first();
  		if(!$userProfile){
  			$userProfile = new UserProfile();
  		}
  		if($userProfile){
  			$userProfile->user_id = $user->id;
  			$userProfile->fill($request->all());
  			if($request->file('avatar')){
  				$uploadImage = Fundraiser::upload( $request, 'avatar', 'users' );
  				$userProfile->avatar = $uploadImage;
  			}
  			$userProfile->save();
  		}
  		if( $request->slug ){
  			$fundraiser = Fundraiser::where('slug', $request->slug)->first();
  			if($fundraiser){
  				$name = explode(' ',$user->name);
  				FundraiserSeller::create([ 'fundraiser_id'=> $fundraiser->id , 'user_id'=> $user->id, 'amount_to_raise'=> (isset($request->amount_to_raise) && is_numeric($request->amount_to_raise)?$request->amount_to_raise:0), 'total_collected'=> '0', 'invite_status'=> 'active', 'status'=>'pending', 'seller_bio'=> $request->seller_bio ]);

  				if($fundraiser->user_id){
  					$user->parent_id = $fundraiser->user_id;
  					$user->save();
  				}
  			}
  		}
  		if( $request->role_id ){
        
	  		if( $request->role_id == 4 && $fundraiser ){
	  			$manager = User::where('id',$fundraiser->user_id)->first();
  				Mail::to( $manager->email )->send( new NewSellerRegistered($user,$fundraiser) );
		  	}

  			if($user->email){
		  		if($request->role_id != 3){
  				  Mail::to( $user->email )->send( new WelcomeEmail($user,$request->role_id) );
          }
  			}

  			$role = new ModelHasRole();
  			$role->role_id = $request->role_id;
  			$role->model_id = $user->id;
  			$role->model_type = 'App\Models\User';
  			$role->save();
  		}
  		$username = Str::slug(explode(' ',$request->name)[0],'-');
       	// Check if a record with the same username already exists
  		$existingModel = \App\Models\User::where('username','like', $username.'%')->count();
       	// If a record exists, append a number to the username to make it unique
  		if ($existingModel) {
  			// $number = (int)substr($existingModel->username, strlen($username));
  			$username .= '-'.$existingModel;
  		}
  		$user->username = $username;
  		$user->save();


  		return response()->json([
  			'status' => 'success',
  			'message' => 'User created successfully.',
  			'data' => $user,
  		]);
  	}
  	return response()->json([
  		'status' => 'failed',
  		'message' => 'user not created.',
  	]);
  }
   /**
   * Display a listing of the resource.
   */
  public function checkEmailExists( Request $request )
  {
   	$userCheck = User::select('email')->where('email',$request->email)->first();
   	if(!$userCheck){
   		return response()->json([
   			'status' => 'failed',
   			'message' => 'User not found.',
   			'data' => null,
   		]);
   	}else{
   		return response()->json([
   			'status' => 'success',
   			'message' => 'User fetched successfully.',
   			'data' => $userCheck,
   		]);
   	}
  }
 }
