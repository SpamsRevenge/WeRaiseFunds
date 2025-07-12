<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class UserInfo extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\UserInfo>
   */
  public static $model = \App\Models\UserProfile::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'id';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
  	'id',
  ];

  /**
   * Get the fields displayed by the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function fields(NovaRequest $request)
  {
  	return [
  		ID::make()->sortable(),

  		BelongsTo::make('User')
  		->searchable(),

  		Image::make('Avatar')->path('profile_images'),

  		Trix::make('User Bio','bio'),

  		Text::make('Address 1')->hideFromIndex(),

  		Text::make('Address 2')->hideFromIndex(),

  		Text::make('City'),

  		Text::make('State'),

  		Text::make('Zip')->hideFromIndex(),

  		// Country::make('Country')->hideFromIndex()->hideFromIndex(),

  		Text::make('Telephone')->textAlign('left')->hideFromIndex(),

  		Text::make('Mobile')->textAlign('left'),

		Select::make("Status", 'profile_status')->searchable()->options([
			'active' => 'Active',
			'inactive' => 'Inactive',
		])->displayUsingLabels()->filterable(),

		Panel::make('Admin Info', [

	  		Textarea::make('Fee Label'),

	  		Select::make('Admin Fee Type', 'fee_type')
	  		->options([
	  			'dollar' => '$',
	  			'percentage' => '%'
	  		]),

	  		Number::make('Fee Amount')
	  		->step(0.01),

	  		Select::make('Fee Status', 'fee_status')
	  		->options([
	  			'enabled' => 'Enabled',
	  			'disabled' => 'Disabled'
	  		]),

	  		Select::make('Jetpay Fee Type', 'jetpay_fee_type')
	  		->options([
	  			'CNV' => 'Convenience',
	  			'SRG' => 'Surcharge',
	  			'SRV' => 'Service'
	  		]),
	  		

	  		Textarea::make('Fee 2 Label'),

	  		Select::make('Admin Fee 2 Type', 'fee_2_type')
	  		->options([
	  			'dollar' => '$',
	  			'percentage' => '%'
	  		]),

	  		Number::make('Fee 2 Amount')
	  		->step(0.01),

	  		Select::make('Fee 2 Status', 'fee_2_status')
	  		->options([
	  			'enabled' => 'Enabled',
	  			'disabled' => 'Disabled'
	  		]),

	  		Select::make('Jetpay Fee 2 Type', 'jetpay_fee_2_type')
	  		->options([
	  			'CNV' => 'Convenience',
	  			'SRG' => 'Surcharge',
	  			'SRV' => 'Service'
	  		]),

	  		Text::make('Payment MID Key','payment_api_key'),

	  		Text::make('Payment TID Key','payment_api_secret'),
		]),
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
  	return [];
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
  	// $query = $query->select('roles.name as user_role');
  	if( auth()->user()->hasRole('super-admin') ){
  		return $query;
  	}else if( auth()->user()->hasRole('admin') ){
  		// $query = $query->where('parent_id', $request->user()->id)->orWhere('user_id', $request->user()->id);
  		$users = \App\Models\User::where('parent_id', $request->user()->id)->orWhere('id', $request->user()->id)->whereHas('userHasRole', function($modelQuery) use($fundraiser_manager_role, $school_role){
  			$modelQuery->whereIn('role_id',[$school_role, $fundraiser_manager_role]);
  		})->get()->pluck('id');
  		return $query->whereIn('user_id', $users);
  	}else{

  		$users = \App\Models\User::with('userHasRole')->where('parent_id', $request->user()->id)->orWhere('id', $request->user()->id)->whereHas('userHasRole', function($modelQuery) use( $seller_role){
  			$modelQuery->whereIn('role_id',[$seller_role]);
  		})->get()->pluck('id');
  		return $query->whereIn('user_id', $users);
  	}
  }
}
