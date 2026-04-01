<?php

namespace App\Filament\Company\Widgets\Dashboard;

use Filament\Widgets\ChartWidget;

class WeeklyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Revenue Growth';

    // Position it in the dashboard (optional)
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => [1200, 1900, 1500, 2800, 3500, 3100, 4200, 5000], // Fake climbing data
                    'fill' => 'start', // Fills the area under the line
                    'borderColor' => '#8b5cf6', // Zenvoyc Violet
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)', // Subtle purple glow
                    'tension' => 0.4, // Makes the line smooth/curvy
                ],
            ],
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Advanced options to make it look "Clean" & Premium
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false, // Keeps the UI minimalist
                ],
            ],
            'scales' => [
                'y' => [
                    'grid' => [
                        'display' => false, // Removes horizontal grid lines
                    ],
                    'ticks' => [
                        'display' => true,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false, // Removes vertical grid lines
                    ],
                ],
            ],
        ];
    }
}
