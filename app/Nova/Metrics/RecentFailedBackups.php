<?php

namespace App\Nova\Metrics;

use App\Models\BackupLog;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class RecentFailedBackups extends Table
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {

        $backup_log_data = BackupLog::where(["status" => "Failed"])->latest()->limit(15)->get();

        $backup_log_data->transform(function ($item, $key) {
            return MetricTableRow::make()
                ->icon('exclamation-circle')
                ->iconClass('text-red-500')
                ->title(str($item->log)->limit(185))
                ->subtitle($item->created_at->format("Y-m-d H:i:s"))
                ->actions(function () use (&$item) {
                    return [
                        MenuItem::externalLink('View log', '/nova/resources/backup-logs/' . $item->id)
                    ];
                });
        });

        return $backup_log_data;


        return [
            MetricTableRow::make()
                ->icon('check-circle')
                ->iconClass('text-green-500')
                ->title('Silver Surfer')
                ->subtitle('In every part of the globe it is the same!'),
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
