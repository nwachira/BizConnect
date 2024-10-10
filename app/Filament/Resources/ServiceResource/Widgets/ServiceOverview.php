<?php

namespace App\Filament\Resources\ServiceResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Service;

class ServiceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalServices = Service::count();

        return [
            Stat::make('Total Services', $totalServices)
                ->description('Total number of services offered')
                ->color('primary'),
        ];
    }
}
