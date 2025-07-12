<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Role;
use App\Models\Fundraiser;
use App\Models\FundraiserSeller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  /**
   * Handle the incoming request.
   */
  public function __invoke(LoginRequest $request)
  {
  	$request->validate([
  		'email' => 'required|email',
  		'password' => 'required',
  		'device_name' => 'required',
  	]);
  	
  	$user = User::where('email', $request->email)->with('userHasRole')->first();
  	$role_id = isset($user->userHasRole[0])?$user->userHasRole[0]->role_id:'';
  	$role = Role::select('id','name')->find($role_id);
  	if (! $user || ! Hash::check($request->password, $user->password)) {
        // throw ValidationException::withMessages([
        //     'email' => 'The provided credentials are incorrect.',
        //     'status' => 'failed',
        // ]);
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'The provided credentials are incorrect.',
  		]);
  	}else if($user && $role_id){
  		$admin_role = app('global.global_admin_role_id');
  		$superadmin_role = app('global.global_super_admin_role_id');
  		if( $role_id == $admin_role || $role_id == $superadmin_role ){
  			return response()->json([
  				'status' => 'failed',
  				'message' => 'Only Schools, Fundraiser Managers and Sellers can login here.',
  			]);
  		}
  	}
  	// if (Auth::attempt(['email'=> $request->email, 'password'=>$user->password])) {
  		// if (!$user->hasVerifiedEmail()) {
        //   // The user's email has not been verified...
  		// 	return response()->json([
	  	// 		'status' => 'failed',
	  	// 		'message' => 'Email not verified.',
	  	// 	]);
  		// }
  	// }
  	$notificationArray = [];
  	if($user->hasRole('fundraiser-manager')){
  		$fundraiser = Fundraiser::where('user_id', $user->id)->get()->pluck('id');
  		$findFundSeller = FundraiserSeller::where('status','pending')->whereIn('fundraiser_id', $fundraiser)->with(['fundraiser','user'])->get();
  		if($findFundSeller){
  			$notificationArray = ['notification_count' => $findFundSeller->count() ];
  		}
  	}

  	if( $request->slug ){
  		$fundraiser = Fundraiser::where('slug', $request->slug)->first();
  		if($fundraiser){
  			$findFundSeller = FundraiserSeller::where('fundraiser_id', $fundraiser->id)->where('user_id',$user->id)->first();
  			if(!$findFundSeller){
  				$name = explode(' ',$user->name);
  				FundraiserSeller::create([ 'fundraiser_id'=> $fundraiser->id , 'user_id'=> $user->id, 'amount_to_raise'=> (isset($request->amount_to_raise) && is_numeric($request->amount_to_raise)?$request->amount_to_raise:0), 'total_collected'=> '0', 'invite_status'=> 'active', 'status'=>'pending', 'seller_bio'=> $request->seller_bio ]);
  			}
  		}
  	}
  	$responseArray = [
  		'status' => 'success',
  		'message' => 'Logged in successfully.',
  		'token' => $user->createToken($request->device_name)->plainTextToken,
  		'user_id' => $user->id,
  		'user_name' => $user->name,
  		'user_role' => (isset($role)?$role:''),
  		'user_data' => $user
  	];
  	return response()->json(array_merge($responseArray,$notificationArray));
  }
}
