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
