<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use App\Models\Order;

class OrderTotalPie extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
    	// return $this->result([
		//     'Group 1' => 100,
		//     'Group 2' => 200,
		//     'Group 3' => 300,
		// ]);
		// return $this->sum($request, Order::class,  'donation_total','created_at');
		return $progress = $this->count($request, Order::class, 'donation_total','fee_collected');
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

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'order-total-pie';
    }
}
