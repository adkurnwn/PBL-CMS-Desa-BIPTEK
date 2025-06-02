<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\ActivityLog;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        $category = $request->input('category');

        // Get overview stats for different periods
        $todayVisitors = $this->statisticsService->getUniqueVisitors('day');
        $weekVisitors = $this->statisticsService->getUniqueVisitors('week');
        $monthVisitors = $this->statisticsService->getUniqueVisitors('month');
        $yearVisitors = $this->statisticsService->getUniqueVisitors('year');

        // Get content stats for all periods
        $allContentStatsAllPeriods = [
            'day' => $this->statisticsService->getContentStatsWithTitles($category, 'day'),
            'week' => $this->statisticsService->getContentStatsWithTitles($category, 'week'),
            'month' => $this->statisticsService->getContentStatsWithTitles($category, 'month'),
            'year' => $this->statisticsService->getContentStatsWithTitles($category, 'year'),
            'all' => $this->statisticsService->getContentStatsWithTitles($category, 'all'),
        ];

        // Get content stats by category (for the category overview cards)
        $newsStats = $this->statisticsService->getContentStatsWithTitles('news', 'month');
        $produkHukumStats = $this->statisticsService->getContentStatsWithTitles('produk_hukum', 'month');
        $danaDesaStats = $this->statisticsService->getContentStatsWithTitles('dana_desa', 'month');
        $produkDesaStats = $this->statisticsService->getContentStatsWithTitles('produk_desa', 'month');
        
        // Get feature stats
        $featureStats = $this->statisticsService->getFeatureStats($period);
        
        // Get time series data for different periods
        $dailyViews = $this->statisticsService->getTimeSeriesData('content_view', 'daily', 30);
        $weeklyViews = $this->statisticsService->getTimeSeriesData('content_view', 'weekly', 84); // 12 weeks
        $monthlyViews = $this->statisticsService->getTimeSeriesData('content_view', 'monthly', 365); // 12 months
        
        $dailyVisits = $this->statisticsService->getTimeSeriesData('feature_visit', 'daily', 30);
        $weeklyVisits = $this->statisticsService->getTimeSeriesData('feature_visit', 'weekly', 84);
        $monthlyVisits = $this->statisticsService->getTimeSeriesData('feature_visit', 'monthly', 365);

        return Inertia::render('Dashboard/Home/Statistics', [
            'stats' => [
                'visitors' => [
                    'today' => $todayVisitors,
                    'week' => $weekVisitors,
                    'month' => $monthVisitors,
                    'year' => $yearVisitors,
                ],
                'content' => [
                    'news' => $newsStats,
                    'produk_hukum' => $produkHukumStats,
                    'dana_desa' => $danaDesaStats,
                    'produk_desa' => $produkDesaStats,
                    'all_periods' => $allContentStatsAllPeriods,
                ],
                'features' => $featureStats,
                'time_series' => [
                    'views' => [
                        'daily' => $dailyViews,
                        'weekly' => $weeklyViews,
                        'monthly' => $monthlyViews,
                    ],
                    'visits' => [
                        'daily' => $dailyVisits,
                        'weekly' => $weeklyVisits,
                        'monthly' => $monthlyVisits,
                    ],
                ],
            ],
            'filters' => [
                'period' => $period,
                'category' => $category,
            ]
        ]);
    }
}
