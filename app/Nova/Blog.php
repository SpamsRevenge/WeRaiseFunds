<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\BlogCategory;

class Blog extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Blog>
   */
  public static $model = \App\Models\Blog::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'title';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
  	'id',
  	'title',
  	'content',
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

  		Text::make("Title")
  		->sortable()
  		->rules("required"),

  		Slug::make("Slug")
  		->from("Title")
  		->separator('_')
  		->creationRules('unique:blogs,slug')
  		->updateRules('unique:blogs,slug,{{resourceId}}'),

  		Image::make("Image", 'image')->path("blogs")->hideFromIndex(),

  		MultiSelect::make("Category", "blog_category_id")->options(
  			function () {
  				return BlogCategory::all()->pluck("title", "id");
  			},
  		),

  		Trix::make("content")->hideFromIndex(),

  		Boolean::make("Status")
  		->trueValue("On")
  		->falseValue("Off"),
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
