@extends('layouts.app')

@section('title', 'Security Analysis Report - ' . $analysis->url)

@php
    $description = 'Detailed security header analysis for ' . $analysis->url . '. Security Score: ' . $analysis->security_score . '%. ' . 
                   (is_array($analysis->missing_security_features) && count($analysis->missing_security_features) > 0 
                   ? count($analysis->missing_security_features) . ' missing security headers detected. ' 
                   : 'All security headers present. ') .
                   'View complete header analysis, recommendations, and missing security features.';
    $ogImage = $analysis->screenshot_url ?? url('/images/og-image.png');
@endphp

@section('meta')
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="security analysis, {{ $analysis->url }}, HTTP headers, security headers, {{ $analysis->security_score }}%, website security">
    @php
        $ogTitle = 'Security Analysis: ' . $analysis->url . ' | AnaEvaRec';
        $ogDescription = 'Security Score: ' . $analysis->security_score . '% - ' . 
                        (is_array($analysis->missing_security_features) && count($analysis->missing_security_features) > 0 
                        ? count($analysis->missing_security_features) . ' missing headers' 
                        : 'All headers present');
        $ogImageAlt = 'Security analysis report for ' . $analysis->url;
    @endphp
@endsection

@section('content')
@php
    $headersRaw = is_array($analysis->headers) ? $analysis->headers : [];
    $headersNormalized = array_change_key_case($headersRaw, CASE_LOWER);

    $keyHeadersMap = [
        'content-security-policy' => 'CSP',
        'strict-transport-security' => 'HSTS',
        'x-frame-options' => 'X-Frame',
        'x-content-type-options' => 'X-CTO',
        'referrer-policy' => 'Referrer',
        'permissions-policy' => 'PP',
        'cross-origin-resource-policy' => 'CORP',
        'cross-origin-embedder-policy' => 'COEP',
        'cross-origin-opener-policy' => 'COOP',
    ];

    // Map for additional headers (full label to short form)
    $additionalHeadersMap = [
        'X-XSS-Protection' => 'X-XSS',
        'X-Permitted-Cross-Domain-Policies' => 'X-PCDP',
        'Expect-CT' => 'Expect-CT',
        'Report-To' => 'Report-To',
        'Clear-Site-Data' => 'CSD',
    ];

    // Map for missing headers to short forms (for responsive display)
    $missingHeadersMap = [
        'Content Security Policy (CSP)' => 'CSP',
        'Content-Security-Policy' => 'CSP',
        'Strict-Transport-Security' => 'HSTS',
        'HTTP Strict Transport Security' => 'HSTS',
        'HSTS' => 'HSTS',
        'X-Frame-Options' => 'X-Frame',
        'X-Content-Type-Options' => 'X-CTO',
        'Referrer-Policy' => 'Referrer',
        'Permissions Policy' => 'PP',
        'Permissions-Policy' => 'PP',
        'X-Permitted-Cross-Domain-Policies' => 'X-PCDP',
        'Cross-Origin-Resource-Policy' => 'CORP',
        'Cross-Origin-Embedder-Policy' => 'COEP',
        'Cross-Origin-Opener-Policy' => 'COOP',
        'Clear-Site-Data' => 'CSD',
        'Expect-CT' => 'Expect-CT',
        'Report-To' => 'Report-To',
        'X-XSS-Protection' => 'X-XSS',
    ];

    // OWASP-style Security Header Weights (Total = 100)
    $expectedSecurityHeaders = [
        'content-security-policy' => [
            'label' => 'Content-Security-Policy',
            'description' => 'Defines trusted content sources to block injections.',
            'weight' => 25,
        ],
        'strict-transport-security' => [
            'label' => 'Strict-Transport-Security',
            'description' => 'Forces browsers to use HTTPS connections.',
            'weight' => 18,
        ],
        'x-frame-options' => [
            'label' => 'X-Frame-Options',
            'description' => 'Protects against clickjacking by controlling iframe usage.',
            'weight' => 12,
        ],
        'x-content-type-options' => [
            'label' => 'X-Content-Type-Options',
            'description' => 'Prevents MIME-type sniffing for scripts and styles.',
            'weight' => 12,
        ],
        'permissions-policy' => [
            'label' => 'Permissions-Policy',
            'description' => 'Restricts access to powerful browser features.',
            'weight' => 10,
        ],
        'referrer-policy' => [
            'label' => 'Referrer-Policy',
            'description' => 'Controls how much referrer information is sent.',
            'weight' => 5,
        ],
        'x-xss-protection' => [
            'label' => 'X-XSS-Protection',
            'description' => 'Enables XSS filtering in browsers.',
            'weight' => 4,
        ],
        'x-permitted-cross-domain-policies' => [
            'label' => 'X-Permitted-Cross-Domain-Policies',
            'description' => 'Controls cross-domain policy files.',
            'weight' => 3,
        ],
        'cross-origin-resource-policy' => [
            'label' => 'Cross-Origin-Resource-Policy',
            'description' => 'Controls how the browser handles cross-origin resource requests.',
            'weight' => 2,
        ],
        'cross-origin-embedder-policy' => [
            'label' => 'Cross-Origin-Embedder-Policy',
            'description' => 'Prevents a document from loading cross-origin resources that don\'t explicitly grant permission.',
            'weight' => 1,
        ],
        'cross-origin-opener-policy' => [
            'label' => 'Cross-Origin-Opener-Policy',
            'description' => 'Isolates browsing context by preventing other origins from accessing the window object.',
            'weight' => 1,
        ],
        'expect-ct' => [
            'label' => 'Expect-CT',
            'description' => 'Enforces Certificate Transparency compliance.',
            'weight' => 1,
        ],
        'report-to' => [
            'label' => 'Report-To',
            'description' => 'Specifies endpoints for reporting API violations.',
            'weight' => 1,
        ],
        'clear-site-data' => [
            'label' => 'Clear-Site-Data',
            'description' => 'Clears browsing data when specified.',
            'weight' => 0,
        ],
    ];

    $presentHeadersDetails = [];
    $presentKeyHeaders = [];
    $additionalPresentHeaders = [];

    foreach ($expectedSecurityHeaders as $headerKey => $meta) {
        if (array_key_exists($headerKey, $headersNormalized) && !empty($headersNormalized[$headerKey])) {
            $presentHeadersDetails[] = [
                'key' => $headerKey,
                'label' => $meta['label'],
                'description' => $meta['description'] ?? null,
                'value' => $headersNormalized[$headerKey],
            ];

            if (array_key_exists($headerKey, $keyHeadersMap)) {
                $presentKeyHeaders[] = $keyHeadersMap[$headerKey];
            } else {
                // Use short form if available, otherwise use the label
                $shortForm = $additionalHeadersMap[$meta['label']] ?? $meta['label'];
                $additionalPresentHeaders[] = $shortForm;
            }
        }
    }

    $missingCount = is_array($analysis->missing_security_features) ? count($analysis->missing_security_features) : 0;

    // OWASP-style Grade Scale
    $grade = 'F';
    $gradeClass = 'text-danger';
    $gradeRingBackground = '#fde2e1';
    $gradeRingBorder = '#f66b6b';
    $gradeDescription = 'Fail / no security headers';

    if ($analysis->security_score >= 85) {
        $grade = 'A';
        $gradeClass = 'text-success';
        $gradeRingBackground = '#d1e7dd';
        $gradeRingBorder = '#52b788';
        $gradeDescription = 'Excellent security';
    } elseif ($analysis->security_score >= 70) {
        $grade = 'B';
        $gradeClass = 'text-success';
        $gradeRingBackground = '#cff4fc';
        $gradeRingBorder = '#38a3a5';
        $gradeDescription = 'Good security';
    } elseif ($analysis->security_score >= 55) {
        $grade = 'C';
        $gradeClass = 'text-warning';
        $gradeRingBackground = '#fff3cd';
        $gradeRingBorder = '#fcbf49';
        $gradeDescription = 'Fair security';
    } elseif ($analysis->security_score >= 40) {
        $grade = 'D';
        $gradeClass = 'text-warning';
        $gradeRingBackground = '#ffe5d9';
        $gradeRingBorder = '#f77f00';
        $gradeDescription = 'Weak security';
    } elseif ($analysis->security_score >= 25) {
        $grade = 'E';
        $gradeClass = 'text-danger';
        $gradeRingBackground = '#fde2e1';
        $gradeRingBorder = '#f66b6b';
        $gradeDescription = 'Poor security';
    }

    $missingHeadersList = [];
    if (is_array($analysis->missing_security_features)) {
        foreach ($analysis->missing_security_features as $missing) {
            $headerName = $missing['name'] ?? $missing['header'] ?? 'Security Header';
            // Use short form if available, otherwise use the full name
            $shortForm = $missingHeadersMap[$headerName] ?? $missingHeadersMap[$missing['header'] ?? ''] ?? $headerName;
            $missingHeadersList[] = $shortForm;
        }
    }

    $displayMissingHeaders = $missingHeadersList; // Show all missing headers
    $missingOverflowCount = 0; // No overflow since we show all

    $encodedUrl = urlencode($analysis->url);
    $cleanUrl = $analysis->url;
    // Multiple fallback services - prioritize Thum.io (free, working, reliable)
    // Thum.io is tested and confirmed working
    $screenshotServices = [
        $analysis->screenshot_url ?? null,
        "https://image.thum.io/get/width/1200/crop/675/noanimate/{$cleanUrl}", // Thum.io path-based - primary
        "https://image.thum.io/get/width/1200/crop/675/noanimate?url={$encodedUrl}", // Thum.io query-based
        "https://image.thum.io/get/fullpage/width/1200/height/675/noanimate/{$cleanUrl}", // Thum.io full page
    ];
    $screenshotUrl = collect($screenshotServices)->first(fn($url) => !empty($url));
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap');
    
    body {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 30%, #93c5fd 60%, #60a5fa 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Screenshot styling */
    #screenshot-img {
        width: 100% !important;
        height: auto !important;
        object-fit: contain !important;
        object-position: center !important;
        display: block !important;
        max-width: 100%;
        margin: 0 auto;
        border-radius: 8px;
    }
    
    /* Container for screenshot to maintain aspect ratio */
    .screenshot-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        background-color: transparent;
        padding: 0;
        min-height: 300px;
    }

    .recent-analyses-card {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
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

    .recent-analyses-table th:nth-child(1),
    .recent-analyses-table td:nth-child(1) {
        text-align: left;
    }

    .recent-analyses-table th:nth-child(2),
    .recent-analyses-table td:nth-child(2) {
        text-align: left;
        padding-right: 5px;
        max-width: 300px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .recent-analyses-table th:nth-child(3),
    .recent-analyses-table td:nth-child(3) {
        text-align: left;
        padding-left: 5px;
        padding-right: 10px;
    }

    .recent-analyses-table a.table-link {
        color: #4f46e5;
        font-weight: 600;
        text-decoration: none;
    }

    .recent-analyses-table a.table-link:hover {
        text-decoration: underline;
    }

    /* Back to History button - white background for visibility */
    a.btn-outline-secondary[href*="history"] {
        background-color: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.15) !important;
        color: #374151 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    a.btn-outline-secondary[href*="history"]:hover {
        background-color: #f8f9fa !important;
        border-color: rgba(0, 0, 0, 0.2) !important;
        color: #1f2937 !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }
</style>

<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('history') }}"
           class="btn btn-outline-secondary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 fw-semibold">
            <span aria-hidden="true">←</span>
            Back to History
        </a>
    </div>

    <h3 class="fw-bold mb-4" style="font-family: 'Rubik', sans-serif;">SECURITY REPORT SUMMARY</h3>

    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-4 bg-white">
            <div class="row g-3 align-items-center mb-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="text-uppercase small text-muted fw-semibold mb-1" style="font-family: 'Rubik', sans-serif;">Website URL</div>
                            <div style="word-break: break-all;">
                                <a href="{{ $analysis->url }}" target="_blank" class="text-decoration-none fw-semibold fs-5 text-primary">
                                    {{ $analysis->url }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-uppercase small text-muted fw-semibold mb-1" style="font-family: 'Rubik', sans-serif;">Report Time</div>
                            <div class="fw-semibold fs-5">
                                {{ $analysis->created_at->timezone('Asia/Kuala_Lumpur')->format('F j, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('analysis.show', $analysis->id) }}?download=pdf" class="btn btn-primary fw-semibold rounded-pill px-4">
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                <div class="col-lg-4 col-md-6">
                    <div class="h-100 border rounded-4 p-4 bg-white d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="text-uppercase small text-muted fw-semibold mb-3" style="font-family: 'Rubik', sans-serif;">Security Grade</div>
                        <div class="ratio ratio-1x1" style="max-width: 160px; width: 55%;">
                            <div class="rounded-circle d-flex flex-column align-items-center justify-content-center"
                                 style="background-color: {{ $gradeRingBackground }}; border: 8px solid {{ $gradeRingBorder }};">
                                <span class="display-3 fw-bold {{ $gradeClass }}" style="font-size: clamp(2.5rem, 6vw, 4rem);">{{ $grade }}</span>
                            </div>
                        </div>
                        <div class="badge px-4 py-2 mt-4" style="font-size: 0.95rem; background-color: {{ $gradeRingBorder }}; color: #fff; border-radius: 999px;">
                            Score {{ $analysis->security_score }}%
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="h-100 border rounded-4 p-4 bg-white text-center">
                        <div class="text-uppercase small text-muted fw-semibold mb-2" style="font-family: 'Rubik', sans-serif;">Security Headers Status</div>
                        <div class="d-flex gap-2 mb-3 justify-content-center">
                            <span class="badge rounded-pill bg-success px-3 py-2">{{ count($presentHeadersDetails) }} Present</span>
                            <span class="badge rounded-pill {{ $missingCount > 0 ? 'bg-danger' : 'bg-success' }} px-3 py-2">{{ $missingCount }} Missing</span>
                        </div>
                        <div class="mb-3">
                            @php
                                $keyHeadersCount = count(array_merge($presentKeyHeaders, $additionalPresentHeaders));
                            @endphp
                            <div class="text-uppercase small text-muted fw-semibold mb-2" style="font-family: 'Rubik', sans-serif;">Key Headers ({{ $keyHeadersCount }})</div>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @if(count($presentKeyHeaders) > 0 || count($additionalPresentHeaders) > 0)
                                    @foreach(array_merge($presentKeyHeaders, $additionalPresentHeaders) as $headerLabel)
                                        <span class="badge rounded-pill bg-success px-3 py-2">{{ $headerLabel }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">No key headers detected.</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-uppercase small text-muted fw-semibold mb-2" style="font-family: 'Rubik', sans-serif;">Missing Headers ({{ $missingCount }})</div>
                            @if($missingCount > 0)
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    @foreach($displayMissingHeaders as $missingHeader)
                                        <span class="badge rounded-pill bg-danger px-3 py-2">{{ $missingHeader }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge rounded-pill bg-success px-3 py-2">All headers present</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="h-100 border rounded-4 bg-white p-3 d-flex flex-column align-items-center">
                        <div class="text-uppercase small text-muted fw-semibold mb-2 text-center" style="font-family: 'Rubik', sans-serif;">Website Preview</div>
                        <div class="screenshot-container position-relative flex-grow-1 d-flex align-items-center justify-content-center w-100">
                            @if(!empty($screenshotUrl))
                            <img
                                id="screenshot-img"
                                data-screenshot-url="{{ $screenshotUrl }}"
                                src="{{ $screenshotUrl }}"
                                alt="Website Screenshot for {{ $analysis->url }}"
                                class="img-fluid"
                                onerror="handleScreenshotError(this)"
                                loading="lazy"
                            >
                            @else
                            <div class="h-100 w-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background-color: transparent; min-height: 300px;">
                                <div style="font-size: 2.5rem;">🌐</div>
                                <div class="mt-2 fw-semibold text-center px-3">Screenshot unavailable</div>
                                <div class="small text-center px-3">{{ $analysis->url }}</div>
                                <a href="{{ $analysis->url }}" class="btn btn-outline-primary btn-sm mt-3" target="_blank">
                                    Visit Website
                                </a>
                            </div>
                            @endif
                            <iframe
                                id="website-iframe"
                                src="{{ $analysis->url }}"
                                style="width: 100%; height: 100%; border: none; display: none;"
                                sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                                loading="lazy"
                            ></iframe>
                            <div id="screenshot-placeholder"
                                 class="d-none h-100 w-100 position-absolute top-0 start-0 d-flex flex-column align-items-center justify-content-center text-muted"
                                 style="background-color: transparent; min-height: 300px;">
                                <div style="font-size: 2.5rem;">🌐</div>
                                <div class="mt-2 fw-semibold text-center px-3">Screenshot unavailable</div>
                                <div class="small text-center px-3">{{ $analysis->url }}</div>
                                <a href="{{ $analysis->url }}" class="btn btn-outline-primary btn-sm mt-3" target="_blank">
                                    Visit Website
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                <h5 class="fw-bold mb-0" style="font-family: 'Rubik', sans-serif;">Present Security Headers</h5>
                <span class="badge bg-success">
                    {{ count($presentHeadersDetails) }} {{ \Illuminate\Support\Str::plural('item', count($presentHeadersDetails)) }}
                </span>
            </div>

            @if(count($presentHeadersDetails) > 0)
                <div class="row g-3">
                    @foreach($presentHeadersDetails as $header)
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 p-3 position-relative" style="background-color: rgba(25,135,84,0.08); border: 1px solid rgba(25,135,84,0.3);">
                                <span class="badge bg-success position-absolute top-0 end-0" style="margin: 12px;">Present</span>
                                <div style="padding-right: 80px;">
                                    <div class="fw-bold text-success mb-1" style="word-break: break-word;">{{ $header['label'] }}</div>
                                    @if(!empty($header['description']))
                                        <div class="small text-black mb-2" style="word-break: break-word; overflow-wrap: break-word;">
                                            {{ $header['description'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    No expected security headers were detected as present.
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                <h5 class="fw-bold mb-0" style="font-family: 'Rubik', sans-serif;">Missing Security Headers</h5>
                <span class="badge {{ $missingCount > 0 ? 'bg-danger' : 'bg-success' }}">
                    {{ $missingCount }} {{ \Illuminate\Support\Str::plural('item', $missingCount) }}
                </span>
            </div>

            @if($missingCount > 0)
                <div class="row g-3">
                    @foreach($analysis->missing_security_features as $missing)
                        @php
                            $missingName = $missing['name'] ?? $missing['header'] ?? 'Security Header';
                            $missingDescription = $missing['description'] ?? 'No description provided.';
                        @endphp
                        <div class="col-12 col-md-6">
                            <div class="rounded-4 p-3 h-100 position-relative" style="background-color: rgba(220,53,69,0.08); border: 1px solid rgba(220,53,69,0.3);">
                                <span class="badge bg-danger position-absolute top-0 end-0" style="margin: 12px;">Missing</span>
                                <div style="padding-right: 80px;">
                                    <div class="fw-bold text-danger mb-1" style="word-break: break-word;">{{ $missingName }}</div>
                                    <div class="small text-black mb-2" style="word-break: break-word; overflow-wrap: break-word;">
                                        {{ $missingDescription }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-success mb-0">
                    All essential security headers are present. Great work!
                </div>
            @endif
        </div>
    </div>

    @if(is_array($analysis->recommendations) && count($analysis->recommendations) > 0)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <h5 class="fw-bold mb-0" style="font-family: 'Rubik', sans-serif;">Security Recommendations</h5>
                    <span class="badge bg-primary">
                        {{ count($analysis->recommendations) }} {{ \Illuminate\Support\Str::plural('recommendation', count($analysis->recommendations)) }}
                    </span>
                </div>
                <div class="row g-3">
                    @foreach($analysis->recommendations as $index => $recommendation)
                        <div class="col-12">
                            <div class="rounded-4 p-4 h-100" style="background-color: rgba(13,110,253,0.08); border: 1px solid rgba(13,110,253,0.2);">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center" style="padding-top: 0.125rem;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" 
                                             style="width: 32px; height: 32px; background-color: #0d6efd; font-size: 0.875rem; min-width: 32px; flex-shrink: 0;">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 d-flex align-items-center" style="min-width: 0; word-wrap: break-word; overflow-wrap: break-word;">
                                        <p class="mb-0 text-dark fw-bold text-justify" style="line-height: 1.6; word-break: break-word; overflow-wrap: break-word;">{{ $recommendation }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
        <div class="card-body p-0">
            <!-- Header Section with Enhanced Gradient Background -->
            <div class="p-3 text-white position-relative" style="background: linear-gradient(135deg, {{ $gradeRingBorder }} 0%, {{ $gradeRingBorder }}cc 100%); overflow: hidden;">
                <!-- Decorative Background Elements -->
                <div class="position-absolute top-0 end-0" style="width: 100px; height: 100px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; transform: translate(30%, -30%);"></div>
                <div class="position-absolute bottom-0 start-0" style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.08); border-radius: 50%; transform: translate(-30%, 30%);"></div>
                
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 position-relative">
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-0" style="font-size: 1.5rem; letter-spacing: -0.01em; font-family: 'Rubik', sans-serif;">Overall Website Security</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="text-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.15) 100%); backdrop-filter: blur(15px); border: 2px solid rgba(255, 255, 255, 0.4);">
                                <span class="h4 fw-bold mb-0" style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">{{ $grade }}</span>
                            </div>
                            <small class="d-block mt-1 opacity-95 fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.05em; text-transform: uppercase;">Grade</small>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.15) 100%); backdrop-filter: blur(15px); border: 2px solid rgba(255, 255, 255, 0.4);">
                                <span class="h5 fw-bold mb-0" style="text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">{{ $analysis->security_score }}%</span>
                            </div>
                            <small class="d-block mt-1 opacity-95 fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.05em; text-transform: uppercase;">Score</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section with Enhanced Design -->
            <div class="p-4 d-flex flex-column">
                <!-- Security Description with Better Layout -->
                <div class="mb-4">
                    <p class="mb-0" style="line-height: 1.75; font-size: 1rem; color: #374151; font-weight: 400; text-align: justify;">
                        @if($analysis->security_score >= 85)
                            This website demonstrates <strong class="text-success" style="font-weight: 600;">excellent security practices</strong> with a comprehensive set of security headers in place. The implementation shows strong protection against common web vulnerabilities including clickjacking, XSS attacks, and MIME-type sniffing. The security configuration indicates a well-maintained and security-conscious approach to web application development.
                        @elseif($analysis->security_score >= 70)
                            This website shows <strong class="text-success" style="font-weight: 600;">good security posture</strong> with most critical security headers implemented. While the current configuration provides solid protection against many common threats, there are opportunities to enhance security further by implementing additional recommended headers. The existing security measures demonstrate a proactive approach to web security.
                        @elseif($analysis->security_score >= 55)
                            This website has a <strong class="text-warning" style="font-weight: 600;">fair security posture</strong> with some essential security headers in place. However, several important security headers are missing, which could leave the site vulnerable to certain types of attacks. Implementing the missing headers would significantly improve the overall security and provide better protection against modern web threats.
                        @elseif($analysis->security_score >= 40)
                            This website has a <strong class="text-warning" style="font-weight: 600;">weak security posture</strong> with limited security headers implemented. Multiple critical security headers are missing, which increases the risk of various web-based attacks including clickjacking, XSS, and data injection. Immediate action is recommended to implement the missing security headers to reduce vulnerability exposure.
                        @elseif($analysis->security_score >= 25)
                            This website has a <strong class="text-danger" style="font-weight: 600;">poor security posture</strong> with very few security headers implemented. The site is vulnerable to a wide range of attacks including clickjacking, XSS, MIME-type sniffing, and more. The lack of proper security headers exposes the website and its users to significant risks. Urgent implementation of security headers is strongly recommended.
                        @else
                            This website has <strong class="text-danger" style="font-weight: 600;">failed security standards</strong> with no or minimal security headers implemented. The site is highly vulnerable to attacks including clickjacking, XSS, MIME-type sniffing, and more. The complete absence of proper security headers exposes the website and its users to critical risks. Immediate and comprehensive implementation of security headers is essential to protect against common web vulnerabilities.
                        @endif
                    </p>
                </div>

                <!-- Enhanced Stats Cards - Positioned at the bottom -->
                <div class="row g-2" style="margin-top: auto;">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 p-2 rounded-3 shadow-sm transition-all" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 3px solid #22c55e; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(34, 197, 94, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0, 0, 0, 0.1)'">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                                    <span class="fw-bold text-white" style="font-size: 1rem;">{{ count($presentHeadersDetails) }}</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.85rem; color: #16a34a; letter-spacing: 0.02em;">Headers Present</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 p-2 rounded-3 shadow-sm transition-all" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 3px solid #ef4444; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0, 0, 0, 0.1)'">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                    <span class="fw-bold text-white" style="font-size: 1rem;">{{ $missingCount }}</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.85rem; color: #dc2626; letter-spacing: 0.02em;">Headers Missing</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="recent-analyses-card bg-white shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-4" style="font-family: 'Rubik', sans-serif;">Additional Informations - Standard HTTP Headers</h5>
        @if(is_array($analysis->headers) && count($analysis->headers) > 0)
            @php
                $securityHeaderKeys = array_keys($expectedSecurityHeaders);
                $infoHeaders = ['server', 'x-powered-by', 'date', 'connection', 'transfer-encoding', 'content-length'];
                $safeHeaders = ['content-type', 'cache-control', 'accept-ranges', 'etag', 'last-modified', 'expires', 'vary'];
                $warningHeaders = ['x-aspnet-version', 'x-aspnetmvc-version'];
                
                // Header descriptions mapping
                $headerDescriptions = [
                    // Security Headers
                    'content-security-policy' => 'Defines trusted content sources to block XSS and injection attacks.',
                    'strict-transport-security' => 'Forces browsers to use HTTPS connections only.',
                    'x-frame-options' => 'Protects against clickjacking by controlling iframe usage.',
                    'x-content-type-options' => 'Prevents MIME-type sniffing for scripts and styles.',
                    'referrer-policy' => 'Controls how much referrer information is sent with requests.',
                    'permissions-policy' => 'Restricts access to powerful browser features and APIs.',
                    'cross-origin-resource-policy' => 'Controls how the browser handles cross-origin resource requests.',
                    'cross-origin-embedder-policy' => 'Prevents loading cross-origin resources without explicit permission.',
                    'cross-origin-opener-policy' => 'Isolates browsing context by preventing cross-origin window access.',
                    'expect-ct' => 'Enforces Certificate Transparency compliance.',
                    'report-to' => 'Specifies endpoints for reporting API violations.',
                    'x-xss-protection' => 'Enables XSS filtering in older browsers.',
                    'x-permitted-cross-domain-policies' => 'Controls cross-domain policy files for Flash/PDF.',
                    'clear-site-data' => 'Clears browsing data when specified.',
                    
                    // Standard Headers
                    'server' => 'Identifies the server software and version.',
                    'date' => 'The date and time when the response was generated.',
                    'content-type' => 'Specifies the media type of the resource.',
                    'content-length' => 'The size of the response body in bytes.',
                    'connection' => 'Controls whether the network connection stays open.',
                    'transfer-encoding' => 'Specifies the form of encoding used to transfer the entity.',
                    'cache-control' => 'Directives for caching mechanisms in requests and responses.',
                    'expires' => 'Date/time after which the response is considered stale.',
                    'last-modified' => 'The last modification date of the resource.',
                    'etag' => 'Entity tag for cache validation.',
                    'vary' => 'Tells caches which request headers to consider when determining cacheability.',
                    'accept-ranges' => 'Indicates which range units are supported.',
                    
                    // Cloudflare/Proxy Headers
                    'cf-ray' => 'Cloudflare Ray ID for request tracking.',
                    'cf-cache-status' => 'Cloudflare cache status (HIT, MISS, etc.).',
                    'cf-request-id' => 'Cloudflare request identifier.',
                    'nel' => 'Network Error Logging configuration.',
                    
                    // Other Headers
                    'x-powered-by' => 'Reveals the server technology stack (security risk if exposed).',
                    'alt-svc' => 'Alternative service advertisement for HTTP/2 or HTTP/3.',
                    'location' => 'Used in redirects to specify the target URL.',
                    'set-cookie' => 'Sends cookies from the server to the user agent.',
                ];
            @endphp
            <div class="table-responsive">
                <table class="recent-analyses-table">
                    <thead>
                        <tr>
                            <th>Header</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analysis->headers as $name => $value)
                            @php
                                $headerValue = is_array($value) ? json_encode($value) : $value;
                                $headerLower = strtolower($name);
                                
                                // Get description
                                $description = $headerDescriptions[$headerLower] ?? 
                                               (isset($expectedSecurityHeaders[$headerLower]) ? $expectedSecurityHeaders[$headerLower]['description'] : 
                                               'Standard HTTP header with no specific security implications.');
                                
                                // Determine status based on header type
                                if (in_array($headerLower, $securityHeaderKeys) || in_array($headerLower, $safeHeaders)) {
                                    $status = 'Safe';
                                    $statusClass = 'bg-success';
                                } elseif (in_array($headerLower, $infoHeaders)) {
                                    $status = 'Info';
                                    $statusClass = 'bg-info';
                                } elseif (in_array($headerLower, $warningHeaders)) {
                                    $status = 'Warning';
                                    $statusClass = 'bg-warning text-dark';
                                } else {
                                    $status = 'Safe';
                                    $statusClass = 'bg-success';
                                }
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $name }}</td>
                                <td style="padding-right: 5px !important; max-width: 300px; word-wrap: break-word; overflow-wrap: break-word;">
                                    <code class="small" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; color: #495057; display: block;">
                                        {{ $headerValue }}
                                    </code>
                                </td>
                                <td class="text-muted small" style="max-width: 300px; padding-left: 5px !important;">
                                    {{ $description }}
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No headers detected for this scan.</p>
        @endif
    </div>
</div>

<script>
    const screenshotFallbacks = @json($screenshotServices);
    let currentFallbackIndex = 0;
    let triedIframe = false;

    let retryCount = 0;
    const maxRetries = 5;
    const retryDelay = 2000; // 2 seconds

    function handleScreenshotError(img) {
        const screenshotUrl = img.dataset.screenshotUrl || img.src;
        
        // Check if this is a ScreenshotOne URL that failed (likely limit reached)
        if (screenshotUrl && screenshotUrl.includes('screenshotone.com')) {
            console.log('ScreenshotOne failed, switching to fallback services immediately');
            currentFallbackIndex = 1; // Skip to first fallback
            retryCount = maxRetries; // Don't retry ScreenshotOne
        }
        
        // First, retry the same URL multiple times (for async services) - but not for ScreenshotOne errors
        if (retryCount < maxRetries && currentFallbackIndex === 0 && !screenshotUrl.includes('screenshotone.com')) {
            retryCount++;
            if (screenshotUrl) {
                console.log(`Retrying screenshot load (attempt ${retryCount}/${maxRetries})`);
                setTimeout(function() {
                    img.src = screenshotUrl;
                }, retryDelay);
                return;
            }
        }
        
        // If retries exhausted or ScreenshotOne failed, try next fallback service
        currentFallbackIndex++;
        if (currentFallbackIndex < screenshotFallbacks.length && screenshotFallbacks[currentFallbackIndex]) {
            retryCount = 0; // Reset retry count for new service
            console.log(`Trying fallback service ${currentFallbackIndex + 1}`);
            img.src = screenshotFallbacks[currentFallbackIndex];
        } else if (!triedIframe) {
            triedIframe = true;
            img.style.display = 'none';
            const iframe = document.getElementById('website-iframe');
            if (iframe) {
                iframe.style.display = 'block';
                setTimeout(function () {
                    if (iframe.style.display === 'block') {
                        try {
                            const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;
                            if (!iframeDoc || iframeDoc.body.children.length === 0) {
                                showPlaceholder();
                            }
                        } catch (e) {
                            // Cross-origin access blocked
                            showPlaceholder();
                        }
                    }
                }, 10000);
            }
        } else {
            showPlaceholder();
        }
    }

    function showPlaceholder() {
        const img = document.getElementById('screenshot-img');
        const iframe = document.getElementById('website-iframe');
        const placeholder = document.getElementById('screenshot-placeholder');
        if (img) img.style.display = 'none';
        if (iframe) iframe.style.display = 'none';
        if (placeholder) placeholder.classList.remove('d-none');
    }
</script>
@endsection

