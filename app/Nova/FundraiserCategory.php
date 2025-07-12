<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\HasMany;

class FundraiserCategory extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\fundraiser_Category>
   */
  public static $model = \App\Models\FundraiserCategory::class;

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
  public static $search = ["id"];

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

  		Text::make("Title")
  		->sortable()
  		->rules("required"),

  		Slug::make("Slug")
  		->from("Title")
  		->separator('_')
  		->creationRules('unique:fundraiser_categories,slug')
  		->updateRules('unique:fundraiser_categories,slug,{{resourceId}}')
  		,

  		Text::make("Sub Title"),

  		Textarea::make("Description"),

  		Image::make("Logo", "logo_image")->path("fundraiser-category"),

  		Image::make("Banner Image", "banner_image")->path(
  			"fundraiser-category"
  		),

  		HasMany::make('fundraiser', 'fundraiser', 'App\Nova\fundraiser')
  		->withMeta(['customQuery' => function ($query) {
  			$query->where('fundraiser_category_id', $this->id);
  		}]),
  		
  		Select::make("Status")->searchable()->options([
  			'active' => 'Active',
  			'deactivate' => 'Deactivate',
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
}