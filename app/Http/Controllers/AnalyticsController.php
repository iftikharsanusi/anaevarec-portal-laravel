<?php

namespace App\Http\Controllers;

use App\Models\HeaderAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the analytics dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Get all analyses for the user
        $analyses = HeaderAnalysis::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate key metrics
        $totalAnalyses = $analyses->count();
        $averageScore = $totalAnalyses > 0 ? round($analyses->avg('security_score'), 2) : 0;
        $bestScore = $totalAnalyses > 0 ? $analyses->max('security_score') : 0;
        $worstScore = $totalAnalyses > 0 ? $analyses->min('security_score') : 0;
        $totalMissingHeaders = $analyses->sum(function($analysis) {
            return is_array($analysis->missing_security_features) 
                ? count($analysis->missing_security_features) 
                : 0;
        });

        // Score trends over time (last 30 days or all time)
        $scoreTrends = $analyses->map(function($analysis) {
            return [
                'date' => $analysis->created_at->format('Y-m-d'),
                'score' => $analysis->security_score,
            ];
        })->groupBy('date')->map(function($group) {
            return round($group->avg('score'), 2);
        })->sortKeys();

        // Grade distribution
        $gradeDistribution = [
            'A' => $analyses->where('security_score', '>=', 85)->count(),
            'B' => $analyses->whereBetween('security_score', [70, 84])->count(),
            'C' => $analyses->whereBetween('security_score', [55, 69])->count(),
            'D' => $analyses->whereBetween('security_score', [40, 54])->count(),
            'E' => $analyses->whereBetween('security_score', [25, 39])->count(),
            'F' => $analyses->where('security_score', '<', 25)->count(),
        ];

        // Most missing headers
        $missingHeadersCount = [];
        foreach ($analyses as $analysis) {
            if (is_array($analysis->missing_security_features)) {
                foreach ($analysis->missing_security_features as $missing) {
                    $headerName = $missing['name'] ?? $missing['header'] ?? 'Unknown';
                    if (!isset($missingHeadersCount[$headerName])) {
                        $missingHeadersCount[$headerName] = 0;
                    }
                    $missingHeadersCount[$headerName]++;
                }
            }
        }
        arsort($missingHeadersCount);
        $topMissingHeaders = array_slice($missingHeadersCount, 0, 5, true);

        // Header implementation progress (how many analyses have each header)
        $headerPresence = [
            'Content-Security-Policy' => 0,
            'Strict-Transport-Security' => 0,
            'X-Frame-Options' => 0,
            'X-Content-Type-Options' => 0,
            'Permissions-Policy' => 0,
            'Referrer-Policy' => 0,
            'X-XSS-Protection' => 0,
            'X-Permitted-Cross-Domain-Policies' => 0,
            'Cross-Origin-Resource-Policy' => 0,
            'Cross-Origin-Embedder-Policy' => 0,
            'Cross-Origin-Opener-Policy' => 0,
            'Expect-CT' => 0,
            'Report-To' => 0,
            'Clear-Site-Data' => 0,
        ];

        foreach ($analyses as $analysis) {
            if (is_array($analysis->headers)) {
                $headersLower = array_change_key_case($analysis->headers, CASE_LOWER);
                foreach ($headerPresence as $headerName => $count) {
                    $headerKey = strtolower($headerName);
                    if (isset($headersLower[$headerKey]) && !empty($headersLower[$headerKey])) {
                        $headerPresence[$headerName]++;
                    }
                }
            }
        }

        // Calculate percentages
        $headerProgress = array_map(function($count) use ($totalAnalyses) {
            return $totalAnalyses > 0 ? round(($count / $totalAnalyses) * 100, 1) : 0;
        }, $headerPresence);

        // Most analyzed URLs
        $urlCounts = $analyses->groupBy('url')->map(function($group) {
            return [
                'url' => $group->first()->url,
                'count' => $group->count(),
                'avg_score' => round($group->avg('security_score'), 1),
                'latest_score' => $group->first()->security_score,
            ];
        })->sortByDesc('count')->take(5);

        // Score distribution (ranges)
        $scoreDistribution = [
            '0-20' => $analyses->whereBetween('security_score', [0, 20])->count(),
            '21-40' => $analyses->whereBetween('security_score', [21, 40])->count(),
            '41-60' => $analyses->whereBetween('security_score', [41, 60])->count(),
            '61-80' => $analyses->whereBetween('security_score', [61, 80])->count(),
            '81-100' => $analyses->whereBetween('security_score', [81, 100])->count(),
        ];

        // Calculate period-over-period changes for KPIs
        $now = now();
        $last30Days = $now->copy()->subDays(30);
        $previous30Days = $now->copy()->subDays(60)->startOfDay();
        $last30DaysEnd = $now->copy()->subDays(30)->startOfDay();

        // Recent analyses (last 30 days)
        $recentAnalyses = $analyses->filter(function($analysis) use ($last30Days) {
            return $analysis->created_at >= $last30Days;
        });
        $previousAnalyses = $analyses->filter(function($analysis) use ($previous30Days, $last30DaysEnd) {
            return $analysis->created_at >= $previous30Days && $analysis->created_at < $last30DaysEnd;
        });

        // Calculate changes
        $recentCount = $recentAnalyses->count();
        $previousCount = $previousAnalyses->count();
        $totalAnalysesChange = $previousCount > 0 
            ? round((($recentCount - $previousCount) / $previousCount) * 100, 2) 
            : ($recentCount > 0 ? 100 : 0);

        $recentAvgScore = $recentAnalyses->count() > 0 ? round($recentAnalyses->avg('security_score'), 2) : 0;
        $previousAvgScore = $previousAnalyses->count() > 0 ? round($previousAnalyses->avg('security_score'), 2) : 0;
        $avgScoreChange = $previousAvgScore > 0 
            ? round((($recentAvgScore - $previousAvgScore) / $previousAvgScore) * 100, 2) 
            : ($recentAvgScore > 0 ? 100 : 0);

        // High scores (A/B grades) - all time count with recent period change
        $allTimeHighScores = $analyses->where('security_score', '>=', 70)->count();
        $recentHighScores = $recentAnalyses->where('security_score', '>=', 70)->count();
        $previousHighScores = $previousAnalyses->where('security_score', '>=', 70)->count();
        $highScoresChange = $previousHighScores > 0 
            ? round((($recentHighScores - $previousHighScores) / $previousHighScores) * 100, 2) 
            : ($recentHighScores > 0 ? 100 : 0);

        // Average missing headers per analysis - all time average with recent period change
        $allTimeAvgMissing = $analyses->count() > 0 
            ? round($analyses->avg(function($analysis) {
                return is_array($analysis->missing_security_features) 
                    ? count($analysis->missing_security_features) 
                    : 0;
            }), 2) 
            : 0;
        $recentAvgMissing = $recentAnalyses->count() > 0 
            ? round($recentAnalyses->avg(function($analysis) {
                return is_array($analysis->missing_security_features) 
                    ? count($analysis->missing_security_features) 
                    : 0;
            }), 2) 
            : 0;
        $previousAvgMissing = $previousAnalyses->count() > 0 
            ? round($previousAnalyses->avg(function($analysis) {
                return is_array($analysis->missing_security_features) 
                    ? count($analysis->missing_security_features) 
                    : 0;
            }), 2) 
            : 0;
        // For missing headers, negative change is good (fewer missing = better)
        // So we calculate: previous - recent (if recent < previous, change is positive)
        $avgMissingChange = $previousAvgMissing > 0 
            ? round($previousAvgMissing - $recentAvgMissing, 2) 
            : ($recentAvgMissing > 0 ? -$recentAvgMissing : 0);

        return view('analytics', compact(
            'totalAnalyses',
            'averageScore',
            'bestScore',
            'worstScore',
            'totalMissingHeaders',
            'scoreTrends',
            'gradeDistribution',
            'topMissingHeaders',
            'headerProgress',
            'urlCounts',
            'scoreDistribution',
            'recentCount',
            'totalAnalysesChange',
            'recentAvgScore',
            'avgScoreChange',
            'allTimeHighScores',
            'highScoresChange',
            'allTimeAvgMissing',
            'avgMissingChange'
        ));
    }
}

