<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;
use App\Models\Fundraiser;

class TopPerformingFundraiser extends Table
{
	public function name()
	{
		return 'Top Performing Fundraisers';
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

    	$fundraisers = Fundraiser::select('title','total_collected');
    	if (!$request->user()->hasRole('super-admin')) {
    		$fundraisers = $fundraisers->where('admin_id', $request->user()->id);
    	}
    	$fundraisers = $fundraisers->limit(5)->orderByRaw('total_collected * 1 desc')->get();
    	foreach ($fundraisers as $key => $fund) {
    		if($fund->total_collected > 0){
    			$fundsarray[] = MetricTableRow::make()->icon('check-circle')->iconClass('text-green-500')->title($fund->title)->subtitle('$'.$fund->total_collected);
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
