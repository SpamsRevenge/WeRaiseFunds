<?php
namespace App\Nova;
use App\Models\FundraiserCategory;
use Illuminate\Http\Request;
use App\Nova\Actions\SendEmailWithPopup;
use App\Nova\Actions\GenerateQrCode;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\URL;
// use OptimistDigital\MultiselectField\Multiselect;
use App\Nova\Filters\DateRange;
use Devtical\Qrcode\Qrcode;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\FormData;
use Sietse85\NovaButton\Button;
use App\Nova\Actions\WelcomEmail;

// use Laravel\Nova\Fields\Multiselect;

class Fundraiser extends Resource
{
  /**
  * The model the resource corresponds to.
  *
  * @var class-string<\App\Models\Fundraiser>
  */
  public static $model = \App\Models\Fundraiser::class;

  /**
  * The single value that should be used to represent the resource when being displayed.
  *
  * @var string
  */
  public static $title = "title";

  /**
  * The columns that should be searched.
  *
  * @var array
  */
  public static $search = ["id", "title"];

  /**
  * Get the fields displayed by the resource.
  *
  * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
  * @return array
  */
  public function fields(Request $request)
  {
  	$nextjs_app_url = app('global.nextjs_app_url');

  	$admin_role = app('global.global_admin_role_id');
  	$manager_role = app('global.global_manager_role_id');
  	$school_role = app('global.global_school_role_id');
  	return [
  		ID::make()->sortable()->hideFromIndex(),

  		Qrcode::make('QR Code For Fundraiser', 'front_page_url')
  		->indexSize(100)
  		->detailSize(200)
  		->onlyOnPreview(),

  		Text::make('Download QR', function () {
  			return "<a href='#' class='fundraise-qr border inline-flex items-center justify-center appearance-none cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 relative disabled:cursor-not-allowed shadow h-9 px-3 bg-primary-500 border-primary-500 hover:enabled:bg-primary-400 hover:enabled:border-primary-400 text-white dark:text-gray-900' download>Download Fundraiser QR</a>";
  		})->asHtml()->onlyOnPreview(),

  		Qrcode::make('QR Code For Seller Signup', 'front_page_url', fn () => $this->front_page_url.'/register')
  		->indexSize(100)
  		->detailSize(200)
  		->onlyOnPreview(),

  		Text::make('Seller Signup QR', function () {
  			return "<a href='#' class='seller-qr border inline-flex items-center justify-center appearance-none cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 relative disabled:cursor-not-allowed shadow h-9 px-3 bg-primary-500 border-primary-500 hover:enabled:bg-primary-400 hover:enabled:border-primary-400 text-white dark:text-gray-900' download>Seller QR</a>";
  		})->asHtml()->onlyOnPreview(),

      // Text::make('code_url'),
      // Hidden::make('code_url')->default(Str::random(64))
      // ->showOnPreview(),

  		Text::make('View Public Fundraiser','front_page_url', fn () => $this->front_page_url)
  		->displayUsing( function(){ 
  			$url = $this->front_page_url;
  			return "<a target='_blank' href='{$url}'>{$url}</a>";
  		})
  		->asHtml()
  		->fillUsing(function ($request, $model, $attribute, $requestAttribute) use($nextjs_app_url){
  			$slug = $request->slug;
  			$id = isset($model->id)?$model->id:''; 
  			if($id){
  				$fundslug = \App\Models\Fundraiser::where('id',$id)->first();
  				$slug = $fundslug->slug;
  			}
  			$state = strtolower(str_replace(' ', '-', $request->state));
  			$category = \App\Models\FundraiserCategory::where('id',$request->fundraiserCategory)->first();
  			$catSlug = $category->slug;
        // return  ;
  			$model->{$attribute} = $nextjs_app_url.''.$catSlug. '/' .$state. '/' .$slug ;
  		})
  		->showOnPreview()
  		->hideFromIndex()
  		->hideFromDetail()
  		->withMeta([
  			'extraAttributes' => [
  				'class' => 'front_page_url_custom',
  			],
  		]),


  		Text::make('Seller Signup',  fn () => $this->front_page_url.'/register')
  		->asHtml()
  		->displayUsing( function(){  
  			$url = $this->front_page_url.'/register';
  			return "<a target='_blank' href='{$url}'>{$url}</a>";
  		})->onlyOnPreview(),       

      // Text::make('Seller Signup', function () {
      // 	return "<a href='".$this->front_page_url."/register' target='_blank'>".$this->front_page_url."/register</a>";
      // })->asHtml()->onlyOnPreview(),

  		Text::make("Title")
  		->sortable()
  		->rules("required"),
      // ->showOnPreview(),

      // ->filterable(function ($request, $query, $value, $attribute) {
      //   $query->where($attribute, 'LIKE', "{$value}%");
      // }),

  		Text::make("Sub Title")->hideFromIndex(),

  		Slug::make("Slug")
  		->from("Title")
  		->separator('-')
  		->creationRules('unique:fundraisers,slug')
      // ->updateRules('unique:fundraisers,slug,{{resourceId}}')
  		->hideWhenUpdating()
  		->rules("required"),

  		Image::make("Featured Image", 'banner_image')
  		->help('Dimensions should be 400X300')
  		->path("fundraiser")
  		->hideFromIndex(),

  		Image::make("Donation Card Image", 'featured_image')
  		->help('Dimensions should be 800X500')
  		->path("fundraiser")->hideFromIndex(),

  		belongsTo::make('Admin', 'allAdmin', 'App\Nova\User')
      // ->searchable()
      // ->showCreateRelationButton()
  		->modalSize('3xl')
  		->relatableQueryUsing(function (NovaRequest $request, $query) use( $admin_role ) {
  			if( $request->user()->hasRole('admin') ){
  				$query = $query->where('id',$request->user()->id);
  			}else{
  				$query = $query->whereHas( 'userHasRole', function($modelQuery) use( $request, $admin_role ){
  					return $modelQuery->where('role_id',$admin_role);
  				});
  			}
  			return $query;
  		})
  		->default(function ($request) {
  			if ($request->user()->hasRole('admin')) {
  				return $request->user()->id;
  			}
  		})
      // ->nullable()
  		->filterable(),

  		belongsTo::make('School', 'allSchools', 'App\Nova\User')
      // ->searchable()
  		->showCreateRelationButton()
  		->relatableQueryUsing(function (NovaRequest $request, $query) use ($school_role){
  			return $query->whereHas( 'userHasRole', function($modelQuery) use($school_role){
  				$modelQuery->where('role_id', $school_role);
  			});
  		})
  		->nullable()
  		->filterable(),

  		belongsTo::make('Manager', 'allManager', 'App\Nova\User')
      // ->searchable()
  		->showCreateRelationButton()
  		->modalSize('3xl')
  		->relatableQueryUsing(function (NovaRequest $request, $query) use ($manager_role){
  			return $query->whereHas( 'userHasRole', function($modelQuery) use($manager_role){
  				$modelQuery->where('role_id', $manager_role);
  			});
  		})
      // ->nullable()
  		->filterable(),

  		Currency::make('Total')
  		->locale('en')
  		->min(1)
  		->step(1)
  		->rules("required"),

  		Currency::make('Total Collected')
  		->locale('en')
  		->step(1),


  		Button::make('View Donation Page')
  		->style('success')
  		->link( ($this->front_page_url?$this->front_page_url:'') , '_blank')
  		->onlyOnIndex()
  		->hideWhenCreating()
  		->hideWhenUpdating(),

  		Date::make('Start Date')->filterable(),

  		Date::make('End Date')->filterable(),

  		belongsTo::make('Category', 'fundraiserCategory', 'App\Nova\FundraiserCategory')
      // ->nullable()
  		->rules("required")
  		->hideFromIndex(),

  		HasMany::make("FundraiserSeller"),

  		HasMany::make('Orders'),

  		HasMany::make('Raffles'),

  		Select::make('Fundraiser Type','fundraiser_type')->options([
  			'donation' => 'Donation',
  			'raffle' => 'Raffle',
  		])->rules("required")->hideFromIndex(),



  		Text::make('Raffles Ticket Quantity', 'ticket_max_qty')
  		->readonly()
  		->dependsOn(['fundraiser_type'], function ($value, NovaRequest $request, FormData $formData) {
  			if ($request->input('fundraiser_type') === 'raffle') {
  				$value->readonly(false)->rules(['required']);
  			}
  		}),


  		Currency::make('Raffles Ticket Price', 'ticket_price')
  		->locale('en')
  		->min(1)
  		->readonly()
  		->step(1)
  		->dependsOn(['fundraiser_type'], function ($value, NovaRequest $request, FormData $formData) {
  			if ($request->input('fundraiser_type') === 'raffle') {
  				$value->readonly(false)->rules(['required']);
  			}
  		}),


  		Button::make('Welcome Email')
  		->title('Refund')
  		->style('danger') 
  		->confirm('Confirmation', 'Are you sure you want to send welcome email.', 'Cancel')
      // ->visible($this->order_status == 'completed')
  		->action(\App\Nova\Actions\WelcomEmail::class)->reload(),

      // //   Currency::make('Total')
      // // ->locale('en')
      // // ->min(1)
      // ->step(1)
      // ->rules("required"),



      // HasMany::make("FundraiserComment"),

  		Trix::make("Description")
  		->help('Please describe the fundraiser in detail to explain what it is used for you.')
  		->hideFromIndex(),

  		// Textarea::make("Short Description")->hideFromIndex(),

  		new Panel("Team Location", $this->addressFields()),

  		Select::make("Status")->searchable()->options([
  			'active' => 'Active',
  			'inactive' => 'Inactive',
  		])->displayUsingLabels()->filterable(),

  		DateTime::make('Created At')->hideWhenCreating()->hideWhenUpdating()->filterable(),

  	];
  }

  protected function addressFields()
  {
  	$us_states = getUSStates();
  	
  	return [
  		Text::make("Address", "address_line_1")->hideFromIndex(),

  		Text::make("Address Line 2")->hideFromIndex(),

  		Text::make("City")->hideFromIndex(),

      // Text::make("State")->hideFromIndex()
      // ->rules("required"),
  		Select::make('State')
  		->options($us_states)
  		->displayUsingLabels()
  		->rules("required")
  		->hideFromIndex(),

  		Text::make("Postal Code", "postalcode")->hideFromIndex(),

      // Country::make("Country")->hideFromIndex(),
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
  		new Filters\DateRange,
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
  	return [
  		// SendEmailWithPopup::make(),
      	// GenerateQrCode::make(),
  	];
  }
  public function rules(Request $request)
  {
  	return [
      // "fundraiser_category_id" => "required|json", // Example rule for array validation
      // Add other validation rules for your fields
  	];
  }

  public static function preview()
  {
      return 'Share QR'; // Change this to your desired text
  }


  public static function creationRules(Request $request)
  {
  	return (new FundraiserRequest())->rules($request);
  }

  /**
  * Get the update validation rules that apply to the request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return array
  */
  public static function updateRules(Request $request, $resource)
  {
  	return (new FundraiserRequest())->rules($request);
  }

  public static function indexQuery(NovaRequest $request, $query)
  {
  	if( auth()->user()->hasRole('super-admin') ){
  		return $query;
  	}
  	return $query->where('admin_id', $request->user()->id);
  }
}
