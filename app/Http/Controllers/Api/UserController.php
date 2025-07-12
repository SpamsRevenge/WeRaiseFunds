<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Fundraiser;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordResetRequest;

use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
	
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
  	$super_admin_role = app('global.global_super_admin_role_id');
  	$Users = User::with([ 'userInfo', 'userHasRole'])->whereHas('userHasRole', function ($query) use($super_admin_role) {
  		$query->where('role_id','!=', $super_admin_role);
  	})->get();
  	if(!$Users){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Users not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'Users fetched successfully.',
  			'data' => $Users,
  		]);
  	}
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
  	return 'added';
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
  	return 'fetched';
  }

  /**
   * Update the specified resource in storage.
   */
  public function update( Request $request )
  {


 // $name = $request->file('avatar');
 // print_r($request->file('avatar'));
   // $address_1 = $request->input('address_1');
   // $address_2 = $request->input('address_2');
   // $city = $request->input('city');
   // $avatar = $request->file('avatar');


// return response()->json([
//   			'status' => 'failed',
//   			'message' => 'User notw found.',
//   			'data' => '',
//   		]);

  	$user = User::where('id', $request->user()->id )->with('userInfo')->first();
		// print_r($user);die;
  	if($user){
  		$userProfile = UserProfile::where( 'user_id',$request->user()->id )->first();
  		if(!$userProfile){
  			$userProfile = new UserProfile();
  		}
  		$user->name = $request->name;
  		$user->save();
  		if($userProfile){
  			$userProfile->user_id = $request->user()->id;
  			$userProfile->fill($request->all());
  			if($request->file('avatar')){
  				$uploadImage = Fundraiser::upload( $request, 'avatar', 'users' );
  				$userProfile->avatar = $uploadImage;
  			}
  			$userProfile->save();
  		}
  		$user = User::where('id', $request->user()->id )->with('userInfo')->first();
  		return response()->json([
  			'status' => 'success',
  			'message' => 'User updated successfully.',
  			'data' => $user,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'User not found.',
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

  public function profile(Request $request){
  	// $peofile = User::select('*', DB::raw("SUBSTRING_INDEX(name, ' ', 1) AS first_name,  SUBSTRING_INDEX(name, ' ', -1) AS last_name"))->where('id',$request->user()->id)->with([ 'userInfo', 'userHasRole' , 'parent' ])->first();
  	$peofile = User::where('id',$request->user()->id)->with([ 'userInfo', 'userHasRole' , 'parent' ])->first();
  	if(!$peofile){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'User not found.',
  			'data' => null,
  		]);
  	}else{
  		return response()->json([
  			'status' => 'success',
  			'message' => 'User fetched successfully.',
  			'data' => $peofile,
  		]);
  	}
  }
  public function sendResetPasswordEmail( Request $request ){
  	$validator = Validator::make($request->all(), [
  		'email' => 'required|email',
  	]);
  	if ($validator->fails()) {
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Email not found.',
  			'data' => null,
  		]);
  	}
  	$user = User::where ('email', $request->email)->first();
  	if ( !$user ){
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Email not found.',
  			'data' => null,
  		]);
  	}
  	$fd =   $user->getEmailForPasswordReset();
  	if($fd){  
  		$token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);
  		$user->notify(
  			new PasswordResetRequest($token, $request->email)
  		);
  	}
  	return response()->json([
  		'status' => 'success',
  		'message' => 'Reset password email sent successfully.',
  		'data' => null,
  	]);

  }

  public function passwordUpdate( Request $request ){
  	$validator = Validator::make($request->all(), [
  		'email' => 'required|email',
  		'password' => 'required|confirmed',
  		'token' => 'required'
  	]);
  	if ($validator->fails()) {
  		return response()->json([
  			'status' => 'failed',
  			'message' => 'Email not found.',
  			'data' => $validator->errors(),
  		]);
  	}
  	$status = Password::reset(
  		$request->only('email', 'password', 'password_confirmation', 'token'),
  		function ($user, $password) use ($request) {
  			$user->forceFill([
  				'password' => Hash::make($password)
  			])->setRememberToken(Str::random(60));

  			$user->save();

  			event(new PasswordReset($user));
  		}
  	);
  	// return $status == Password::PASSWORD_RESET
  	// ? response()->json([
  	// 	'status' => 'success',
  	// 	'message' => 'Password updated successfully.',
  	// 	'data' => null,
  	// ])
  	// : response()->json([
  	// 	'status' => 'failed',
  	// 	'message' => 'Failed to update password.',
  	// 	'data' => null,
  	// ]);
  	switch ($status) {
  		case Password::PASSWORD_RESET:
  		return response()->json([
  			'message' => 'Password has been successfully changed',
  			'status' => 'success',
  			'data' => null
  		]);
  		case Password::INVALID_TOKEN:
  		return response()->json([
  			'message' => 'Invalid token provided.',
  			'status' => 'failed',
  			'data' => null
  		]);
  		case Password::INVALID_USER:
  		return response()->json([
  			'message' => 'No user could be found with the provided email address',
  			'status' => 'failed',
  			'data' => null
  		]);
  		case Password::RESET_THROTTLED:
  		return response()->json([
  			'message' => 'Too many password reset attempts. Please try again later.',
  			'status' => 'failed',
  			'data' => null
  		]);
  		default:
  		return response()->json(['error' => 'An unknown error occurred. Please try again.'],  500);	return response()->json([
  			'message' => 'An unknown error occurred. Please try again.',
  			'status' => 'failed',
  			'data' => null
  		]);
  	}
  	return $status;

  	

  }
}
