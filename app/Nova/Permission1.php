<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Permission extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var class-string<\App\Models\Permission>
   */
  public static $model = \App\Models\Permission::class;

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
  	'id',
  	'name',
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

  		Text::make('Name')
  		->sortable()
  		->rules('required', 'max:255'),

  		Select::make('Group')
  		->sortable()
  		->options([
  			'invoice' => 'Invoice',
  			'client' => 'Client',
  			'contact' => 'Contact',
  			'payment' => 'Payment',
  			'team' => 'Team',
  			'user' => 'User',
  			'role' => 'Role',
  			'permission' => 'Permission',
  		])
  		->rules('required'),
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
