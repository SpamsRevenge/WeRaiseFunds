<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Illuminate\Support\Facades\Blade;
use Outl1ne\PageManager\PageManager;
use Sereny\NovaPermissions\NovaPermissions;
use App\Nova\Permission;
use App\Nova\Role;
use App\Nova\User;
use App\Nova\Fundraiser;
use App\Nova\FundraiserCategory;
use App\Nova\FundraiserComment;
use App\Nova\FundraiserSeller;
use App\Nova\Raffle;
use App\Nova\Blog;
use App\Nova\ContactUsSubmission;
use App\Nova\BlogCategory;
use App\Nova\Order;
use Laravel\Nova\Dashboards\Main;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Outl1ne\MenuBuilder\MenuBuilder;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Panel;
use Outl1ne\MenuBuilder\Nova\Resources\MenuResource;
// use Stepanenko3\NovaSettings\NovaSettingsTool;
// use Stepanenko3\NovaSettings\Resources\Settings;
use Outl1ne\NovaSettings\NovaSettings;
use Outl1ne\NovaMediaHub\MediaHub;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Illuminate\Support\Facades\Auth;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
  /**
  * Bootstrap any application services.
  *
  * @return void
  */
  public function boot()
  {
  	parent::boot();

  	Nova::script('custom', asset('/storage/custom-js/custom.js?v='.time()));

  	Nova::withBreadcrumbs();

	// Settings Page Add
  	\Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
  		new Tabs('Banner Section', [
  			new Tab('General Settings', [
  				Text::make('Site Title', 'site_title'),
  				Textarea::make('Site Description', 'site_description'),
  				Text::make('Default MID Key', 'default_api_key'),
  				Text::make('Default TID Key', 'default_secret_key'),
  				Trix::make('Legal Terms Content'),

  			]),
  			new Tab('Email Settings', [
  				Heading::make('Contact Us Form Admin Notification Email'),
  				Text::make('To Emails', 'contactus_form_submission_admin_notification_emails'),
  				Text::make('Email Subject', 'contact_us_notification_email_subject'),
  				Textarea::make('Email Content', 'contact_us_notification_email_content')
  				->help('{name} {school} {program_name} {city} {state} {program_start_date} {501c} {other_details}'),

  				Heading::make('All Emails'),  				
  				Text::make('Super Admin Emails', 'super_admin_to_emails'),
  				Text::make('Admin Emails', 'admin_to_emails'),

  				Heading::make('New Order Donor Invoice Notification Email'),
  				Text::make('Invoice Email Subject', 'new_order_donor_notification_email_subject'),
  				Textarea::make('Invoice Email Content', 'new_order_donor_notification_email_content')
  				->help('{donor_first_name} {donor_last_name} {donor_email} {order_date} {donation_total} {order_subtotal} {order_fee} {fundraiser_title} {doner_country} {doner_zip} {payment_method}'),

  				Heading::make('New Order Raffle Invoice Notification Email'),
  				Text::make('Invoice Email Subject', 'new_order_raffle_notification_email_subject'),
  				Textarea::make('Invoice Email Content', 'new_order_raffle_notification_email_content')
  				->help('{donor_first_name} {donor_last_name} {donor_email} {order_date} {donation_total} {order_subtotal} {order_fee} {fundraiser_title} {doner_country} {doner_zip} {payment_method} {raffle_qty}'),


  				Heading::make('Order Refund Donor Notification Email'),
  				Text::make('Refund Email Subject', 'oder_refund_notification_email_subject'),
  				Textarea::make('Refund Email Content', 'oder_refund_notification_email_content')
  				->help('{donor_first_name} {donor_last_name} {donor_email} {order_date} {donation_total} {order_subtotal} {order_fee} {fundraiser_title} {doner_country} {doner_zip} {payment_method}'),

  				
  				Heading::make('Fundraiser Seller Notification'),
  				Textarea::make('Fundraiser Seller Notification Email Content', 'fundraiser_seller_notification_email_content')
  				->help('{message} {seller_name}'),

  				Heading::make('New Order Admin Notification Email'),
  				Text::make('To Emails', 'order_admin_notification_emails'),
  				Text::make('Email Subject', 'order_admin_notification_email_subject'),
  				Textarea::make('Email Content', 'order_admin_notification_email_content')
  				->help('{name} {school} {program_name} {city} {state} {program_start_date} {501c} {other_details}'),

  				

  			]),
  			new Tab('Site Header Settings', [
  			// Heading::make('Site Header'),
  				Image::make('Header Logo', 'site_header_logo'),
  			]),	
  			new Tab('Site Footer Settings', [
  			// Heading::make('Site Footer'),
  				Image::make('Footer Logo', 'site_footer_logo'),
  				Text::make('Footer Facebook Link', 'site_footer_facebook_link'),
  				Text::make('Footer Instagram Link', 'site_footer_instagram_link'),
  				Text::make('Footer Twitter Link', 'site_footer_twitter_link'),
  				Text::make('Footer Copyright Text', 'site_footer_copyright_text'),
  			]),		
  		]),		
  	]);

  	\Outl1ne\NovaSettings\NovaSettings::addSettingsFields([  		
  		Panel::make('Super Admin', [
  			Heading::make('Welcome Email'),
  			Text::make('Welcome Email Subject', 'sup_admin_welcome_email_subject'),
  			Textarea::make('Welcome Email Content', 'sup_admin_welcome_email_content')
  			->help('{firstname}'),
  		]),
  	], [], 'Super Admin');
  	
  	\Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
  		Panel::make('Admin Settings', [
  			Heading::make('Welcome Email'),
  			Text::make('Welcome Email Subject', 'admin_welcome_email_subject'),
  			Textarea::make('Welcome Email Content', 'admin_welcome_email_content')
  			->help('{firstname}'),
  		]),
  	], [], 'Admin');
  	
  	\Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
  		Panel::make('School Settings', [
  			Heading::make('Welcome Email'),
  			Text::make('Welcome Email Subject', 'school_welcome_email_subject'),
  			Textarea::make('Welcome Email Content', 'school_welcome_email_content')
  			->help('{firstname}'),
  		]),
  	], [], 'School');
  	
  	\Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
  		Panel::make('Fundraiser Manager Settings', [
  			Heading::make('Welcome Email'),
  			Text::make('Welcome Email Subject', 'fund_manager_welcome_email_subject'),
  			Textarea::make('Welcome Email Content', 'fund_manager_welcome_email_content')
  			->help('{firstname}'),

  			Heading::make('Seller Registered Notification'),
  			Text::make('Seller Registered Email Subject', 'seller_registered_email_subject'),
  			Textarea::make('Seller Registered Email Content', 'seller_registered_email_content')
  			->help('{seller_name} {fundraiser_title}'),
  		]),
  	], [], 'Fundraiser Manager');
  	
  	\Outl1ne\NovaSettings\NovaSettings::addSettingsFields([
  		Panel::make('Seller Settings', [
  			Heading::make('Welcome Email'),
  			Text::make('Welcome Email Subject', 'seller_welcome_email_subject'),
  			Textarea::make('Welcome Email Content', 'seller_welcome_email_content')
  			->help('{firstname}'),

  			Heading::make('Seller Approved Email'),
  			Text::make('Approved Email Subject', 'seller_approved_email_subject'),
  			Textarea::make('Approved Email Content', 'seller_approved_email_content')
  			->help('{seller_name} {fundraiser_title}'),
  		]),
  	], [], 'Seller');
  	
  	Nova::mainMenu( fn($request) => [
  		MenuSection::dashboard(Main::class)->icon("chart-bar"),

  		MenuSection::make("Fundraisers", [
  			MenuItem::resource(Fundraiser::class),
  			MenuItem::resource(FundraiserCategory::class)
  			->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
        // MenuItem::resource(FundraiserComment::class),
  			MenuItem::resource(FundraiserSeller::class),
  			MenuItem::resource(Raffle::class),
  		])
  		->icon("cash")
  		->collapsable(),

  		MenuSection::make("Orders", [
  			MenuItem::resource(Order::class),
        // MenuItem::resource(FundraiserCategory::class),
        // MenuItem::resource(FundraiserComment::class),
  		])
  		->icon("currency-dollar")
  		->collapsable(),

  		MenuSection::make("Resources", [
  			MenuItem::resource(User::class),
  			MenuItem::resource(Role::class)
  			->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::resource(Permission::class)->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::resource(MenuResource::class)->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::link("Media", "/media-hub")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::resource(ContactUsSubmission::class)->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  		])
  		->icon("user-group")
      // ->icon('newspaper')
  		->collapsable(),

  		MenuSection::make("Page Manager", [
  			MenuItem::link("Pages", "/resources/pages"),
  		])
  		->icon("newspaper")
  		->collapsable()
  		->canSee(function ($request) {
  			return ($request->user())?$request->user()->isSuperAdmin():false;
  		}),

        // NovaSettings::make()->canSee(fn ($request) => $request->user()->isSuperAdmin()),

  		MenuSection::make("Blog Manager", [
  			MenuItem::resource(Blog::class),
  			MenuItem::resource(BlogCategory::class),
  		])
  		->icon("book-open")
  		->collapsable()
  		->canSee(function ($request) {
  			return ($request->user())?$request->user()->isSuperAdmin():false;
  		}),

  		MenuSection::make("Settings", [
  			MenuItem::link("General Settings", "/nova-settings")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::link("Super Admin Settings", "/nova-settings/super-admin")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::link("Admin Settings", "/nova-settings/admin")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::link("School Settings", "/nova-settings/school")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::link("Fundraiser Manager Settings", "/nova-settings/fundraiser-manager")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  			MenuItem::link("Seller Settings", "/nova-settings/seller")->canSee(function ($request) {
  				return ($request->user())?$request->user()->isSuperAdmin():false;
  			}),
  		])
  		->icon("cog")
      // ->icon('newspaper')
  		->collapsable(),	
  		
  	]);
  	// NovaSettings::addSettingsFields(function() {
  	// 	return [
  	// 		Text::make('Some setting', 'some_setting'),
  	// 		Number::make('A number', 'a_number'),
  	// 	];
  	// });
  	Nova::footer(function ($request) {
  		return Blade::render('
  			@env(\'prod\')
  			This is production!
  			@endenv
  			');
  	});

  	app()->bind('global.global_super_admin_role_id', function () {
  		return 1;
  	});
  	app()->bind('global.global_admin_role_id', function () {
  		return 2;
  	});
  	app()->bind('global.global_manager_role_id', function () {
      return 3; // please also update manager id in gate() function bellow
  });
  	app()->bind('global.global_seller_role_id', function () {
      return 4; // please also update seller id in gate() function bellow
  });
  	app()->bind('global.global_school_role_id', function () {
      return 5; // please also update school id in gate() function bellow
  });

  	app()->bind('global.nextjs_app_url', function () {
      return 'https://weraisefunds.org/'; // 
  });
  }

  /**
  * Register the Nova routes.
  *
  * @return void
  */
  protected function routes()
  {
  	Nova::routes()
  	->withAuthenticationRoutes()
  	->withPasswordResetRoutes()
  	->register();
  }

  /**
  * Register the Nova gate.
  *
  * This gate determines who can access Nova in non-local environments.
  *
  * @return void
  */
  protected function gate()
  {
  	$super_admin_role = 1;
  	$admin_role = 2;
    // $school_role = 5;
  	Gate::define("viewNova", function ($user) use($super_admin_role, $admin_role){
      // $all_user = \App\Models\User::select('email')->whereHas( 'userHasRole', function($modelQuery) use($super_admin_role, $admin_role){
      //  $modelQuery->whereIn('role_id',[$super_admin_role,$admin_role]);
      // })->get()->pluck('email')->toArray();
      // return in_array($user->email, ($all_user?$all_user:["forte.test.only@gmail.com"]) );
  		return $user->hasAnyRole(['super-admin', 'admin']);
  		// if($user->hasAnyRole(['super-admin', 'admin'])){
  		// 	return true;
  		// }else{
  		// 	Auth::logout(); 
  		// 	return abort(403, 'You do not have permission to access Nova.');
  		// }
  	});
  }

  /**
  * Get the dashboards that should be listed in the Nova sidebar.
  *
  * @return array
  */
  protected function dashboards()
  {
  	return [new \App\Nova\Dashboards\Main()];
    // return [new \App\Nova\Dashboards\FundraiserInsights()];
  }

  /**
  * Get the tools that should be listed in the Nova sidebar.
  *
  * @return array
  */
  public function tools()
  {
  	return [
      // ...
      (new PageManager())->withSeoFields(fn() => []), // Optional
      (new NovaPermissions())->canSee(function ($request) {
      	return ($request->user())?$request->user()->isSuperAdmin():false;
      }),
      NovaPermissions::make("Role")
      ->roleResource(Role::class)
      ->permissionResource(Permission::class)
        // ->disablePermissions()
        // ->hideFieldsFromRole([
        //     'id',
        //     'guard_name'
        // ])
        // ->hideFieldsFromPermission([
        //     'id',
        //     'guard_name',
        //     'users',
        //     'roles'
        // ])
      ->resolveGuardsUsing(function ($request) {
      	return ["web"];
      })
      ->resolveModelForGuardUsing(function () {
      	/** @var App\Auth\CustomGuard $guard */
      	$guard = auth()->guard();
      	return $guard->getProvider()->getModel();
      })->canSee(function ($request) {
      	return ($request->user())?$request->user()->isSuperAdmin():false;
      }),
      // other tools...
      // new \Acme\NovaCustomJs\NovaCustomJs,
      MenuBuilder::make()
        ->title('Site Menus') // Define a new name for sidebar
        ->icon('adjustments') // Customize menu icon, supports heroicons
        ->hideMenu(false), // Hide MenuBuilder defined MenuSection.

        new NovaSettings(),

        MediaHub::make(),

    ];
}

  /**
  * Register any application services.
  *
  * @return void
  */
  public function register()
  {
    //
  }
  protected function scripts()
  {
  	return [
  		asset('js/custom-popup.js'),
  	];
  }
}