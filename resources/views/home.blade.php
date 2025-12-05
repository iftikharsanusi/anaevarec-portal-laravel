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
<style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap');
    
    body {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 30%, #93c5fd 60%, #60a5fa 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    .recent-analyses-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
    }

    .analysis-preview-card {
        border-radius: 18px;
        overflow: hidden;
    }

    .recent-analyses-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        background-color: #fdfdff;
    }

    .recent-analyses-table thead th {
        background-color: #f3f4ff;
        color: #374151;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .recent-analyses-table tbody tr {
        background-color: #ffffff;
    }

    .recent-analyses-table tbody tr + tr td {
        border-top: 1px solid #f1f3f8;
    }

    .recent-analyses-table th,
    .recent-analyses-table td {
        padding: 16px 20px;
        border: none;
        vertical-align: middle;
        text-align: center;
    }

    .recent-analyses-table a.table-link {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
    }

    .recent-analyses-table a.table-link:hover {
        text-decoration: underline;
    }

    .recent-analyses-table th:nth-child(1),
    .recent-analyses-table td:nth-child(1) {
        text-align: left;
    }

    .recent-analyses-table th:nth-child(2),
    .recent-analyses-table td:nth-child(2) {
        text-align: center;
    }

    .recent-analyses-table th:nth-child(3),
    .recent-analyses-table td:nth-child(3) {
        text-align: left;
    }

    .recent-analyses-table th:nth-child(4),
    .recent-analyses-table td:nth-child(4) {
        text-align: center;
    }

    .hero-heading {
        font-family: 'Rubik', sans-serif;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .recent-analyses-heading {
        font-family: 'Rubik', sans-serif;
        font-weight: 700;
    }

    .modern-success-alert {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(52, 211, 153, 0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        animation: slideDown 0.3s ease-out;
        position: relative;
        overflow: hidden;
    }

    .modern-success-alert::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: rgba(255, 255, 255, 0.5);
    }

    .modern-success-alert-content {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
        font-weight: 700;
        font-size: 1.05rem;
        flex: 1;
    }

    .modern-success-alert-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #ffffff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 18px;
        line-height: 1;
        flex-shrink: 0;
        margin-left: 12px;
    }

    .modern-success-alert-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4 hero-heading">Web Security Header Analyzer</h1>
            <h5 class="text-muted mb-4">Scan any website you want by just enter the website URL below:</h5>

            <!-- Analysis Form -->
            <div class="mb-4">
                <form id="analyze-form" action="{{ route('analyze') }}" method="POST">
                    @csrf
                    <div class="shadow-sm rounded-4 overflow-hidden" style="background-color: transparent;">
                        <div class="input-group input-group-lg">
                            <input type="text" name="url" class="form-control border-0 bg-white" placeholder="Enter website URL (e.g., example.com)" required>
                            <button type="submit" id="analyze-btn" class="btn btn-primary px-4">
                                <span id="analyze-text">Analyze</span>
                                <span id="analyze-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                    @error('url')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </form>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="modern-success-alert" role="alert">
                    <div class="modern-success-alert-content">
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="modern-success-alert-close" onclick="this.parentElement.remove()" aria-label="Close">×</button>
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
                @php
                    $headers = $latestAnalysis->headers ?? [];
                    $headersNormalized = array_change_key_case($headers, CASE_LOWER);
                    
                    // OWASP-style Security Headers (11 total)
                    $expectedSecurityHeaders = [
                        'content-security-policy',
                        'strict-transport-security',
                        'x-frame-options',
                        'x-content-type-options',
                        'referrer-policy',
                        'permissions-policy',
                        'cross-origin-resource-policy',
                        'cross-origin-embedder-policy',
                        'cross-origin-opener-policy',
                        'expect-ct',
                        'report-to',
                    ];
                    
                    // Count present headers
                    $presentCount = 0;
                    foreach ($expectedSecurityHeaders as $headerKey) {
                        if (array_key_exists($headerKey, $headersNormalized) && !empty($headersNormalized[$headerKey])) {
                            $presentCount++;
                        }
                    }
                    
                    $missingCount = is_array($latestAnalysis->missing_security_features) ? count($latestAnalysis->missing_security_features) : 0;
                    
                    // Key headers to display
                    $keyHeaders = [
                        'Content-Security-Policy' => 'CSP',
                        'Strict-Transport-Security' => 'HSTS',
                        'X-Frame-Options' => 'X-Frame',
                        'X-Content-Type-Options' => 'X-CTO',
                        'Referrer-Policy' => 'Referrer',
                        'Permissions-Policy' => 'PP',
                        'Cross-Origin-Resource-Policy' => 'CORP',
                        'Cross-Origin-Embedder-Policy' => 'COEP',
                        'Cross-Origin-Opener-Policy' => 'COOP',
                    ];
                    
                    $foundHeaders = [];
                    foreach ($keyHeaders as $headerName => $shortName) {
                        $headerKey = strtolower($headerName);
                        if (array_key_exists($headerKey, $headersNormalized) && !empty($headersNormalized[$headerKey])) {
                            $foundHeaders[] = $shortName;
                        }
                    }
                    
                    // OWASP-style Grade Scale
                    $grade = 'F';
                    $gradeClass = 'bg-danger';
                    if ($latestAnalysis->security_score >= 85) {
                        $grade = 'A';
                        $gradeClass = 'bg-success';
                    } elseif ($latestAnalysis->security_score >= 70) {
                        $grade = 'B';
                        $gradeClass = 'bg-success';
                    } elseif ($latestAnalysis->security_score >= 55) {
                        $grade = 'C';
                        $gradeClass = 'bg-warning text-dark';
                    } elseif ($latestAnalysis->security_score >= 40) {
                        $grade = 'D';
                        $gradeClass = 'bg-warning text-dark';
                    } elseif ($latestAnalysis->security_score >= 25) {
                        $grade = 'E';
                        $gradeClass = 'bg-danger';
                    }
                @endphp

                <div class="card shadow-sm border-0 mb-4 analysis-preview-card">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                <span class="fw-bold">📊</span>
                            </div>
                            <div>
                                <h5 class="mb-0"><b>ANALYSIS RESULT PREVIEW</b></h5>
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4 align-items-stretch">
                            <div class="col-lg-4">
                                <div class="border rounded-4 p-3 h-100 bg-light d-flex flex-column justify-content-center">
                                    @if(!empty($latestAnalysis->screenshot_url))
                                        <div class="ratio ratio-4x3 rounded-3 overflow-hidden shadow-sm bg-white position-relative">
                                            <img 
                                                id="preview-screenshot-img"
                                                data-screenshot-url="{{ $latestAnalysis->screenshot_url }}"
                                                src="{{ $latestAnalysis->screenshot_url }}" 
                                                alt="Website preview of {{ parse_url($latestAnalysis->url, PHP_URL_HOST) ?? $latestAnalysis->url }}" 
                                                class="w-100 h-100 object-fit-cover"
                                                loading="lazy"
                                                onerror="retryScreenshotLoad(this)"
                                            >
                                            <div class="d-none position-absolute top-50 start-50 translate-middle text-center text-muted px-3 fallback-preview">
                                                <span class="fw-semibold d-block">Preview unavailable</span>
                                                <small class="d-block">Screenshot service failed to load</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-white border rounded-3 d-flex flex-column justify-content-center align-items-center w-100 h-100 py-5 text-muted text-center px-3">
                                            <span class="fw-semibold">No preview available yet</span>
                                            <small class="text-center">Run a fresh scan to capture a screenshot</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7">
                                        <h6 class="text-uppercase text-muted fw-semibold mb-1">Website URL</h6>
                                        <a href="{{ $latestAnalysis->url }}" target="_blank" class="text-decoration-none fw-semibold">
                                            {{ $latestAnalysis->url }}
                                        </a>
                                    </div>
                                    <div class="col-md-5">
                                        <h6 class="text-uppercase text-muted fw-semibold mb-1">Security Score</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1 bg-light rounded-pill overflow-hidden" style="height: 28px;">
                                                <div class="h-100 d-flex align-items-center justify-content-center fw-semibold {{ $gradeClass }}" style="width: {{ max($latestAnalysis->security_score, 5) }}%; {{ $latestAnalysis->security_score >= 55 ? 'color: #fff;' : 'color: #000;' }}">
                                                    {{ $latestAnalysis->security_score }}%
                                                </div>
                                            </div>
                                            <span class="badge rounded-pill fs-6 {{ $gradeClass }}">
                                                {{ $grade }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="text-uppercase text-muted fw-semibold mb-2">Security Headers Status</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge rounded-pill bg-success px-3 py-2">{{ $presentCount }} Present</span>
                                            <span class="badge rounded-pill bg-danger px-3 py-2">{{ $missingCount }} Missing</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-uppercase text-muted fw-semibold mb-2">Key Headers</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if(count($foundHeaders) > 0)
                                                @foreach($foundHeaders as $header)
                                                    <span class="badge rounded-pill bg-success bg-opacity-75 px-3 py-2">{{ $header }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">No key headers detected</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(is_array($latestAnalysis->missing_security_features) && count($latestAnalysis->missing_security_features) > 0)
                                    <div class="mt-4">
                                        <h6 class="text-uppercase text-muted fw-semibold mb-2">Missing Headers ({{ count($latestAnalysis->missing_security_features) }})</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach(array_slice($latestAnalysis->missing_security_features, 0, 5) as $missing)
                                                <span class="badge rounded-pill bg-danger px-3 py-2">{{ $missing['name'] ?? $missing['header'] }}</span>
                                            @endforeach
                                            @if(count($latestAnalysis->missing_security_features) > 5)
                                                <span class="badge rounded-pill bg-secondary px-3 py-2">+{{ count($latestAnalysis->missing_security_features) - 5 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="text-lg fw-bold text-muted">
                                Analyzed {{ $latestAnalysis->created_at->diffInSeconds() < 5 ? 'just now' : $latestAnalysis->created_at->diffForHumans() }}
                            </div>
                            <a href="{{ route('analysis.show', $latestAnalysis->id) }}" class="btn btn-primary px-4">
                                View Full Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Analyses List -->
            @if($analyses->count() > 0)
                <div class="recent-analyses-card bg-white shadow-sm p-4 mb-4">
                    <h5 class="mb-4 recent-analyses-heading text-center">RECENT ANALYSES</h5>
                    <div class="table-responsive">
                        <table class="recent-analyses-table">
                            <thead>
                                <tr class="recent-analyses-heading">
                                    <th class="text-center ">URL</th>
                                    <th class="text-center">Security Score</th>
                                    <th class="text-center">Analyzed</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analyses as $analysis)
                                    @php
                                        $displayHost = parse_url($analysis->url, PHP_URL_HOST) ?? $analysis->url;
                                        $isoTimestamp = $analysis->created_at
                                            ? $analysis->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y H:i:s')
                                            : '';
                                        // OWASP-style Grade Scale for badge colors
                                        if ($analysis->security_score >= 85) {
                                            $scoreBadgeClass = 'bg-success';
                                        } elseif ($analysis->security_score >= 70) {
                                            $scoreBadgeClass = 'bg-success';
                                        } elseif ($analysis->security_score >= 55) {
                                            $scoreBadgeClass = 'bg-warning text-dark';
                                        } elseif ($analysis->security_score >= 40) {
                                            $scoreBadgeClass = 'bg-warning text-dark';
                                        } elseif ($analysis->security_score >= 25) {
                                            $scoreBadgeClass = 'bg-danger';
                                        } else {
                                            $scoreBadgeClass = 'bg-danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center recent-analyses-heading">
                                            <a href="{{ $analysis->url }}" target="_blank" class="table-link d-inline-block">
                                                {{ $displayHost }}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-3 py-2 {{ $scoreBadgeClass }}">
                                                {{ $analysis->security_score }}%
                                            </span>
                                        </td>
                                        <td class="text-muted text-center recent-analyses-heading">{{ $isoTimestamp }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('analysis.show', $analysis->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                                                <b>View Details</b>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('history') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            View All History
                        </a>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted mb-0">No analyses yet. Start by analyzing a website above!</h4>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Retry logic for screenshot loading (ScreenshotOne API)
    function retryScreenshotLoad(img) {
        const maxRetries = 8; // Try up to 8 times
        const retryDelay = 2000; // 2 seconds between retries
        let retryCount = parseInt(img.dataset.retryCount || '0');
        const screenshotUrl = img.dataset.screenshotUrl || img.src;
        
        if (retryCount < maxRetries && screenshotUrl) {
            retryCount++;
            img.dataset.retryCount = retryCount;
            
            // Add timestamp to bypass cache and retry
            const separator = screenshotUrl.includes('?') ? '&' : '?';
            const retryUrl = screenshotUrl.split('?')[0] + separator + '_retry=' + retryCount + '&_t=' + Date.now();
            
            setTimeout(function() {
                // Create new image to test if it loads
                const testImg = new Image();
                testImg.onload = function() {
                    img.src = retryUrl;
                    img.classList.remove('d-none');
                };
                testImg.onerror = function() {
                    if (retryCount < maxRetries) {
                        retryScreenshotLoad(img);
                    } else {
                        // Max retries reached, show fallback
                        img.classList.add('d-none');
                        const fallback = img.nextElementSibling;
                        if (fallback && fallback.classList.contains('fallback-preview')) {
                            fallback.classList.remove('d-none');
                        }
                    }
                };
                testImg.src = retryUrl;
            }, retryDelay);
        } else {
            // Max retries reached, show fallback
            img.classList.add('d-none');
            const fallback = img.nextElementSibling;
            if (fallback && fallback.classList.contains('fallback-preview')) {
                fallback.classList.remove('d-none');
            }
        }
    }
    
    // Also handle initial load errors
    document.addEventListener('DOMContentLoaded', function() {
        const screenshotImg = document.getElementById('preview-screenshot-img');
        if (screenshotImg) {
            screenshotImg.addEventListener('error', function() {
                if (!this.dataset.retryStarted) {
                    this.dataset.retryStarted = 'true';
                    retryScreenshotLoad(this);
                }
            });
        }

        // Handle Analyze button loading state
        const analyzeForm = document.getElementById('analyze-form');
        const analyzeBtn = document.getElementById('analyze-btn');
        const analyzeText = document.getElementById('analyze-text');
        const analyzeSpinner = document.getElementById('analyze-spinner');

        if (analyzeForm && analyzeBtn) {
            analyzeForm.addEventListener('submit', function() {
                // Disable button and show spinner
                analyzeBtn.disabled = true;
                analyzeText.textContent = 'Analyzing...';
                analyzeSpinner.classList.remove('d-none');
            });
        }
    });
</script>
@endsection
