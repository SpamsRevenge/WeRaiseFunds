<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use App\Models\Order;
use App\Models\Fundraiser;

class TotalOrders extends Value
{

	public function name()
	{
	    return 'Total Transactions';
	}
  /**
   * Calculate the value of the metric.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return mixed
   */
  public function calculate(NovaRequest $request)
  {
	$query = Order::query();
	if (!$request->user()->hasRole('super-admin')) {
		$adminFundraisers = Fundraiser::where('admin_id',$request->user()->id)->get()->pluck('id');
		$query->whereIn('fundraiser_page_id', $adminFundraisers);
	}
  	return $this->count($request, $query);
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
