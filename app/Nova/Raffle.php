<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;

class Raffle extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Raffle>
     */
    public static $model = \App\Models\Raffle::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'donor_email';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
    	'donor_email',
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

    		BelongsTo::make("Fundraiser")
			// ->searchable()
    		->filterable()
    		->readonly(function ($request) {
    			return $request->isUpdateOrUpdateAttachedRequest();
    		}),

    		BelongsTo::make("Order")
			// ->searchable()
    		->filterable()
    		->readonly(function ($request) {
    			return $request->isUpdateOrUpdateAttachedRequest();
    		}),

    		Text::make('Donor Email','donor_email'),

    		Select::make("Status",'status')->searchable()->options([
    			'active' => 'Active',
    			'inactive' => 'Inactive'
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

	public static function indexQuery(NovaRequest $request, $query)
	{
		if( auth()->user()->hasRole('super-admin') ){
			return $query;
		}
		return $query->whereHas( 'Fundraiser', function($modelQuery) use ($request){
			$modelQuery->where( 'admin_id', auth()->user()->id );
		});
	}
	
}
