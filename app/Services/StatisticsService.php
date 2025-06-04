<?php

namespace App\Services;

use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StatisticsService
{
    private function hashIp($ip)
    {
        return $ip;
    }

    private function hashUserAgent($userAgent)
    {
        return $userAgent;
    }

    public function trackContentView($category, $contentId, Request $request)
    {
        try {
            $ipHash = $this->hashIp($request->ip());
            $userAgentHash = $this->hashUserAgent($request->userAgent());
            
            // Handle session ID safely for API requests
            try {
                $sessionId = $request->session()->getId();
            } catch (\Exception $e) {
                $sessionId = null; // API requests might not have sessions
            }
            
            $now = Carbon::now();

            // Check if this user already viewed this content today (optional deduplication)
            $existingView = Statistic::where([
                'type' => 'content_view',
                'category' => $category,
                'content_id' => $contentId,
                'ip_hash' => $ipHash,
                'date' => $now->toDateString(),
            ])->exists();

            if (1) {
                $statistic = Statistic::create([
                    'type' => 'content_view',
                    'category' => $category,
                    'content_id' => $contentId,
                    'ip_hash' => $ipHash,
                    'user_agent_hash' => $userAgentHash,
                    'session_id' => $sessionId,
                    'date' => $now->toDateString(),
                    'time' => $now->toTimeString(),
                ]);
            }
        } catch (\Exception $e) {
        }
    }

    public function trackFeatureVisit($featureName, Request $request)
    {
        $ipHash = $this->hashIp($request->ip());
        $userAgentHash = $this->hashUserAgent($request->userAgent());
        
        // Handle session ID safely for API requests
        try {
            $sessionId = $request->session()->getId();
        } catch (\Exception $e) {
            $sessionId = null; // API requests might not have sessions
        }
        
        $now = Carbon::now();

        // Check if this user already visited this feature today (optional deduplication)
        $existingVisit = Statistic::where([
            'type' => 'feature_visit',
            'category' => 'feature',
            'feature_name' => $featureName,
            'ip_hash' => $ipHash,
            'date' => $now->toDateString(),
        ])->exists();

        if (!$existingVisit) {
            Statistic::create([
                'type' => 'feature_visit',
                'category' => 'feature',
                'feature_name' => $featureName,
                'ip_hash' => $ipHash,
                'user_agent_hash' => $userAgentHash,
                'session_id' => $sessionId,
                'date' => $now->toDateString(),
                'time' => $now->toTimeString(),
            ]);
        }
    }

    public function trackContentInteraction($category, $contentId, $interactionType, Request $request)
    {
        try {
            $ipHash = $this->hashIp($request->ip());
            $userAgentHash = $this->hashUserAgent($request->userAgent());
            
            try {
                $sessionId = $request->session()->getId();
            } catch (\Exception $e) {
                $sessionId = null;
            }
            
            $now = Carbon::now();

            Statistic::create([
                'type' => 'content_interaction',
                'category' => $category,
                'content_id' => $contentId,
                'ip_hash' => $ipHash,
                'user_agent_hash' => $userAgentHash,
                'session_id' => $sessionId,
                'date' => $now->toDateString(),
                'time' => $now->toTimeString(),
            ]);
        } catch (\Exception $e) {
        }
    }

    public function getContentStats($category = null, $period = 'month')
    {
        $query = Statistic::contentViews();

        if ($category) {
            $query->where('category', $category);
        }

        // Apply period filter
        switch ($period) {
            case 'day':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
            case 'year':
                $query->thisYear();
                break;
        }

        return $query->select('category', 'content_id', DB::raw('COUNT(*) as views'))
                    ->groupBy('category', 'content_id')
                    ->orderBy('views', 'desc')
                    ->get();
    }

    public function getFeatureStats($period = 'month')
    {
        $query = Statistic::featureVisits();

        // Apply period filter
        switch ($period) {
            case 'day':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
            case 'year':
                $query->thisYear();
                break;
        }

        return $query->select('feature_name', DB::raw('COUNT(*) as visits'))
                    ->groupBy('feature_name')
                    ->orderBy('visits', 'desc')
                    ->get();
    }

    public function getTimeSeriesData($type = 'content_view', $period = 'daily', $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $query = Statistic::where('type', $type)
                         ->whereBetween('date', [$startDate, $endDate]);

        switch ($period) {
            case 'daily':
                return $query->select('date', DB::raw('COUNT(*) as count'))
                           ->groupBy('date')
                           ->orderBy('date')
                           ->get();
            case 'weekly':
                return $query->select(
                               DB::raw('YEARWEEK(date) as week'),
                               DB::raw('COUNT(*) as count')
                           )
                           ->groupBy('week')
                           ->orderBy('week', 'asc')
                           ->get();
            case 'monthly':
                return $query->select(
                               DB::raw('YEAR(date) as year'),
                               DB::raw('MONTH(date) as month'),
                               DB::raw('COUNT(*) as count')
                           )
                           ->groupBy('year', 'month')
                           ->orderBy('year', 'asc')
                           ->orderBy('month', 'asc')
                           ->get();
        }
    }

    public function getUniqueVisitors($period = 'month')
    {
        $query = Statistic::query();

        switch ($period) {
            case 'day':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
            case 'year':
                $query->thisYear();
                break;
        }

        return $query->whereNotNull('session_id')->distinct('session_id')->count();
    }

    public function getContentStatsWithTitles($category = null, $period = 'month')
    {
        // Get view stats
        $viewQuery = Statistic::where('type', 'content_view');
        if ($category) {
            $viewQuery->where('category', $category);
        }

        // Get comment stats (only for news and produk_hukum)
        $commentQuery = Statistic::where('type', 'content_interaction');
        if ($category && in_array($category, ['news', 'produk_hukum'])) {
            $commentQuery->where('category', $category);
        } elseif (!$category) {
            $commentQuery->whereIn('category', ['news', 'produk_hukum']);
        } else {
            // For other categories, no comments
            $commentQuery->where('id', -1); // This will return no results
        }

        // Apply period filter
        $this->applyPeriodFilter($commentQuery, $period);
        $this->applyPeriodFilter($viewQuery, $period);

        $commentStats = $commentQuery->select('category', 'content_id', DB::raw('COUNT(*) as comments'))
                                    ->groupBy('category', 'content_id')
                                    ->get();

        // Get view counts
        $viewStats = $viewQuery->select('category', 'content_id', DB::raw('COUNT(*) as views'))
                              ->groupBy('category', 'content_id')
                              ->get();

        // Combine and calculate weighted scores
        $combinedStats = collect();

        // Add view stats
        foreach ($viewStats as $stat) {
            $combinedStats->push([
                'category' => $stat->category,
                'content_id' => $stat->content_id,
                'views' => $stat->views,
                'comments' => 0,
                'weighted_score' => $stat->views * 1
            ]);
        }

        // Add comment stats
        foreach ($commentStats as $stat) {
            $existingIndex = $combinedStats->search(function ($item) use ($stat) {
                return $item['category'] === $stat->category && $item['content_id'] === $stat->content_id;
            });
            
            if ($existingIndex !== false) {
                $existing = $combinedStats[$existingIndex];
                $existing['comments'] = $stat->comments;
                $existing['weighted_score'] = ($existing['views'] * 1) + ($stat->comments * 3);
                $combinedStats[$existingIndex] = $existing;
            } else {
                $combinedStats->push([
                    'category' => $stat->category,
                    'content_id' => $stat->content_id,
                    'views' => 0,
                    'comments' => $stat->comments,
                    'weighted_score' => $stat->comments * 3
                ]);
            }
        }

        // Sort by weighted score and get titles
        $enrichedStats = $combinedStats->sortByDesc('weighted_score')->map(function ($stat) {
            $title = 'Unknown';
            
            switch ($stat['category']) {
                case 'news':
                    $content = DB::table('news')->where('id', $stat['content_id'])->first();
                    $title = $content ? $content->title : 'News ID: ' . $stat['content_id'];
                    break;
                case 'produk_hukum':
                    $content = DB::table('produk_hukum')->where('id', $stat['content_id'])->first();
                    $title = $content ? $content->title : 'Produk Hukum ID: ' . $stat['content_id'];
                    break;
                case 'dana_desa':
                    $content = DB::table('dana_desa')->where('id', $stat['content_id'])->first();
                    $title = $content ? $content->title : 'Dana Desa ID: ' . $stat['content_id'];
                    break;
                case 'produk_desa':
                    $content = DB::table('produk_desa')->where('id', $stat['content_id'])->first();
                    $title = $content ? $content->product_name : 'Produk Desa ID: ' . $stat['content_id'];
                    break;
                default:
                    $title = ucfirst($stat['category']) . ' ID: ' . $stat['content_id'];
                    break;
            }

            return [
                'category' => $stat['category'],
                'content_id' => $stat['content_id'],
                'views' => $stat['views'],
                'comments' => $stat['comments'],
                'weighted_score' => $stat['weighted_score'],
                'title' => $title
            ];
        });

        return $enrichedStats->values();
    }

    public function getVisitorTimeSeriesData($period = 'daily')
    {
        $labels = [];
        $values = [];
        $now = Carbon::now();

        // Indonesian day names mapping
        $dayNames = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        // Indonesian month names mapping
        $monthNames = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];

        switch ($period) {
            case 'daily':
                // Show hours from 00:00 to current hour with 4-hour steps
                $currentHour = $now->hour;
                // Include the current 4-hour block if we're past the start of it
                $maxHour = $currentHour + 4 - ($currentHour % 4);
                
                for ($i = 0; $i < $maxHour; $i += 4) {
                    $hourStart = Carbon::today()->addHours($i);
                    $hourEnd = Carbon::today()->addHours($i + 4);
                    $labels[] = $hourStart->format('H:i') . '-' . $hourEnd->format('H:i');
                    
                    // For the current time block, only count up to now
                    $endTime = $hourEnd;
                    if ($hourEnd->gt($now)) {
                        $endTime = $now;
                    }
                    
                    $count = Statistic::whereDate('date', Carbon::today())
                        ->where(function($query) use ($hourStart, $endTime) {
                            $query->whereTime('time', '>=', $hourStart->format('H:i:s'))
                                  ->whereTime('time', '<=', $endTime->format('H:i:s'));
                        })
                        ->whereNotNull('session_id')
                        ->distinct('session_id')
                        ->count();
                    
                    $values[] = $count;
                }
                break;
                
            case 'weekly':
                // Show last 7 days ending with today
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->clone()->subDays($i);
                    $dayName = $dayNames[$date->format('l')];
                    $monthName = $monthNames[$date->format('F')];
                    $labels[] = $dayName . ', ' . $date->format('j') . ' ' . $monthName;
                    
                    $count = Statistic::whereDate('date', $date)
                        ->whereNotNull('session_id')
                        ->distinct('session_id')
                        ->count();
                    
                    $values[] = $count;
                }
                break;
                
            case 'monthly':
                // Show last 4 weeks ending with current week
                for ($i = 3; $i >= 0; $i--) {
                    $weekStart = $now->clone()->subWeeks($i)->startOfWeek();
                    $weekEnd = $now->clone()->subWeeks($i)->endOfWeek();
                    
                    // For current week, only go up to today
                    if ($i === 0) {
                        $weekEnd = $now->lt($weekEnd) ? $now : $weekEnd;
                    }
                    
                    $startMonth = $monthNames[$weekStart->format('F')];
                    $endMonth = $monthNames[$weekEnd->format('F')];
                    
                    if ($weekStart->format('F') === $weekEnd->format('F')) {
                        $labels[] = $weekStart->format('j') . '-' . $weekEnd->format('j') . ' ' . $startMonth;
                    } else {
                        $labels[] = $weekStart->format('j') . ' ' . $startMonth . ' - ' . $weekEnd->format('j') . ' ' . $endMonth;
                    }
                    
                    $count = Statistic::whereBetween('date', [$weekStart, $weekEnd])
                        ->whereNotNull('session_id')
                        ->distinct('session_id')
                        ->count();
                    
                    $values[] = $count;
                }
                break;
                
            case 'yearly':
                // Show months from January to current month of current year
                $startOfYear = $now->clone()->startOfYear();
                $currentMonth = $now->month;
                
                for ($i = 0; $i < $currentMonth; $i++) {
                    $monthStart = $startOfYear->clone()->addMonths($i);
                    $monthEnd = $monthStart->clone()->endOfMonth();
                    
                    // For current month, only go up to today
                    if ($i === $currentMonth - 1) {
                        $monthEnd = $now->lt($monthEnd) ? $now : $monthEnd;
                    }
                    
                    $monthName = $monthNames[$monthStart->format('F')];
                    $labels[] = $monthName . ' ' . $monthStart->format('Y');
                    
                    $count = Statistic::whereBetween('date', [$monthStart, $monthEnd])
                        ->whereNotNull('session_id')
                        ->distinct('session_id')
                        ->count();
                    
                    $values[] = $count;
                }
                break;
                
            case 'all':
                // Show years from first record to current year
                $firstRecord = Statistic::orderBy('date', 'asc')->first();
                $startYear = $firstRecord ? Carbon::parse($firstRecord->date)->year : $now->year;
                $currentYear = $now->year;
                
                for ($year = $startYear; $year <= $currentYear; $year++) {
                    $yearStart = Carbon::createFromDate($year, 1, 1)->startOfYear();
                    $yearEnd = Carbon::createFromDate($year, 12, 31)->endOfYear();
                    
                    // For current year, only go up to today
                    if ($year === $currentYear) {
                        $yearEnd = $now->lt($yearEnd) ? $now : $yearEnd;
                    }
                    
                    $labels[] = (string) $year;
                    
                    $count = Statistic::whereBetween('date', [$yearStart, $yearEnd])
                        ->whereNotNull('session_id')
                        ->distinct('session_id')
                        ->count();
                    
                    $values[] = $count;
                }
                break;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    public function getContentViewsByCategory($months = 6)
    {
        $now = Carbon::now();
        $categories = ['news', 'produk_hukum', 'dana_desa', 'produk_desa'];
        $labels = [];
        $datasets = [];

        // Indonesian month names mapping
        $monthNames = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];

        // Generate labels for the last X months
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthDate = $now->clone()->subMonths($i);
            $monthName = $monthNames[$monthDate->format('F')];
            $labels[] = $monthName . ' ' . $monthDate->format('Y');
        }

        // Initialize datasets for each category
        foreach ($categories as $category) {
            $data = [];
            
            for ($i = $months - 1; $i >= 0; $i--) {
                $monthStart = $now->clone()->subMonths($i)->startOfMonth();
                $monthEnd = $now->clone()->subMonths($i)->endOfMonth();
                
                // For current month, only count up to today
                if ($i === 0) {
                    $monthEnd = $now->lt($monthEnd) ? $now : $monthEnd;
                }
                
                $count = Statistic::where('type', 'content_view')
                    ->where('category', $category)
                    ->whereBetween('date', [$monthStart, $monthEnd])
                    ->count();
                
                $data[] = $count;
            }
            
            $datasets[] = [
                'label' => $this->getCategoryDisplayName($category),
                'category' => $category,
                'data' => $data
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    private function getCategoryDisplayName($category)
    {
        $names = [
            'news' => 'Berita',
            'produk_hukum' => 'Produk Hukum',
            'dana_desa' => 'Dana Desa',
            'produk_desa' => 'Produk Desa'
        ];
        
        return $names[$category] ?? ucfirst($category);
    }

    private function applyPeriodFilter($query, $period)
    {
        switch ($period) {
            case 'day':
                $query->whereDate('date', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('date', Carbon::now()->month)
                      ->whereYear('date', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('date', Carbon::now()->year);
                break;
            case 'all':
                // No filter - show all time data
                break;
        }
        return $query;
    }
}
