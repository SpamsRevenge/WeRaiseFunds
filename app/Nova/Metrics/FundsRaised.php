<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Progress;
use App\Models\FundraiserSeller;

class FundsRaised extends Progress
{
  /**
   * Calculate the value of the metric.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return mixed
   */
  public function calculate(NovaRequest $request)
  {  

  	// print_r($request->resourceId);
  	$seller = $request->resourceId ? FundraiserSeller::find($request->resourceId) : null;
  	$targetValue = $seller->amount_to_raise;
  	 // Check if both total_collected and amount_to_raise are zero
  	if ( $targetValue < 1 ) {
	    // Return a default value or message
  		return false;
  	}
  	$progress = $this->sum($request, FundraiserSeller::class, function ($query) use($seller) {
  		return $query->where('id', $seller->id);
  	}, 'total_collected', target: $targetValue)->prefix('$');
  	// Append the target value as a suffix to the progress value
  	$progress->suffix(' / $' . number_format($targetValue,0,','))
  	->format([
  		'thousandSeparated' => true,
  		'mantissa' => 0,
  	]);;
  	return $progress;
  }

  /**
   * Determine the amount of time the results of the metric should be cached.
   *
   * @return  \DateTimeInterface|\DateInterval|float|int
   */
  public function cacheFor()
  {
      // return now()->addMinutes(5);
  }

  /**
   * Get the URI key for the metric.
   *
   * @return string
   */
  public function uriKey()
  {
  	return 'funds-raised';
  }
}
