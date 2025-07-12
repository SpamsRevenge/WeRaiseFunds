<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Trix;
use Devtical\Qrcode\Qrcode;
use Laravel\Nova\Fields\URL;
use App\Models\User;
use App\Nova\Metrics\FundsRaised;

class FundraiserSeller extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\FundraiserSeller>
   */
  public static $model = \App\Models\FundraiserSeller::class;

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
  	'id','seller_first_name','seller_last_name','seller_email',
  ];

  /**
   * Get the fields displayed by the resource.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function fields(NovaRequest $request)
  {
  	$nextjs_app_url = app('global.nextjs_app_url');

  	$seller_role = app('global.global_seller_role_id');
  	return [
  		
  		ID::make()->sortable(),

  		Qrcode::make('QR Code For Seller', 'front_page_url')
  		->indexSize(100)
  		->detailSize(300)
  		->onlyOnPreview(),

  		Text::make("Download QR", function () {
  			return "<a href='#' class='fundraise-qr border inline-flex items-center justify-center appearance-none cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 relative disabled:cursor-not-allowed shadow h-9 px-3 bg-primary-500 border-primary-500 hover:enabled:bg-primary-400 hover:enabled:border-primary-400 text-white dark:text-gray-900' download>Download Seller Page QR</a>";
  		})->asHtml()->onlyOnPreview(),

  		Text::make('View Public Fundraiser','front_page_url', fn () => $this->front_page_url)
  		->asHtml()
  		->displayUsing( function(){  
  			$url = $this->front_page_url;
  			return "<a target='_blank' href='{$url}'>{$url}</a>";
  		})
  		->fillUsing(function ($request, $model, $attribute, $requestAttribute) use($nextjs_app_url){

  			$fundId = $request->Fundraiser;

  			$fund = \App\Models\Fundraiser::where('id',$fundId)->first();
  			$slug = isset($fund->slug)?$fund->slug:'';

  			$user = \App\Models\User::where('id',$request->User)->first();
  			$username = isset($user->username)?$user->username:'';

  			$state = isset($fund->state)?$fund->state:'';
  			$state = strtolower(str_replace(' ', '-', $state));
  			$cat_id = isset($fund->fundraiser_category_id)?$fund->fundraiser_category_id:'';
  			$category = \App\Models\FundraiserCategory::where('id',$cat_id)->first();
  			$catSlug = isset($category->slug)?$category->slug:'';

  			// return  ;
  			$model->{$attribute} = $nextjs_app_url.''.$catSlug. '/' .$state. '/' .$slug. '/' .$username ;
  		})
  		->showOnPreview()
  		->hideFromIndex()
  		->hideFromDetail()
  		->withMeta([
  			'extraAttributes' => [
  				'class' => 'front_page_url_custom',
  			],
  		]),

  		// Text::make('First Name','seller_first_name'),

  		// Text::make('Last Name','seller_last_name'),

  		// Email::make('Email','seller_email'),

  		// Select::make('User','user_id')->options(
  		// 	function () {
  		// 		return User::select('users.id', 'users.name')->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')->where('model_has_roles.role_id',4)->pluck("users.name", "users.id");
  		// 	},
  		// )->displayUsingLabels(),


  		belongsTo::make('User', 'User', 'App\Nova\User')
  		// ->searchable()
  		->showCreateRelationButton()
  		->modalSize('3xl')
  		->relatableQueryUsing(function (NovaRequest $request, $query) use($seller_role) {
  			return $query->whereHas( 'userHasRole', function($modelQuery) use($seller_role){
  				$modelQuery->where('role_id', $seller_role);
  			});
  		}),

  		belongsTo::make('Fundraiser', 'Fundraiser', 'App\Nova\Fundraiser')
  		->readonly(function ($request) {
  			return $request->isUpdateOrUpdateAttachedRequest();
  		}),
  		// ->searchable()
  		

  		HasMany::make('Orders'),
  		// ->relatableQueryUsing(function (NovaRequest $request, $query) use($seller_role) {
  		// 	return $query->where('seller_id', $request->resourceId);
  		// }),

  		Currency::make('Amount To Raise','amount_to_raise')
  		->locale('en')
  		->min(1)
  		->step(1),

  		Currency::make('Total Collected','total_collected')
  		->locale('en')
  		->min(1)
  		->step(1)
  		->exceptOnForms(),

      // Text::make('Transection ID','transaction_id'),
      // Text::make('Transection Mode','transaction_mode'),
      // new Panel("Donor Address", $this->addressFields()),
      // belongsTo::make("User"),
      // belongsTo::make("Fundraiser"),

      // // $table->text('fundraiser_page_id')->nullable();
      // // $table->text('seller_id')->nullable();
  		
  		Trix::make('Bio','seller_bio'),
  		
  		Select::make("Invite Status")->searchable()->options([
  			'active' => 'Active',
  			'inactive' => 'Inactive',
  		])->displayUsingLabels()->filterable(),
  		
  		Select::make("Status")->searchable()->options([
  			'pending' => 'Pending',
  			'approved' => 'Approved',
  			'declined' => 'Declined',
  		])->displayUsingLabels()->filterable(),


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
  	return [
  		FundsRaised::make()->onlyOnDetail()
		  // (new FundsRaised())->onlyOnDetail(),
  	];
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
  		$funds = \App\Models\Fundraiser::where('admin_id', $request->user()->id)->get()->pluck('id');
  		return $query->whereIn('fundraiser_id', $funds);
  	}else{
  		$funds = [];
  		return $query->whereIn('fundraiser_id', $funds);
  	}
  }
}
