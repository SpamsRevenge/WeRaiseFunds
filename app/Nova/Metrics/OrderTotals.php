<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use App\Models\Order;
use App\Models\Fundraiser;

class OrderTotals extends Value
{

	public function name()
	{
		return 'Total Funds Raised';
	}
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
	  	// $seller = Oreders::find($request->resourceId) : null;
	  	// $targetValue = 1000;
		// return $this->sum($request, Order::class, 'donation_total')->currency('$')->allowZeroResult();
    	$query = Order::query()->where('order_status','completed');
    	if (!$request->user()->hasRole('super-admin')) {
    		$adminFundraisers = Fundraiser::where('admin_id',$request->user()->id)->get()->pluck('id');
    		$query->whereIn('fundraiser_page_id', $adminFundraisers);
    	}
    	$progress = $this->sum($request, $query, 'donation_total')->currency('$')->allowZeroResult();;
	 	// $entries = $this->count($request, Order::class, 'id');
	  	// Append the target value as a suffix to the progress value
	    // $progress->suffix('Total Transactions( '.$entries->value.' )/ Tax Collected( '.$entries->value.' )');
	    // ->format([
	    //   'thousandSeparated' => true,
	    //   'mantissa' => 0,
	  	// ]);
	    // If the card's data is empty, return an empty string instead of the "No Prior Data" message
         // print_r($progress);
    	return $progress;
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
    	return [
    		'TODAY' => Nova::__('Today'),
    		7 => Nova::__('7 Days'),
    		15 => Nova::__('15 Days'),
    		30 => Nova::__('30 Days'),
    		365 => Nova::__('365 Days'),
    		'MTD' => Nova::__('Month To Date'),
    		'QTD' => Nova::__('Quarter To Date'),
    		'YTD' => Nova::__('Year To Date'),
    	];
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
