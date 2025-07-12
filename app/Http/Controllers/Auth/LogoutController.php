<?php

namespace App\Http\Controllers\Auth;

use Laravel\Sanctum\PersonalAccessToken;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
  /**
   * Handle the incoming request.
   */
  public function __invoke(Request $request)
  {

  	$user = $request->user();
  	if(!$user){
  		return response()->json([
  			'status' => 'faild',
  			'message' => 'User not found.',
  		]);
  	}
    // print_r($user );
  	$user->tokens()->delete();
  	return response()->json([
  		'status' => 'success',
  		'message' => 'Logged out successfully.',
  	]);
  }
}
