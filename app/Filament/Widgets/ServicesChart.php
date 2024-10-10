<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaction; 
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ServicesChart extends ChartWidget
{
    protected static ?string $heading = 'Service Revenue Comparison';
    protected static ?int $navigationSort = 1;


    protected function getData(): array
    {
        $services = Transaction::select('service_id', \DB::raw('SUM(amount_earned) as total_earned'))
            ->groupBy('service_id')
            ->with('service:id,name') // Eager load service name
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Earned',
                    'data' => $services->pluck('total_earned'),
                    'backgroundColor' => [
                        // Add colors for each bar (you can customize these)
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        // ... add more colors if needed
                    ],
                ],
            ],
            'labels' => $services->pluck('service.name'), // Use service names for labels
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Or use 'pie' for a pie chart representation
    }
}
