@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            ← Back to Home
        </a>
    </div>

    <!-- Top Section: Screenshot and Report Info -->
    <div class="row mb-4">
        <!-- Left: Screenshot -->
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
            <div class="card">
                <div class="card-body p-0">
                    @php
                        $encodedUrl = urlencode($analysis->url);
                        // Multiple screenshot service fallbacks
                        $screenshotServices = [
                            $analysis->screenshot_url ?? null,
                            "https://mini.s-shot.ru/1024x768/JPEG/1024/Z100/?{$encodedUrl}",
                            "https://image.thum.io/get/width/1200/crop/675/noanimate/{$encodedUrl}",
                            "https://api.pagepeeker.com/v2/thumbs.php?size=x&url={$encodedUrl}",
                            "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={$encodedUrl}&screenshot=true",
                        ];
                        $screenshotUrl = collect($screenshotServices)->first(fn($url) => !empty($url));
                    @endphp
                    <div style="position: relative; min-height: 400px; background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                        <img 
                            id="screenshot-img"
                            src="{{ $screenshotUrl }}" 
                            alt="Website Screenshot for {{ $analysis->url }}"
                            class="img-fluid"
                            style="width: 100%; height: auto; display: block; min-height: 400px; object-fit: cover;"
                            onerror="handleScreenshotError(this)"
                        >
                        <iframe 
                            id="website-iframe"
                            src="{{ $analysis->url }}" 
                            style="width: 100%; height: 500px; border: none; display: none; border-radius: 8px;"
                            sandbox="allow-same-origin allow-scripts allow-popups allow-forms"
                            loading="lazy"
                        ></iframe>
                        <div id="screenshot-placeholder" style="display: none; padding: 3rem; text-align: center; color: #6c757d; min-height: 400px; flex-direction: column; align-items: center; justify-content: center;" class="d-flex">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🌐</div>
                            <p class="h5 mb-2">Screenshot unavailable</p>
                            <p class="text-muted mb-3 small" style="word-break: break-all;">{{ $analysis->url }}</p>
                            <a href="{{ $analysis->url }}" target="_blank" class="btn btn-primary">
                                Visit Website →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Report Header -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="text-primary mb-3">Latest Security Report for:</h4>
                    <p class="text-muted mb-3" style="word-break: break-all;">
                        <a href="{{ $analysis->url }}" target="_blank" class="text-decoration-none">{{ $analysis->url }}</a>
                    </p>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <strong>Report generated:</strong> {{ $analysis->created_at->format('D, M j, Y g:i A T') }}
                        </div>
                        <div class="mb-2">
                            <span>🇺🇸</span>
                            <strong>Test Server Location:</strong> Seattle, WA, USA
                        </div>
                        <div>
                            <span>🌐</span>
                            <strong>Using:</strong> HTTP Guzzle, Laravel Framework
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Metrics -->
    <div class="row">
        <!-- Left: Security Grade -->
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary mb-3 d-flex align-items-center">
                        Security Grade
                        <svg class="ms-2" width="16" height="16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="cursor: help; color: #6c757d;">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                    </h5>
                    <div class="text-center mb-4">
                        @php
                            $grade = 'F';
                            $gradeClass = 'text-danger';
                            if ($analysis->security_score >= 90) { $grade = 'A'; $gradeClass = 'text-success'; }
                            elseif ($analysis->security_score >= 80) { $grade = 'B'; $gradeClass = 'text-info'; }
                            elseif ($analysis->security_score >= 70) { $grade = 'C'; $gradeClass = 'text-warning'; }
                            elseif ($analysis->security_score >= 60) { $grade = 'D'; $gradeClass = 'text-warning'; }
                        @endphp
                        <div class="{{ $gradeClass }}" style="font-size: 5rem; font-weight: 700; line-height: 1;">
                            {{ $grade }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="small d-flex align-items-center">
                            Security Score
                            <svg class="ms-2" width="14" height="14" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="cursor: help; color: #6c757d;">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="fw-bold 
                            @if($analysis->security_score >= 80) text-success
                            @elseif($analysis->security_score >= 50) text-warning
                            @else text-danger
                            @endif">
                            {{ $analysis->security_score }}%
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="small d-flex align-items-center">
                            Headers Present
                            <svg class="ms-2" width="14" height="14" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="cursor: help; color: #6c757d;">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="fw-bold text-success">
                            @php
                                $totalSecurityHeaders = 10;
                                $missingCount = is_array($analysis->missing_security_features) ? count($analysis->missing_security_features) : 0;
                                $presentCount = $totalSecurityHeaders - $missingCount;
                            @endphp
                            {{ $presentCount }}/{{ $totalSecurityHeaders }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Security Headers Status -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary mb-3 d-flex align-items-center">
                        Security Headers Status
                        <svg class="ms-2" width="16" height="16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="cursor: help; color: #6c757d;">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                    </h5>
                    
                    @php
                        $headers = $analysis->headers ?? [];
                        $securityHeaders = [
                            'Strict-Transport-Security' => 'HSTS',
                            'X-Frame-Options' => 'X-Frame-Options',
                            'X-Content-Type-Options' => 'X-Content-Type',
                            'Content-Security-Policy' => 'CSP',
                            'X-XSS-Protection' => 'XSS Protection',
                        ];
                        
                        $headerStatus = [];
                        foreach ($securityHeaders as $headerName => $displayName) {
                            $found = false;
                            foreach ($headers as $key => $value) {
                                if (strcasecmp($key, $headerName) === 0) {
                                    $found = true;
                                    break;
                                }
                            }
                            $headerStatus[] = [
                                'name' => $displayName,
                                'present' => $found,
                            ];
                        }
                    @endphp

                    @foreach($headerStatus as $status)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="small d-flex align-items-center">
                                {{ $status['name'] }}
                                <svg class="ms-2" width="14" height="14" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="cursor: help; color: #6c757d;">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            <span class="badge {{ $status['present'] ? 'bg-success' : 'bg-danger' }}">
                                {{ $status['present'] ? 'Present' : 'Missing' }}
                            </span>
                        </div>
                    @endforeach

                    @php
                        $missingCount = is_array($analysis->missing_security_features) ? count($analysis->missing_security_features) : 0;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="small d-flex align-items-center">
                            Missing Headers
                            <svg class="ms-2" width="14" height="14" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="cursor: help; color: #6c757d;">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        <span class="badge {{ $missingCount == 0 ? 'bg-success' : ($missingCount <= 3 ? 'bg-warning' : 'bg-danger') }}">
                            {{ $missingCount }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Missing Security Features Section -->
    @if(is_array($analysis->missing_security_features) && count($analysis->missing_security_features) > 0)
        <div class="card mt-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">⚠ Missing Security Features</h5>
            </div>
            <div class="card-body">
                @foreach($analysis->missing_security_features as $missing)
                    <div class="alert alert-warning mb-3" style="border-left: 4px solid #dc3545; background-color: #fff3cd;">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="flex-grow-1">
                                <h6 class="mb-2 text-danger fw-bold">
                                    {{ $missing['name'] ?? $missing['header'] }}
                                </h6>
                                <p class="mb-1 small">
                                    <strong>Description:</strong> {{ $missing['description'] ?? 'N/A' }}
                                </p>
                                <p class="mb-0 small">
                                    <strong>Recommendation:</strong> {{ $missing['recommendation'] ?? 'Add this security header to improve your website security.' }}
                                </p>
                                @if(isset($missing['current_value']))
                                    <p class="mt-2 mb-0 small text-muted">
                                        <strong>Current Value:</strong> <code class="small">{{ $missing['current_value'] }}</code>
                                    </p>
                                @endif
                            </div>
                            <span class="badge bg-danger ms-2 mb-2">Missing</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recommendations -->
    @if(is_array($analysis->recommendations) && count($analysis->recommendations) > 0)
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">💡 Security Recommendations</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($analysis->recommendations as $recommendation)
                        <li class="list-group-item">{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- All Headers Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">HTTP Headers</h5>
        </div>
        <div class="card-body">
            @if(is_array($analysis->headers) && count($analysis->headers) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%;">Header Name</th>
                                <th>Header Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analysis->headers as $name => $value)
                                <tr>
                                    <td><strong>{{ $name }}</strong></td>
                                    <td>
                                        <code class="small" style="background-color: #f8f9fa; padding: 2px 6px; border-radius: 3px; word-break: break-all;">
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No headers found.</p>
            @endif
        </div>
    </div>
</div>

<script>
    let screenshotFallbacks = @json($screenshotServices);
    let currentFallbackIndex = 0;
    let triedIframe = false;
    
    function handleScreenshotError(img) {
        currentFallbackIndex++;
        if (currentFallbackIndex < screenshotFallbacks.length && screenshotFallbacks[currentFallbackIndex]) {
            img.src = screenshotFallbacks[currentFallbackIndex];
        } else if (!triedIframe) {
            // Try iframe as last resort
            triedIframe = true;
            img.style.display = 'none';
            const iframe = document.getElementById('website-iframe');
            if (iframe) {
                iframe.style.display = 'block';
                // Timeout after 10 seconds if iframe doesn't load
                setTimeout(function() {
                    if (iframe.style.display === 'block') {
                        try {
                            const iframeDoc = iframe.contentDocument || iframe.contentWindow?.document;
                            if (!iframeDoc || iframeDoc.body.children.length === 0) {
                                showPlaceholder();
                            }
                        } catch (e) {
                            // Cross-origin, can't check
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
        if (placeholder) placeholder.style.display = 'flex';
    }
</script>
@endsection
