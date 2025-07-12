<?php

namespace App\Observers;

use App\Models\User;
use App\Models\FundraiserSeller;
use Spatie\Permission\Models\Role;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
  /**
   * Handle the User "created" event.
   */
  public function created(User $user): void
  {
  	$roleId = session('user_role');

  	$role = Role::find($roleId);
  	$user->assignRole($role);

     // Forget the user_role attribute from the session
  	session()->forget('user_role');

  	// Mail::to( $user->email )->send( new WelcomeEmail($user,$roleId) );


  }

  /**
   * Handle the User "updated" event.
   */
  public function updated(User $user): void
  {
      //
  }

  /**
   * Handle the User "deleted" event.
   */
  public function deleted(User $user): void
  {
  	// $user->userInfo()->delete();
  	// $user->delete();
  	FundraiserSeller::where('user_id', $user->id)->delete();
  }

  /**
   * Handle the User "restored" event.
   */
  public function restored(User $user): void
  {
      //
  }

  /**
   * Handle the User "force deleted" event.
   */
  public function forceDeleted(User $user): void
  {
  	// $user->userInfo()->delete();
  	// $user->delete();
  }
}
