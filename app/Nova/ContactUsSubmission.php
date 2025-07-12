<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class ContactUsSubmission extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\ContactUs>
   */
  public static $model = \App\Models\ContactUs::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'name';

  // public static $name = 'Contact Us';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
  	'name','school','program_name',
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
  		ID::make()->hideFromIndex(),

  		Text::make("Name")
  		->sortable(),

  		Text::make("School")
  		->sortable(),

  		Text::make("Program Name")
  		->sortable(),

  		Text::make("City")
  		->sortable(),

  		Text::make("State")
  		->sortable(),

  		Text::make("Program Start Date")
  		->sortable(),

  		Boolean::make("Are you a 501c?",'501c')
  		->sortable(),

  		Text::make("Referer Url")
  		->sortable(),

  		Textarea::make("Other Details")
  		->sortable(),
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

  public static function authorizedToCreate(Request $request)
  {
  	return false;
  }

  public function authorizedToUpdate(Request $request)
  {
  	return false;
  }

  public function authorizedToReplicate(Request $request)
  {
  	return false;
  }

}
