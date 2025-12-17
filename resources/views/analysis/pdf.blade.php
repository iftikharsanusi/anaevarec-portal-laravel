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
            text-align: left;
        }
        .summary-overview-title {
            text-align: center;
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
            font-family: DejaVu Sans, sans-serif;
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
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-all;
        }
        .page-break-before {
            page-break-before: always;
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
        $isPresent = array_key_exists($header['key'], $normalizedHeaders) && trim($normalizedHeaders[$header['key']]) !== '';
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

    <div class="section" style="text-align: center;">
        <h2 class="summary-overview-title">Summary Overview</h2>
        <div style="text-align: center; margin: 20px 0;">
            <table style="width: 70%; margin: 0 auto;">
                <tbody>
                    <tr>
                        <th style="width: 32%; text-align: center; padding: 4px 8px;">Target URL:</th>
                        <td style="text-align: center; padding: 4px 8px;">{{ $analysis->url }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: center; padding: 4px 8px;">Scan Date &amp; Time:</th>
                        <td style="text-align: center; padding: 4px 8px;">{{ $reportGeneratedAt->format('F j, Y g:i A') }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: center; padding: 4px 8px;">Scanned By:</th>
                        <td style="text-align: center; padding: 4px 8px;">{{ optional($analysis->user)->name ?? 'AnaEvaRec Analyzer' }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: center; padding: 4px 8px;">Overall Security Score:</th>
                        <td style="text-align: center; padding: 4px 8px;">{{ $analysis->security_score }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section page-break-before">
        <h2>Detailed Header Evaluation</h2>
        <p class="small-muted">
            This section details the presence and configuration status of critical HTTP Security Headers.
        </p>
        
        @php
            $presentHeaders = [];
            $missingHeaders = [];
            
            foreach ($securityHeaders as $header) {
                $headerKey = $header['key'];
                $isPresent = array_key_exists($headerKey, $normalizedHeaders) && trim($normalizedHeaders[$headerKey]) !== '';

                $missingItem = $headerKey ? ($missingItems[$headerKey] ?? null) : null;
                $verdict = $missingItem['recommendation'] ?? ($isPresent ? $header['default_present'] : $header['default_missing']);
                
                if ($isPresent) {
                    $presentHeaders[] = [
                        'label' => $header['label'],
                        'verdict' => $verdict
                    ];
                } else {
                    $missingHeaders[] = [
                        'label' => $header['label'],
                        'verdict' => $verdict
                    ];
                }
            }
        @endphp
        
        <h3 style="font-size: 13px; margin-top: 20px; margin-bottom: 10px;">Present Security Headers ({{ count($presentHeaders) }})</h3>
        @if(count($presentHeaders) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Header Name</th>
                    <th style="width: 15%;">Status</th>
                    <th>Recommendation / Verdict</th>
                </tr>
            </thead>
            <tbody>
                @foreach($presentHeaders as $header)
                <tr>
                    <td>{{ $header['label'] }}</td>
                    <td>Present</td>
                    <td>{{ $header['verdict'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="padding: 12px; background-color: #f9f9f9; border: 1px solid #000000;">No security headers detected as present.</p>
        @endif
        
        <h3 style="font-size: 13px; margin-top: 20px; margin-bottom: 10px;">Missing Security Headers ({{ count($missingHeaders) }})</h3>
        @if(count($missingHeaders) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Header Name</th>
                    <th style="width: 15%;">Status</th>
                    <th>Recommendation / Verdict</th>
                </tr>
            </thead>
            <tbody>
                @foreach($missingHeaders as $header)
                <tr>
                    <td>{{ $header['label'] }}</td>
                    <td>Missing</td>
                    <td>{{ $header['verdict'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="padding: 12px; background-color: #f9f9f9; border: 1px solid #000000;">All security headers are present.</p>
        @endif
    </div>

    @if(is_array($analysis->recommendations) && count($analysis->recommendations) > 0)
    <div class="section page-break-before">
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
    @endif

    <div class="section page-break-before">
        <h2>Overall Website Security</h2>
        <table>
            <tbody>
                <tr>
                    <th style="width: 32%; font-family: DejaVu Sans, sans-serif;">Security Grade:</th>
                    <td style="font-size: 18px; font-weight: bold; font-family: DejaVu Sans, sans-serif;">{{ $grade }}</td>
                </tr>
                <tr>
                    <th style="font-family: DejaVu Sans, sans-serif;">Security Score:</th>
                    <td style="font-size: 16px; font-weight: bold; font-family: DejaVu Sans, sans-serif;">{{ $analysis->security_score }}%</td>
                </tr>
                <tr>
                    <th style="font-family: DejaVu Sans, sans-serif;">Headers Present:</th>
                    <td style="font-size: 16px; font-weight: bold; font-family: DejaVu Sans, sans-serif;">{{ $presentCount }}</td>
                </tr>
                <tr>
                    <th style="font-family: DejaVu Sans, sans-serif;">Headers Missing:</th>
                    <td style="font-size: 16px; font-weight: bold; font-family: DejaVu Sans, sans-serif;">{{ $missingCount }}</td>
                </tr>
                <tr>
                    <th style="font-family: DejaVu Sans, sans-serif;">Security Assessment:</th>
                    <td style="text-align: justify; font-family: DejaVu Sans, sans-serif;">
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

    <div class="section page-break-before">
        <h2>Additional Informations - Standard HTTP Headers</h2>
        <p class="small-muted">
            All HTTP response headers returned by the target server, categorized by type and security implications.
        </p>
        @if(is_array($analysis->headers) && count($analysis->headers) > 0)
            @php
                $infoHeaders = ['server', 'x-powered-by', 'date', 'connection', 'transfer-encoding', 'content-length'];
                $safeHeaders = ['content-type', 'cache-control', 'accept-ranges', 'etag', 'last-modified', 'expires', 'vary'];
                $warningHeaders = ['x-aspnet-version', 'x-aspnetmvc-version'];
                
                // Security header keys for exclusion
                $securityHeaderKeys = array_map(function($h) {
                    return $h['key'] === 'https' ? 'https' : $h['key'];
                }, $securityHeaders);
                
                // Header descriptions mapping
                $headerDescriptions = [
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
                    'cf-ray' => 'Cloudflare Ray ID for request tracking.',
                    'cf-cache-status' => 'Cloudflare cache status (HIT, MISS, etc.).',
                    'cf-request-id' => 'Cloudflare request identifier.',
                    'nel' => 'Network Error Logging configuration.',
                    'x-powered-by' => 'Reveals the server technology stack (security risk if exposed).',
                    'alt-svc' => 'Alternative service advertisement for HTTP/2 or HTTP/3.',
                    'location' => 'Used in redirects to specify the target URL.',
                    'set-cookie' => 'Sends cookies from the server to the user agent.',
                ];
            @endphp
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Header</th>
                        <th style="width: 35%;">Value</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analysis->headers as $name => $value)
                        @php
                            $headerValue = is_array($value) ? implode(', ', $value) : $value;
                            $headerLower = strtolower($name);
                            
                            // Skip security headers (already covered in other sections)
                            if (in_array($headerLower, $securityHeaderKeys)) {
                                continue;
                            }
                            
                            // Get description
                            $description = $headerDescriptions[$headerLower] ?? 'Standard HTTP header with no specific security implications.';
                            
                            // Determine status based on header type
                            if (in_array($headerLower, $safeHeaders)) {
                                $status = 'Safe';
                            } elseif (in_array($headerLower, $infoHeaders)) {
                                $status = 'Info';
                            } elseif (in_array($headerLower, $warningHeaders)) {
                                $status = 'Warning';
                            } else {
                                $status = 'Safe';
                            }
                            
                            // Truncate long values for PDF
                            if (strlen($headerValue) > 80) {
                                $headerValue = substr($headerValue, 0, 77) . '...';
                            }
                        @endphp
                        <tr>
                            <td><strong>{{ $name }}</strong></td>
                            <td style="font-size: 10.5px; word-break: break-word;">{{ $headerValue }}</td>
                            <td style="font-size: 10.5px;">{{ $description }}</td>
                            <td style="font-size: 10.5px; text-align: center;">{{ $status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="padding: 12px; background-color: #f9f9f9; border: 1px solid #000000;">
                No standard HTTP headers were captured during this scan.
            </p>
        @endif
    </div>

    <div class="section page-break-before">
        <h2>RAW HTTP RESPONSE HEADERS</h2>
        <p class="small-muted">
            The raw response headers returned by the target server during the scan.
        </p>
        <div class="raw-headers">
            {{ $rawHeaderOutput }}
        </div>
        
        <div style="margin-top: 30px;">
            <h3 style="font-size: 13px; text-transform: uppercase; font-weight: bold; margin-bottom: 12px;">Next Steps and Conclusion</h3>
            <p style="font-family: DejaVu Sans, sans-serif; text-align: justify;">
                This report provides a point-in-time evaluation of HTTP response headers and serves as a security assessment tool. 
                It is intended to support documentation, tracking, and security awareness efforts. This analysis does not constitute 
                a comprehensive penetration test or complete vulnerability assessment.
            </p>
            <p style="font-family: DejaVu Sans, sans-serif; text-align: justify;">
                <strong>Recommendation:</strong> To achieve optimal security posture, aim for a Grade "A" (85% or higher) by implementing 
                all recommended security headers. Critical headers such as Content-Security-Policy (CSP) and HTTP Strict Transport 
                Security (HSTS) are essential for protecting against common web vulnerabilities including cross-site scripting (XSS), 
                clickjacking, and man-in-the-middle attacks. Regular security header audits are recommended to maintain and improve 
                your website's security posture over time.
            </p>
        </div>
    </div>
</body>
</html>

