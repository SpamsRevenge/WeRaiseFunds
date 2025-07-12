<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DateRange extends Filter
{
  /**
   * The filter's component.
   *
   * @var string
   */
  public $component = 'select-filter';

  /**
   * Apply the filter to the given query.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @param  mixed  $value
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function apply(Request $request, $query, $value)
	{
		// print_r($value);
		$options = self::options($request);
	  return $query->whereBetween('created_at', [Carbon::parse($options[$value]['start']), Carbon::parse($options[$value]['end'])]);
	}

  /**
   * Get the filter's available options.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function options(Request $request)
	{
    return [
      "Today" => [
        "start" => now()->startOfDay(),
        "end" => now()->endOfDay(),
      ],
      "Last 7 Days" => [
        "start" => now()->subDays(7)->startOfDay(),
        "end" => now()->endOfDay(),
      ],
      "This Month" => [
        "start" => now()->startOfMonth(),
        "end" => now()->endOfMonth(),
      ],
      "Last Month" => [
        "start" => now()->subMonth()->startOfMonth(),
        "end" => now()->subMonth()->endOfMonth(),
      ],
    ];
	}
}
