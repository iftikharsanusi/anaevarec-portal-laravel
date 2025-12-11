@extends('layouts.app')

@section('title', 'Web Analytics Dashboard')

@php
    $description = 'Comprehensive analytics and data visualization for your security header analyses. View trends, statistics, and insights.';
@endphp

@section('meta')
    <meta name="description" content="{{ $description }}">
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap');
    
    body {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        font-family: 'Rubik', sans-serif;
    }

    .analytics-container {
        padding: 2rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .analytics-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        margin-bottom: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 0.625rem 0.875rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.2s ease;
        height: 100%;
        min-height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
    }

    .kpi-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .kpi-icon-wrapper {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .kpi-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        color: #3b82f6;
    }

    .kpi-icon svg {
        width: 100%;
        height: 100%;
    }

    .kpi-content {
        text-align: center;
    }

    .kpi-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 400;
        margin-bottom: 0.2rem;
        text-transform: none;
        letter-spacing: 0;
    }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #000000;
        margin: 0.1rem 0;
        line-height: 1.2;
    }

    .kpi-change {
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    .kpi-change.positive {
        color: #10b981;
    }

    .kpi-change.negative {
        color: #ef4444;
    }

    .kpi-change svg {
        width: 12px;
        height: 12px;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .progress-bar-container {
        margin-bottom: 1rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .progress-bar-wrapper {
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .progress-bar.green {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 1rem;
    }

    .donut-chart-container {
        position: relative;
        height: 160px;
        margin-top: 1rem;
    }

    .analytics-content-wrapper {
        position: relative;
    }

    .analytics-content-wrapper.fogged {
        opacity: 0.5;
        pointer-events: none;
        filter: blur(4px);
        transition: opacity 0.3s ease, filter 0.3s ease;
    }

    .page-fog-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(255, 255, 255, 0.90) 50%, rgba(255, 255, 255, 0.85) 100%);
        backdrop-filter: blur(15px) saturate(180%);
        -webkit-backdrop-filter: blur(15px) saturate(180%);
        border-radius: 16px;
        z-index: 100;
        pointer-events: none;
        transition: opacity 0.4s ease;
    }

    .analytics-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .analytics-table {
        margin: 0 auto;
    }

    .analytics-table th {
        background-color: #f9fafb;
        padding: 0.75rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }

    .analytics-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #1f2937;
        text-align: center;
    }

    .analytics-table tr:hover {
        background-color: #f9fafb;
    }

    .badge-score {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-score.excellent {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-score.good {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-score.fair {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-score.poor {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .page-title {
        color: #ffffff;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .analytics-container {
            padding: 1rem 0.5rem;
        }
        
        .kpi-card {
            min-height: 120px;
            padding: 0.625rem 0.875rem;
        }
        
        .kpi-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }
        
        .kpi-value {
            font-size: 1.6rem;
        }
        
        .kpi-label {
            font-size: 0.75rem;
        }
        
        .kpi-change {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .kpi-change svg {
            width: 11px;
            height: 11px;
        }
        
        .chart-container {
            height: 250px;
        }
    }
</style>

<div class="analytics-container">
    <h1 class="page-title">Web Analytics Dashboard</h1>

    <div class="analytics-content-wrapper @if($totalAnalyses == 0) fogged @endif">
        @if($totalAnalyses == 0)
        <div class="page-fog-overlay"></div>
        @endif

        <!-- Top KPI Cards - Minimalist Design -->
    <div class="row g-3 mb-3 justify-content-center">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Total Analyses</div>
                    <div class="kpi-value">{{ number_format($totalAnalyses) }}</div>
                    <div class="kpi-change {{ $totalAnalysesChange >= 0 ? 'positive' : 'negative' }}">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $totalAnalysesChange >= 0 ? '+' : '' }}{{ number_format($totalAnalysesChange, 2) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Average Score</div>
                    <div class="kpi-value">{{ number_format($averageScore, 1) }}%</div>
                    <div class="kpi-change {{ $avgScoreChange >= 0 ? 'positive' : 'negative' }}">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $avgScoreChange >= 0 ? '+' : '' }}{{ number_format($avgScoreChange, 2) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">High Scores</div>
                    <div class="kpi-value">{{ number_format($allTimeHighScores) }}</div>
                    <div class="kpi-change {{ $highScoresChange >= 0 ? 'positive' : 'negative' }}">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $highScoresChange >= 0 ? '+' : '' }}{{ number_format($highScoresChange, 2) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-label">Avg Missing Headers</div>
                    <div class="kpi-value">{{ number_format($allTimeAvgMissing, 1) }}</div>
                    <div class="kpi-change {{ $avgMissingChange >= 0 ? 'positive' : 'negative' }}">
                        @if($avgMissingChange >= 0)
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        @else
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                        <span>{{ $avgMissingChange >= 0 ? '+' : '' }}{{ number_format($avgMissingChange, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balanced Charts Row -->
    <div class="row g-3">
        <!-- Left: Security Score Trends Over Time -->
        <div class="col-lg-6">
            <div class="analytics-card">
                <div class="section-title">
                    <i class="fas fa-chart-area"></i>
                    Security Score Trends Over Time
                </div>
                <div class="chart-container">
                    <canvas id="scoreTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right: Top Missing Headers -->
        <div class="col-lg-6">
            <div class="analytics-card">
                <div class="section-title">
                    <i class="fas fa-exclamation-circle"></i>
                    Top Missing Headers
                </div>
                @if(!empty($topMissingHeaders))
                    <div class="donut-chart-container">
                        <canvas id="missingHeadersChart"></canvas>
                    </div>
                    <div class="mt-2">
                        @foreach($topMissingHeaders as $header => $count)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size: 0.8rem; color: #374151;">{{ $header }}</span>
                                <span style="font-weight: 600; color: #1a1a2e; font-size: 0.875rem;">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.3; color: #10b981;"></i>
                        <p style="margin: 0;">No missing headers found!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Security Grade Distribution Row -->
    <div class="row g-3 mb-3">
        <div class="col-lg-12">
            <div class="analytics-card">
                <div class="section-title">
                    <i class="fas fa-chart-pie"></i>
                    Security Grade Distribution
                </div>
                <div class="chart-container">
                    <canvas id="gradeDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row g-3">
        <!-- Left: Header Implementation Progress -->
        <div class="col-lg-6">
            <div class="analytics-card">
                <div class="section-title">
                    <i class="fas fa-tasks"></i>
                    Header Implementation Progress
                </div>
                @if($totalAnalyses > 0)
                    @php
                        // Group headers by security level
                        $essentialHeaders = [
                            'Content-Security-Policy' => ['points' => 25, 'badge' => 'Critical'],
                            'Strict-Transport-Security' => ['points' => 18, 'badge' => 'Critical'],
                            'X-Frame-Options' => ['points' => 12, 'badge' => 'Essential'],
                            'X-Content-Type-Options' => ['points' => 12, 'badge' => 'Essential'],
                            'Permissions-Policy' => ['points' => 10, 'badge' => 'Important'],
                            'Referrer-Policy' => ['points' => 5, 'badge' => 'Important'],
                        ];
                        
                        $importantHeaders = [
                            'X-XSS-Protection' => ['points' => 4, 'badge' => 'Recommended'],
                            'X-Permitted-Cross-Domain-Policies' => ['points' => 3, 'badge' => 'Recommended'],
                        ];
                        
                        $advancedHeaders = [
                            'Cross-Origin-Resource-Policy' => ['points' => 2, 'badge' => 'Advanced'],
                            'Cross-Origin-Embedder-Policy' => ['points' => 1, 'badge' => 'Advanced'],
                            'Cross-Origin-Opener-Policy' => ['points' => 1, 'badge' => 'Advanced'],
                            'Expect-CT' => ['points' => 1, 'badge' => 'Advanced'],
                            'Report-To' => ['points' => 1, 'badge' => 'Advanced'],
                            'Clear-Site-Data' => ['points' => 1, 'badge' => 'Advanced'],
                        ];
                    @endphp
                    
                    <!-- Essential Headers -->
                    <div style="margin-bottom: 1.5rem;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #dc2626; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                            <i class="fas fa-shield-alt" style="margin-right: 0.25rem;"></i>Essential Headers
                        </div>
                        @foreach($essentialHeaders as $headerName => $headerInfo)
                            @if(isset($headerProgress[$headerName]))
                                <div class="progress-bar-container" style="margin-bottom: 0.875rem;">
                                    <div class="progress-label">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span>{{ $headerName }}</span>
                                            <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 4px; background-color: #fee2e2; color: #991b1b; font-weight: 600;">
                                                {{ $headerInfo['badge'] }}
                                            </span>
                                        </div>
                                        <span>{{ $headerProgress[$headerName] }}%</span>
                                    </div>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar {{ $headerProgress[$headerName] >= 70 ? 'green' : '' }}" 
                                             style="width: {{ $headerProgress[$headerName] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <!-- Important Headers -->
                    <div style="margin-bottom: 1.5rem;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                            <i class="fas fa-exclamation-triangle" style="margin-right: 0.25rem;"></i>Important Headers
                        </div>
                        @foreach($importantHeaders as $headerName => $headerInfo)
                            @if(isset($headerProgress[$headerName]))
                                <div class="progress-bar-container" style="margin-bottom: 0.875rem;">
                                    <div class="progress-label">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span>{{ $headerName }}</span>
                                            <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 4px; background-color: #fef3c7; color: #92400e; font-weight: 600;">
                                                {{ $headerInfo['badge'] }}
                                            </span>
                                        </div>
                                        <span>{{ $headerProgress[$headerName] }}%</span>
                                    </div>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar {{ $headerProgress[$headerName] >= 70 ? 'green' : '' }}" 
                                             style="width: {{ $headerProgress[$headerName] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <!-- Advanced Headers -->
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                            <i class="fas fa-cog" style="margin-right: 0.25rem;"></i>Advanced Headers
                        </div>
                        @foreach($advancedHeaders as $headerName => $headerInfo)
                            @if(isset($headerProgress[$headerName]))
                                <div class="progress-bar-container" style="margin-bottom: 0.875rem;">
                                    <div class="progress-label">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span>{{ $headerName }}</span>
                                            <span style="font-size: 0.65rem; padding: 0.125rem 0.5rem; border-radius: 4px; background-color: #f3f4f6; color: #4b5563; font-weight: 600;">
                                                {{ $headerInfo['badge'] }}
                                            </span>
                                        </div>
                                        <span>{{ $headerProgress[$headerName] }}%</span>
                                    </div>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar {{ $headerProgress[$headerName] >= 70 ? 'green' : '' }}" 
                                             style="width: {{ $headerProgress[$headerName] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem; color: #6b7280;">
                        <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>No analysis data available. Start analyzing websites to see header implementation progress!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Most Analyzed URLs -->
        <div class="col-lg-6">
            <div class="analytics-card">
                <div class="section-title">
                    <i class="fas fa-globe"></i>
                    Most Analyzed URLs
                </div>
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th>Count</th>
                            <th>Avg Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($urlCounts as $urlData)
                            @php
                                $host = parse_url($urlData['url'], PHP_URL_HOST) ?? substr($urlData['url'], 0, 30);
                                $scoreClass = $urlData['avg_score'] >= 70 ? 'excellent' : ($urlData['avg_score'] >= 40 ? 'good' : ($urlData['avg_score'] >= 25 ? 'fair' : 'poor'));
                            @endphp
                            <tr>
                                <td title="{{ $urlData['url'] }}">
                                    {{ $host }}
                                </td>
                                <td>{{ $urlData['count'] }}</td>
                                <td>
                                    <span class="badge-score {{ $scoreClass }}">{{ $urlData['avg_score'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                        @if($urlCounts->isEmpty())
                            <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280; padding: 2rem;">
                                    No data available
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Score Trends Chart
    const scoreTrendsData = @json([
        'labels' => $scoreTrends->keys()->toArray(),
        'data' => $scoreTrends->values()->toArray()
    ]);

    @if($scoreTrends->isNotEmpty())
    const scoreTrendsCtx = document.getElementById('scoreTrendsChart').getContext('2d');
    new Chart(scoreTrendsCtx, {
        type: 'line',
        data: {
            labels: scoreTrendsData.labels,
            datasets: [{
                label: 'Security Score',
                data: scoreTrendsData.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Score: ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @else
    // No data available for trends chart
    document.getElementById('scoreTrendsChart').parentElement.innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;">No analysis data available yet. Start analyzing websites to see trends!</div>';
    @endif

    // Missing Headers Donut Chart
    @if(!empty($topMissingHeaders))
    const missingHeadersData = @json([
        'labels' => array_keys($topMissingHeaders),
        'data' => array_values($topMissingHeaders)
    ]);

    const missingHeadersCtx = document.getElementById('missingHeadersChart').getContext('2d');
    new Chart(missingHeadersCtx, {
        type: 'doughnut',
        data: {
            labels: missingHeadersData.labels,
            datasets: [{
                data: missingHeadersData.data,
                backgroundColor: [
                    '#3b82f6',
                    '#8b5cf6',
                    '#ec4899',
                    '#f59e0b',
                    '#10b981'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    @else
    // No missing headers data
    document.getElementById('missingHeadersChart').parentElement.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;">No missing headers data available</div>';
    @endif

    // Security Grade Distribution Chart
    @php
        $gradeDistributionData = [
            'A' => $gradeDistribution['A'] ?? 0,
            'B' => $gradeDistribution['B'] ?? 0,
            'C' => $gradeDistribution['C'] ?? 0,
            'D' => $gradeDistribution['D'] ?? 0,
            'E' => $gradeDistribution['E'] ?? 0,
            'F' => $gradeDistribution['F'] ?? 0,
        ];
    @endphp
    const gradeDistributionData = @json($gradeDistributionData);
    const totalGrades = Object.values(gradeDistributionData).reduce((a, b) => a + b, 0);

    @if($totalAnalyses > 0)
    const gradeDistributionCtx = document.getElementById('gradeDistributionChart').getContext('2d');
    new Chart(gradeDistributionCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(gradeDistributionData),
            datasets: [{
                label: 'Number of Analyses',
                data: Object.values(gradeDistributionData),
                backgroundColor: [
                    '#10b981', // A - Green
                    '#3b82f6', // B - Blue
                    '#f59e0b', // C - Yellow/Orange
                    '#f77f00', // D - Orange
                    '#ef4444', // E - Red
                    '#dc2626', // F - Dark Red
                ],
                borderColor: [
                    '#059669',
                    '#2563eb',
                    '#d97706',
                    '#ea580c',
                    '#dc2626',
                    '#991b1b',
                ],
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            const percentage = totalGrades > 0 ? ((value / totalGrades) * 100).toFixed(1) : 0;
                            return 'Grade ' + context.label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @else
    // No grade distribution data
    document.getElementById('gradeDistributionChart').parentElement.innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;">No analysis data available yet. Start analyzing websites to see grade distribution!</div>';
    @endif
</script>
@endsection

