<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;

use App\Nova\Metrics\OrderTotals;
class FundraiserInsights extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            OrderTotals::make()
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'fundraiser-insights';
    }
}
