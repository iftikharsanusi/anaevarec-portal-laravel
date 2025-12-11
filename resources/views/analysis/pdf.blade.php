<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Security Header Analysis Report</title>
    <style>
        @page { margin: 40px 50px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #000000;
            font-size: 12px;
            line-height: 1.5;
        }
        h1, h2, h3 {
            color: #000000;
            margin: 0 0 12px;
        }
        h1 {
            font-size: 22px;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
        }
        h2 {
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: bold;
            color: #000000;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 24px;
            color: #000000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px 10px;
            font-size: 11.5px;
        }
        th {
            background-color: #f5f5f5;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: left;
            font-weight: bold;
            color: #000000;
        }
        td {
            color: #000000;
        }
        .section {
            margin-bottom: 28px;
        }
        .divider {
            border: none;
            border-top: 2px solid #000000;
            margin: 40px 0;
        }
        .small-muted {
            color: #333333;
            font-size: 11px;
        }
        .raw-headers {
            border: 1px solid #000000;
            padding: 14px;
            min-height: 80px;
            background-color: #f9f9f9;
            white-space: pre-wrap;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 10.5px;
            color: #000000;
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

    // Match the same headers as in analysis view
    $securityHeaders = [
        [
            'key' => 'https',
            'label' => 'HTTPS Protocol',
            'default_missing' => 'Switching to HTTPS encrypts all communication between your website and visitors, protecting sensitive data like passwords and personal information from being intercepted by attackers.',
            'default_present' => 'Secure protocol detected.',
        ],
        [
            'key' => 'content-security-policy',
            'label' => 'Content-Security-Policy',
            'default_missing' => 'Implementing a Content Security Policy (CSP) helps protect your site from cross-site scripting (XSS) and malicious code injection by controlling which content is allowed to run.',
            'default_present' => 'CSP header present.',
        ],
        [
            'key' => 'strict-transport-security',
            'label' => 'Strict-Transport-Security',
            'default_missing' => 'Enabling HTTP Strict Transport Security (HSTS) ensures that browsers always connect to your site using HTTPS, protecting users from man-in-the-middle attacks and preventing downgrade attacks.',
            'default_present' => 'Header detected with at least one directive.',
        ],
        [
            'key' => 'x-frame-options',
            'label' => 'X-Frame-Options',
            'default_missing' => 'Adding the X-Frame-Options header prevents clickjacking attacks by controlling whether your site can be embedded in frames on other websites, keeping your users safe from deceptive interactions.',
            'default_present' => 'Defends against clickjacking.',
        ],
        [
            'key' => 'x-content-type-options',
            'label' => 'X-Content-Type-Options',
            'default_missing' => 'Setting X-Content-Type-Options to \'nosniff\' prevents browsers from guessing content types, which helps protect against MIME-type confusion attacks and keeps your content secure.',
            'default_present' => 'Header present and helps prevent MIME sniffing.',
        ],
        [
            'key' => 'permissions-policy',
            'label' => 'Permissions-Policy',
            'default_missing' => 'Configuring Permissions Policy (formerly Feature Policy) gives you control over which browser features and APIs can be used on your site, helping to prevent unauthorized access to sensitive device capabilities.',
            'default_present' => 'Permissions policy header detected.',
        ],
        [
            'key' => 'referrer-policy',
            'label' => 'Referrer-Policy',
            'default_missing' => 'Setting a Referrer Policy helps you control how much referrer information is shared when users navigate away from your site, protecting user privacy and reducing information leakage.',
            'default_present' => 'Referrer policy configured.',
        ],
        [
            'key' => 'x-xss-protection',
            'label' => 'X-XSS-Protection',
            'default_missing' => 'Enabling X-XSS-Protection provides an additional layer of protection against cross-site scripting attacks by activating the browser\'s built-in XSS filter.',
            'default_present' => 'Header detected.',
        ],
        [
            'key' => 'x-permitted-cross-domain-policies',
            'label' => 'X-Permitted-Cross-Domain-Policies',
            'default_missing' => 'Configuring X-Permitted-Cross-Domain-Policies helps control how cross-domain policy files are handled, providing better security for cross-origin resource sharing.',
            'default_present' => 'Header present.',
        ],
        [
            'key' => 'cross-origin-resource-policy',
            'label' => 'Cross-Origin-Resource-Policy',
            'default_missing' => 'Implementing Cross-Origin-Resource-Policy helps control how the browser handles cross-origin resource requests, adding an extra layer of protection against certain types of attacks.',
            'default_present' => 'Header present.',
        ],
        [
            'key' => 'cross-origin-embedder-policy',
            'label' => 'Cross-Origin-Embedder-Policy',
            'default_missing' => 'Setting Cross-Origin-Embedder-Policy prevents your site from loading cross-origin resources that don\'t explicitly grant permission, helping to isolate your site\'s resources for better security.',
            'default_present' => 'Header present.',
        ],
        [
            'key' => 'cross-origin-opener-policy',
            'label' => 'Cross-Origin-Opener-Policy',
            'default_missing' => 'Configuring Cross-Origin-Opener-Policy isolates your browsing context by preventing other origins from accessing window objects, which helps protect against certain cross-origin attacks.',
            'default_present' => 'Header present.',
        ],
        [
            'key' => 'expect-ct',
            'label' => 'Expect-CT',
            'default_missing' => 'Enabling Expect-CT helps monitor Certificate Transparency compliance, allowing you to detect misissued certificates and improve the security of your HTTPS connections.',
            'default_present' => 'Expect-CT header detected.',
        ],
        [
            'key' => 'report-to',
            'label' => 'Report-To',
            'default_missing' => 'Setting up Report-To allows you to receive violation reports from browsers, helping you monitor and respond to security issues on your site in real-time.',
            'default_present' => 'Header detected.',
        ],
        [
            'key' => 'clear-site-data',
            'label' => 'Clear-Site-Data',
            'default_missing' => 'Configuring Clear-Site-Data gives you control over when to clear browsing data, which can be useful for logout functionality and privacy protection.',
            'default_present' => 'Header detected.',
        ],
    ];

    $rawHeaderLines = [];
    foreach ($headersRaw as $name => $value) {
        $rawHeaderLines[] = $name . ': ' . (is_array($value) ? implode(', ', $value) : $value);
    }
    $rawHeaderOutput = count($rawHeaderLines) ? implode("\n", $rawHeaderLines) : 'No headers captured during this scan.';

    $reportGeneratedAt = $analysis->created_at ? $analysis->created_at->timezone(config('app.timezone')) : $generatedAt;

    // Calculate grade (same as analysis view)
    $grade = 'F';
    if ($analysis->security_score >= 85) {
        $grade = 'A';
    } elseif ($analysis->security_score >= 70) {
        $grade = 'B';
    } elseif ($analysis->security_score >= 55) {
        $grade = 'C';
    } elseif ($analysis->security_score >= 40) {
        $grade = 'D';
    } elseif ($analysis->security_score >= 25) {
        $grade = 'E';
    }

    // Get present headers count
    $presentCount = 0;
    foreach ($securityHeaders as $header) {
        if ($header['key'] === 'https') {
            $isPresent = str_starts_with(strtolower($analysis->url), 'https://');
        } else {
            $isPresent = array_key_exists($header['key'], $normalizedHeaders) && trim($normalizedHeaders[$header['key']]) !== '';
        }
        if ($isPresent) {
            $presentCount++;
        }
    }

    // Get missing headers count
    $missingCount = is_array($analysis->missing_security_features) ? count($analysis->missing_security_features) : 0;
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
                        // Use friendly recommendation from analysis if available, otherwise use default
                        $verdict = $missingItem['recommendation'] ?? ($isPresent ? $header['default_present'] : $header['default_missing']);
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

    @if(is_array($analysis->recommendations) && count($analysis->recommendations) > 0)
    <div class="section">
        <h2>Security Recommendations</h2>
        <p class="small-muted">
            Actionable recommendations to improve your website's security posture.
        </p>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th>Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analysis->recommendations as $index => $recommendation)
                <tr>
                    <td style="text-align: center; font-weight: bold; background-color: #f5f5f5;">{{ $index + 1 }}</td>
                    <td style="padding: 12px 10px;">{{ $recommendation }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr class="divider">
    @endif

    <div class="section">
        <h2>Overall Website Security</h2>
        <table>
            <tbody>
                <tr>
                    <th style="width: 32%;">Security Grade:</th>
                    <td style="font-size: 18px; font-weight: bold;">{{ $grade }}</td>
                </tr>
                <tr>
                    <th>Security Score:</th>
                    <td style="font-size: 16px; font-weight: bold;">{{ $analysis->security_score }}%</td>
                </tr>
                <tr>
                    <th>Headers Present:</th>
                    <td>{{ $presentCount }}</td>
                </tr>
                <tr>
                    <th>Headers Missing:</th>
                    <td>{{ $missingCount }}</td>
                </tr>
                <tr>
                    <th>Security Assessment:</th>
                    <td>
                        @if($analysis->security_score >= 85)
                            This website demonstrates <strong>excellent security practices</strong> with a comprehensive set of security headers in place. The implementation shows strong protection against common web vulnerabilities including clickjacking, XSS attacks, and MIME-type sniffing. The security configuration indicates a well-maintained and security-conscious approach to web application development.
                        @elseif($analysis->security_score >= 70)
                            This website shows <strong>good security posture</strong> with most critical security headers implemented. While the current configuration provides solid protection against many common threats, there are opportunities to enhance security further by implementing additional recommended headers. The existing security measures demonstrate a proactive approach to web security.
                        @elseif($analysis->security_score >= 55)
                            This website has a <strong>fair security posture</strong> with some essential security headers in place. However, several important security headers are missing, which could leave the site vulnerable to certain types of attacks. Implementing the missing headers would significantly improve the overall security and provide better protection against modern web threats.
                        @elseif($analysis->security_score >= 40)
                            This website has a <strong>weak security posture</strong> with limited security headers implemented. Multiple critical security headers are missing, which increases the risk of various web-based attacks including clickjacking, XSS, and data injection. Immediate action is recommended to implement the missing security headers to reduce vulnerability exposure.
                        @elseif($analysis->security_score >= 25)
                            This website has a <strong>poor security posture</strong> with very few security headers implemented. The site is vulnerable to a wide range of attacks including clickjacking, XSS, MIME-type sniffing, and more. The lack of proper security headers exposes the website and its users to significant risks. Urgent implementation of security headers is strongly recommended.
                        @else
                            This website has <strong>failed security standards</strong> with no or minimal security headers implemented. The site is highly vulnerable to attacks including clickjacking, XSS, MIME-type sniffing, and more. The complete absence of proper security headers exposes the website and its users to critical risks. Immediate and comprehensive implementation of security headers is essential to protect against common web vulnerabilities.
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <hr class="divider">

    <div class="section">
        <h2>RAW HTTP RESPONSE HEADERS</h2>
        <p class="small-muted">
            The raw response headers returned by the target server during the scan.
        </p>
        <div class="raw-headers">
            {{ $rawHeaderOutput }}
        </div>
    </div>

    <hr class="divider">

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

