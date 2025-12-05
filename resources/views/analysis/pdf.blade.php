<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Security Header Analysis Report</title>
    <style>
        @page { margin: 40px 50px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0b3558;
            font-size: 12px;
            line-height: 1.5;
        }
        h1, h2, h3 {
            color: #0b3558;
            margin: 0 0 12px;
        }
        h1 {
            font-size: 22px;
            text-align: center;
            text-transform: uppercase;
        }
        h2 {
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        th, td {
            border: 1px solid #2c5f77;
            padding: 8px 10px;
            font-size: 11.5px;
        }
        th {
            background-color: #e3eef5;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: left;
        }
        .section {
            margin-bottom: 28px;
        }
        .divider {
            border: none;
            border-top: 5px solid #0b3558;
            margin: 40px 0;
        }
        .small-muted {
            color: #4a6f85;
            font-size: 11px;
        }
        .raw-headers {
            border: 1px solid #2c5f77;
            padding: 14px;
            min-height: 80px;
            background-color: #f5f9fc;
            white-space: pre-wrap;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 10.5px;
        }
    </style>
</head>
<body>
@php
    $headersRaw = is_array($analysis->headers) ? $analysis->headers : [];
    $normalizedHeaders = [];
    foreach ($headersRaw as $name => $value) {
        $normalizedHeaders[strtolower($name)] = is_array($value) ? implode(', ', $value) : $value;
    }

    $missingItems = [];
    if (is_array($analysis->missing_security_features)) {
        foreach ($analysis->missing_security_features as $item) {
            if (! empty($item['header'])) {
                $missingItems[strtolower($item['header'])] = $item;
            }
        }
    }

    $securityHeaders = [
        [
            'key' => 'https',
            'label' => 'HTTPS Protocol',
            'default_missing' => 'Serve the site over HTTPS to encrypt traffic.',
            'default_present' => 'Secure protocol detected.',
        ],
        [
            'key' => 'strict-transport-security',
            'label' => 'Strict-Transport-Security',
            'default_missing' => 'Enable HSTS to enforce HTTPS connections.',
            'default_present' => 'Header detected with at least one directive.',
        ],
        [
            'key' => 'content-security-policy',
            'label' => 'Content-Security-Policy',
            'default_missing' => 'Define a CSP to mitigate XSS and injection attacks.',
            'default_present' => 'CSP header present.',
        ],
        [
            'key' => 'x-frame-options',
            'label' => 'X-Frame-Options',
            'default_missing' => 'Add X-Frame-Options to prevent clickjacking.',
            'default_present' => 'Defends against clickjacking.',
        ],
        [
            'key' => 'x-content-type-options',
            'label' => 'X-Content-Type-Options',
            'default_missing' => 'Set X-Content-Type-Options to nosniff to prevent MIME sniffing.',
            'default_present' => 'Header present and helps prevent MIME sniffing.',
        ],
        [
            'key' => 'x-xss-protection',
            'label' => 'X-XSS-Protection',
            'default_missing' => 'Add X-XSS-Protection header for legacy browser XSS mitigation.',
            'default_present' => 'Header detected.',
        ],
        [
            'key' => 'referrer-policy',
            'label' => 'Referrer-Policy',
            'default_missing' => 'Specify a referrer policy to control referrer data.',
            'default_present' => 'Referrer policy configured.',
        ],
        [
            'key' => 'permissions-policy',
            'label' => 'Permissions-Policy',
            'default_missing' => 'Configure Permissions-Policy to limit browser feature usage.',
            'default_present' => 'Permissions policy header detected.',
        ],
        [
            'key' => 'x-permitted-cross-domain-policies',
            'label' => 'X-Permitted-Cross-Domain-Policies',
            'default_missing' => 'Set X-Permitted-Cross-Domain-Policies to restrict cross-domain data.',
            'default_present' => 'Header present.',
        ],
        [
            'key' => 'expect-ct',
            'label' => 'Expect-CT',
            'default_missing' => 'Configure Expect-CT to enforce certificate transparency.',
            'default_present' => 'Expect-CT header detected.',
        ],
        [
            'key' => 'clear-site-data',
            'label' => 'Clear-Site-Data',
            'default_missing' => 'Add Clear-Site-Data to support secure data clearing routines.',
            'default_present' => 'Header detected.',
        ],
        [
            'key' => 'set-cookie',
            'label' => 'Cookie Security Attributes',
            'default_missing' => 'Ensure cookies include Secure, HttpOnly, and SameSite attributes.',
            'default_present' => 'Cookie header detected; verify security attributes.',
        ],
    ];

    $rawHeaderLines = [];
    foreach ($headersRaw as $name => $value) {
        $rawHeaderLines[] = $name . ': ' . (is_array($value) ? implode(', ', $value) : $value);
    }
    $rawHeaderOutput = count($rawHeaderLines) ? implode("\n", $rawHeaderLines) : 'No headers captured during this scan.';

    $reportGeneratedAt = $analysis->created_at ? $analysis->created_at->timezone(config('app.timezone')) : $generatedAt;
@endphp

    <h1>Web Security Header Analysis Report</h1>
    <div class="subtitle">
        <strong>Analyzer:</strong> Web Security Header Analyzer (Laravel-Based)
    </div>

    <div class="section">
        <h2>Summary Overview</h2>
        <table>
            <tbody>
                <tr>
                    <th style="width: 32%;">Target URL:</th>
                    <td>{{ $analysis->url }}</td>
                </tr>
                <tr>
                    <th>Scan Date &amp; Time:</th>
                    <td>{{ $reportGeneratedAt->format('F j, Y g:i A') }}</td>
                </tr>
                <tr>
                    <th>Scanned By:</th>
                    <td>{{ optional($analysis->user)->name ?? 'AnaEvaRec Analyzer' }}</td>
                </tr>
                <tr>
                    <th>Overall Security Score:</th>
                    <td>{{ $analysis->security_score }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Detailed Header Evaluation</h2>
        <p class="small-muted">
            This section details the presence and configuration status of critical HTTP Security Headers.
        </p>
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Header Name</th>
                    <th style="width: 15%;">Status</th>
                    <th>Recommendation / Verdict</th>
                </tr>
            </thead>
            <tbody>
                @foreach($securityHeaders as $header)
                    @php
                        if ($header['key'] === 'https') {
                            $isPresent = str_starts_with(strtolower($analysis->url), 'https://');
                            $headerKey = null;
                        } else {
                            $headerKey = $header['key'];
                            $isPresent = array_key_exists($headerKey, $normalizedHeaders) && trim($normalizedHeaders[$headerKey]) !== '';
                        }

                        $missingItem = $headerKey ? ($missingItems[$headerKey] ?? null) : null;
                        $status = $isPresent ? 'Present' : 'Missing';
                        $verdict = $missingItem['recommendation']
                            ?? ($isPresent ? $header['default_present'] : $header['default_missing']);
                    @endphp
                    <tr>
                        <td>{{ $header['label'] }}</td>
                        <td>{{ $status }}</td>
                        <td>{{ $verdict }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr class="divider">

    <div class="section">
        <h2>Raw HTTP Response Headers</h2>
        <p class="small-muted">
            The raw response headers returned by the target server during the scan.
        </p>
        <div class="raw-headers">
            {{ $rawHeaderOutput }}
        </div>
    </div>

    <div class="section">
        <h2>Next Steps and Conclusion</h2>
        <p>
            This report is a point-in-time evaluation of HTTP response headers and does not constitute a full penetration test or vulnerability scan.
            It is intended to support documentation, tracking, and awareness efforts.
        </p>
        <p>
            <strong>Important Note:</strong> The goal is to consistently achieve an "A" or "A+" score by implementing the recommended headers,
            such as Content-Security-Policy (CSP) and HTTP Strict Transport Security (HSTS), to mitigate common attacks like XSS and Clickjacking.
        </p>
    </div>
</body>
</html>

