@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $description = 'Analyze website security headers instantly. Get detailed security analysis, recommendations, and missing header reports for any website URL.';
@endphp

@section('meta')
    <meta name="description" content="{{ $description }}">
    @php
        $ogTitle = 'Dashboard - AnaEvaRec | Web Security Header Analyzer';
        $ogDescription = $description;
        $ogImage = url('/images/og-image.png');
    @endphp
@endsection

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h4 class="mb-4">Web Security Header Analyzer</h4>
            <p class="text-muted mb-4">Scan any website you want — enter the website URL below:</p>

            <!-- Analysis Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('analyze') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="url" class="form-control" placeholder="Enter website URL (e.g., example.com)" required>
                            <button type="submit" class="btn btn-primary">Analyze</button>
                        </div>
                        @error('url')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Latest Analysis Preview -->
            @if($latestAnalysis)
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📊 Analysis Result Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="text-muted mb-2">Website URL</h6>
                                <p class="mb-0">
                                    <a href="{{ $latestAnalysis->url }}" target="_blank" class="text-decoration-none">
                                        {{ $latestAnalysis->url }}
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Security Score</h6>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 25px;">
                                        <div class="progress-bar {{ $latestAnalysis->security_score >= 80 ? 'bg-success' : ($latestAnalysis->security_score >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                            role="progressbar" 
                                            style="width: {{ $latestAnalysis->security_score }}%"
                                            aria-valuenow="{{ $latestAnalysis->security_score }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <strong>{{ $latestAnalysis->security_score }}%</strong>
                                        </div>
                                    </div>
                                    <span class="badge {{ $latestAnalysis->security_score >= 80 ? 'bg-success' : ($latestAnalysis->security_score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                        @php
                                            $grade = 'F';
                                            if ($latestAnalysis->security_score >= 90) $grade = 'A';
                                            elseif ($latestAnalysis->security_score >= 80) $grade = 'B';
                                            elseif ($latestAnalysis->security_score >= 70) $grade = 'C';
                                            elseif ($latestAnalysis->security_score >= 60) $grade = 'D';
                                        @endphp
                                        Grade: {{ $grade }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="text-muted mb-2">Security Headers Status</h6>
                                @php
                                    $headers = $latestAnalysis->headers ?? [];
                                    $totalSecurityHeaders = 10;
                                    $missingCount = is_array($latestAnalysis->missing_security_features) ? count($latestAnalysis->missing_security_features) : 0;
                                    $presentCount = $totalSecurityHeaders - $missingCount;
                                @endphp
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        <span class="badge bg-success">{{ $presentCount }} Present</span>
                                    </div>
                                    <div>
                                        <span class="badge bg-danger">{{ $missingCount }} Missing</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Key Headers</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                        $keyHeaders = [
                                            'Strict-Transport-Security' => 'HSTS',
                                            'X-Frame-Options' => 'X-Frame',
                                            'Content-Security-Policy' => 'CSP',
                                            'X-Content-Type-Options' => 'X-CTO',
                                        ];
                                        $foundHeaders = [];
                                        foreach ($keyHeaders as $headerName => $shortName) {
                                            foreach ($headers as $key => $value) {
                                                if (strcasecmp($key, $headerName) === 0) {
                                                    $foundHeaders[] = $shortName;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(count($foundHeaders) > 0)
                                        @foreach($foundHeaders as $header)
                                            <span class="badge bg-success">{{ $header }}</span>
                                        @endforeach
                                        @if(count($foundHeaders) < count($keyHeaders))
                                            <span class="badge bg-secondary">+{{ count($keyHeaders) - count($foundHeaders) }} more</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">No key headers found</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(is_array($latestAnalysis->missing_security_features) && count($latestAnalysis->missing_security_features) > 0)
                            <hr class="my-3">
                            <div>
                                <h6 class="text-muted mb-2">⚠ Missing Headers ({{ count($latestAnalysis->missing_security_features) }})</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice($latestAnalysis->missing_security_features, 0, 5) as $missing)
                                        <span class="badge bg-danger">{{ $missing['name'] ?? $missing['header'] }}</span>
                                    @endforeach
                                    @if(count($latestAnalysis->missing_security_features) > 5)
                                        <span class="badge bg-secondary">+{{ count($latestAnalysis->missing_security_features) - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Analyzed: {{ $latestAnalysis->created_at->diffForHumans() }}
                            </div>
                            <a href="{{ route('analysis.show', $latestAnalysis->id) }}" class="btn btn-primary">
                                View Full Details →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Analyses List -->
            @if($analyses->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Analyses</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Security Score</th>
                                        <th>Analyzed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analyses as $analysis)
                                        <tr>
                                            <td>
                                                <a href="{{ $analysis->url }}" target="_blank" class="text-decoration-none">
                                                    {{ $analysis->url }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge {{ $analysis->security_score >= 80 ? 'bg-success' : ($analysis->security_score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $analysis->security_score }}%
                                                </span>
                                            </td>
                                            <td>{{ $analysis->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('analysis.show', $analysis->id) }}" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('history') }}" class="btn btn-outline-secondary">View All History</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="text-muted mb-0">No analyses yet. Start by analyzing a website above!</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
