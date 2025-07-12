<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\FormData;
use App\Models\UserProfile;
use App\Models\Role;
use App\Nova\Filters\RoleFilter;
// use Sereny\NovaPermissions\Nova\Role;
use Illuminate\Support\Str;

use OutOfOffice\PasswordGenerator\PasswordGenerator;

use Illuminate\Support\Facades\Validator;


class User extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\User>
   */
  // public static $model = \App\Models\User::class;

  public static $model = \App\Models\User::class;


  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'name';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
  	'id', 'name', 'email',
  ];
  /**
   * Get Global Roles.
   *
   * @var str
   */
  public static $with = ['parent'];

  /**
   * Get the fields displayed by the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function fields(NovaRequest $request)
  {
  	$fundraiser_manager_role = app('global.global_manager_role_id');
  	$seller_role = app('global.global_seller_role_id');
  	$admin_role = app('global.global_admin_role_id');
  	$school_role = app('global.global_school_role_id');
  	return [

  		ID::make()->sortable(),

  		// Gravatar::make()->maxWidth(50),

  		Text::make('Name')
  		->sortable()
  		->rules('required', 'max:255'),

  		Hidden::make('Username')
  		->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
  			$username = Str::slug(explode(' ',$request->name)[0],'-');

       	// Check if a record with the same username already exists
  			$existingModel = \App\Models\User::where('username','like', $username.'%')->count();

       	// If a record exists, append a number to the username to make it unique
  			if ($existingModel) {
  				// $number = (int)substr($existingModel->username, strlen($username));
  				$username .= '-'.$existingModel;
  			}
  			$model->username = $username;
  		})->hideWhenUpdating(),


  		Email::make('Email')
  		->sortable()
  		->creationRules('unique:users,email')
  		->updateRules('unique:users,email,{{resourceId}}')
  		->rules('required', 'email', 'max:254'),

  		PasswordGenerator::make('Password')
  		->onlyOnForms()
  		->updateRules('nullable', Rules\Password::defaults())
  		->creationRules('required', 'min:8'),

  		Select::make('User Role', 'user_role')
  		->options(function () use($fundraiser_manager_role, $seller_role, $school_role){
  			$role = Role::select('*');
  			if(auth()->user()->hasRole('super-admin')){
  				// super admin condition if any
  			}else if(auth()->user()->hasRole('admin')){
  				$role = $role->whereIn( 'id', [$fundraiser_manager_role,$school_role] );
  			}else{
  				$role = $role->where( 'id', $seller_role );
  			}
  			return $role->get()->pluck("name", "id");
  		})
  		->creationRules('required|exists:roles,id')
  		->showOnCreating()
  		->hideFromIndex()
  		->hideFromDetail()
  		->hideWhenUpdating()
  		->readonly(),

  		BelongsTo::make('Parent', 'parent', 'App\Nova\User')
  		->sortable()
  		->nullable()
  		->relatableQueryUsing(function (NovaRequest $request, $query) use( $admin_role,$fundraiser_manager_role,$school_role ) {
  			if( $request->user()->hasRole('admin') ){
  				return $query->where('id',$request->user()->id);
  			}else{
  				// $query = $query->whereHas( 'userHasRole', function($modelQuery) use( $request, $admin_role,$fundraiser_manager_role,$school_role ){
  				// 	return $modelQuery->whereIn('role_id',[$fundraiser_manager_role,$school_role,$admin_role]);
  				// });
  			}
  			return $query;
  		})
  		->default(function ($request) {
  			if ($request->user()->hasRole('admin')) {
  				return $request->user()->id;
  			}
  		})
  		->dependsOn(
  			['user_role'],
  			function ($field, NovaRequest $request, FormData $formData) {
  				if ($formData->user_role != 1 && $formData->user_role != 2) {
  					$field->creationRules(['required']);
  				}
  			}
  		)  		
  		->updateRules(function ( $model ) {
  			$userRole = \App\Models\User::where('id',$model->resourceId)->with(['userHasRole'])->first();
  			if ($userRole && $userRole->userHasRole && $userRole->userHasRole[0]->role_id != 1 && $userRole->userHasRole[0]->role_id != 2) {
				return ['required'];
			}
  		}),
  		
  		MorphOne::make('User Info'),

  		MorphToMany::make('Roles', 'roles', \Sereny\NovaPermissions\Nova\Role::class)->canSee(function ($request) {
  			return ($request->user())?$request->user()->isSuperAdmin():false;
  		}),

  		HasMany::make("Seller's Fundraiser", 'fundraiserSeller', \App\Nova\FundraiserSeller::class),

  		Text::make('Role', function(){
  			return $this->roles->pluck('name')->implode(', ');
  		})->onlyOnIndex(),

  	];
  }

  /**
   * Get the cards available for the request.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function cards(NovaRequest $request)
  {
  	return [];
  }

  /**
   * Get the filters available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function filters(NovaRequest $request)
  {
  	return [
  		new RoleFilter,
  	];
  }

  /**
   * Get the lenses available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function lenses(NovaRequest $request)
  {
  	return [];
  }

  /**
   * Get the actions available for the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function actions(NovaRequest $request)
  {
  	return [];
  }

  public static function indexQuery(NovaRequest $request, $query)
  {
  	$fundraiser_manager_role = app('global.global_manager_role_id');
  	$seller_role = app('global.global_seller_role_id');
  	$school_role = app('global.global_school_role_id');
  	$admin_role = app('global.global_admin_role_id');
  	// $query = $query->select('roles.name as user_role');
  	if( auth()->user()->hasRole('super-admin') ){
  		return $query;
  	}else if( auth()->user()->hasRole('admin') ){
  		$query = $query->where(function ($query) use ($request) {
  			$query->where('parent_id', $request->user()->id)
  			->orWhere('id', $request->user()->id);
  		});
  		return $query->whereHas('userHasRole', function($modelQuery) use($admin_role, $fundraiser_manager_role, $school_role){
  			$modelQuery->whereIn('role_id',[$admin_role, $school_role, $fundraiser_manager_role]);
  		});
  	}else{
  		return $query->whereHas( 'userHasRole', function($modelQuery) use($seller_role){
  			$modelQuery->where('role_id',$seller_role);
  		});
  	}
  }
  // public static function validatorForCreation(NovaRequest $request)
  // {
  // 	return Validator::make($request->all(), [
  // 		'user_role' => 'required|exists:roles,id',
  // 	]);
  // }

  public static function fillFields(NovaRequest $request, $model, $fields)
  {
    // Store the user_role attribute in the session
  	session(['user_role' => $request->user_role]);

  	return parent::fillFields($request, $model, $fields);
  }

  public function authorizedToView(Request $request)
  {
  	return auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
  }
  
}

