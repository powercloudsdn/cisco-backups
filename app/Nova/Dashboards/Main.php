<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\FailedBackupsPerDay;
use App\Nova\Metrics\RecentFailedBackups;
use App\Nova\Metrics\SuccessBackupsPerDay;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

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
            new SuccessBackupsPerDay(),
            new FailedBackupsPerDay(),
            (new RecentFailedBackups())->width('full'),
        ];
    }
}
