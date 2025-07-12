<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;
use App\Models\Fundraiser;
use Illuminate\Support\Facades\DB;


class TopPerformingAdmin extends Table
{
	public function name()
	{
		return 'Top Performing Admins';
	}
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
    	$fundsarray = [];
    	
    	$fundraisers = Fundraiser::select('admin_id', DB::raw('SUM(total_collected) as total_collected'))
    	->with('allAdmin')
    	->groupBy(['admin_id','total_collected'])
    	->orderByRaw('total_collected * 1 desc')
    	->get();
    	foreach ($fundraisers as $key => $fund) {
    		if($fund->total_collected > 0){
    			$fundsarray[] =  MetricTableRow::make()->icon('check-circle')->iconClass('text-green-500')->title($fund->allAdmin?$fund->allAdmin->name:'Unknown')->subtitle('$'.$fund->total_collected);
    		}
    	}
    	// print_r($fundraisers);
    	return $fundsarray;
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }
}
