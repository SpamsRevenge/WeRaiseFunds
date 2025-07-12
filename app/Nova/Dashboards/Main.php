<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

use App\Nova\Metrics\OrderTotals;
use App\Nova\Metrics\TotalCampaigns;
use App\Nova\Metrics\TopPerformingAdmin;
use App\Nova\Metrics\TopPerformingFundraiser;
class Main extends Dashboard
{
  /**
   * Get the cards for the dashboard.
   *
   * @return array
   */
  public function cards()
  {
  	return [
  		new OrderTotals,
  		new TotalCampaigns,
  		TopPerformingAdmin::make()->canSee(function ($request) {
  			return ($request->user())?$request->user()->isSuperAdmin():false;
  		}),
  		new TopPerformingFundraiser,
  	];
  }
}

?>