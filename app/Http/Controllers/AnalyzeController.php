<?php

namespace App\Http\Controllers;

use App\Models\HeaderAnalysis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalyzeController extends Controller
{
    /**
     * Analyze headers for a given URL
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        $url = trim($request->input('url'));
        
        try {
            // Remove any leading/trailing whitespace and ensure URL has protocol
            if (!preg_match('/^https?:\/\//', $url)) {
                $url = 'http://' . $url;
            }
            
            // Validate the final URL format
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return redirect()->route('home')
                    ->with('error', 'Please enter a valid URL or domain name.');
            }

            // Fetch headers - try HEAD first, fallback to GET if needed
            $response = Http::timeout(10)->head($url);
            
            // Some servers don't support HEAD, fallback to GET
            if (!$response->successful()) {
                $response = Http::timeout(10)->get($url);
                
                if (!$response->successful()) {
                    return redirect()->route('home')
                        ->with('error', 'Failed to fetch headers from the URL. Please check if the URL is accessible.');
                }
            }

            $headers = $response->headers();
            $headersArray = [];
            
            // Convert headers to array format
            foreach ($headers as $key => $value) {
                $headersArray[$key] = is_array($value) ? $value[0] : $value;
            }

            // Analyze security features
            $analysis = $this->analyzeSecurityHeaders($headersArray, $url);
            
            // Generate screenshot URL using multiple services
            $screenshotUrl = $this->generateScreenshotUrl($url);
            
            // Store in database
            $headerAnalysis = HeaderAnalysis::create([
                'user_id' => auth()->id(),
                'url' => $url,
                'screenshot_url' => $screenshotUrl,
                'headers' => $headersArray,
                'recommendations' => $analysis['recommendations'],
                'missing_security_features' => $analysis['missing'],
                'security_score' => $analysis['score'],
            ]);

            return redirect()->route('home')
                ->with('success', 'Headers analyzed successfully!')
                ->with('analysis_id', $headerAnalysis->id);

        } catch (ConnectionException $e) {
            // Network connection errors (DNS, timeout, unreachable)
            Log::error('Header analysis connection error: ' . $e->getMessage(), [
                'url' => $url,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('home')
                ->with('error', 'Unable to connect to the website. Please check if the URL is correct and the website is accessible.');
        } catch (RequestException $e) {
            // HTTP request errors
            Log::error('Header analysis request error: ' . $e->getMessage(), [
                'url' => $url,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('home')
                ->with('error', 'Unable to fetch headers from the website. The website may be temporarily unavailable or blocking requests.');
        } catch (\Exception $e) {
            // Generic errors - hide technical details
            Log::error('Header analysis error: ' . $e->getMessage(), [
                'url' => $url,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check for common error patterns and provide user-friendly messages
            $errorMessage = $e->getMessage();
            if (stripos($errorMessage, 'resolve host') !== false || 
                stripos($errorMessage, 'could not resolve') !== false ||
                stripos($errorMessage, 'name or service not known') !== false) {
                return redirect()->route('home')
                    ->with('error', 'The website URL could not be found. Please verify the URL is correct and try again.');
            } elseif (stripos($errorMessage, 'timeout') !== false || 
                      stripos($errorMessage, 'timed out') !== false) {
                return redirect()->route('home')
                    ->with('error', 'The request timed out. The website may be slow or unavailable. Please try again later.');
            } elseif (stripos($errorMessage, 'connection') !== false || 
                      stripos($errorMessage, 'refused') !== false) {
                return redirect()->route('home')
                    ->with('error', 'Unable to connect to the website. Please check if the URL is correct and the website is accessible.');
            } else {
                // Generic error message for any other errors
                return redirect()->route('home')
                    ->with('error', 'An error occurred while analyzing the website. Please verify the URL is correct and try again.');
            }
        }
    }

    /**
     * Analyze security headers and return recommendations
     */
    private function analyzeSecurityHeaders(array $headers, string $url): array
    {
        $recommendations = [];
        $missing = [];
        $score = 0;
        $maxScore = 0;

        // Common security headers to check
        // Scoring prioritizes standard/essential headers so websites with basic security get reasonable scores
        $securityHeaders = [
            // ESSENTIAL HEADERS (82 points) - Standard implementation
            'Content-Security-Policy' => [
                'name' => 'Content Security Policy (CSP)',
                'description' => 'Prevents XSS and injection attacks',
                'points' => 25, // Increased - Most critical header
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'Strict-Transport-Security' => [
                'name' => 'HSTS (HTTP Strict Transport Security)',
                'description' => 'Forces browsers to use HTTPS connections',
                'points' => 18, // Increased - Very important
                'check' => function($value) {
                    return stripos($value, 'max-age') !== false;
                }
            ],
            'X-Frame-Options' => [
                'name' => 'X-Frame-Options',
                'description' => 'Prevents clickjacking attacks',
                'points' => 12, // Increased - Standard security
                'check' => function($value) {
                    $value = strtolower($value);
                    return in_array($value, ['deny', 'sameorigin']) || stripos($value, 'allow-from') !== false;
                }
            ],
            'X-Content-Type-Options' => [
                'name' => 'X-Content-Type-Options',
                'description' => 'Prevents MIME type sniffing',
                'points' => 12, // Increased - Standard security
                'check' => function($value) {
                    return strtolower($value) === 'nosniff';
                }
            ],
            'Permissions-Policy' => [
                'name' => 'Permissions Policy',
                'description' => 'Controls browser features and APIs',
                'points' => 10, // Standard - Important
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'Referrer-Policy' => [
                'name' => 'Referrer Policy',
                'description' => 'Controls referrer information',
                'points' => 5, // Increased - Good practice
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            // IMPORTANT HEADERS (7 points) - Recommended but not critical
            'X-XSS-Protection' => [
                'name' => 'X-XSS-Protection',
                'description' => 'Enables XSS filtering in browsers',
                'points' => 4, // Standard - Legacy but still useful
                'check' => function($value) {
                    $value = strtolower(trim($value));
                    // Valid values: "1", "1; mode=block", "0" (disabled)
                    // We want "1" or "1; mode=block" for security
                    return $value === '1' || $value === '1; mode=block' || $value === '1;mode=block';
                }
            ],
            'X-Permitted-Cross-Domain-Policies' => [
                'name' => 'X-Permitted-Cross-Domain-Policies',
                'description' => 'Controls cross-domain policy files',
                'points' => 3, // Reduced - Less critical
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            // ADVANCED HEADERS (11 points) - Modern/optional headers
            'Cross-Origin-Resource-Policy' => [
                'name' => 'Cross-Origin-Resource-Policy',
                'description' => 'Controls how the browser handles cross-origin resource requests',
                'points' => 2, // Reduced - Advanced feature
                'check' => function($value) {
                    $value = strtolower(trim($value));
                    // Valid values: "same-origin", "same-site", "cross-origin"
                    return in_array($value, ['same-origin', 'same-site', 'cross-origin']);
                }
            ],
            'Cross-Origin-Embedder-Policy' => [
                'name' => 'Cross-Origin-Embedder-Policy',
                'description' => 'Prevents loading cross-origin resources without explicit permission',
                'points' => 1, // Reduced - Advanced feature
                'check' => function($value) {
                    $value = strtolower(trim($value));
                    // Valid values: "unsafe-none", "require-corp", "credentialless"
                    return in_array($value, ['unsafe-none', 'require-corp', 'credentialless']);
                }
            ],
            'Cross-Origin-Opener-Policy' => [
                'name' => 'Cross-Origin-Opener-Policy',
                'description' => 'Isolates browsing context by preventing cross-origin window access',
                'points' => 1, // Reduced - Advanced feature
                'check' => function($value) {
                    $value = strtolower(trim($value));
                    // Valid values: "unsafe-none", "same-origin-allow-popups", "same-origin"
                    return in_array($value, ['unsafe-none', 'same-origin-allow-popups', 'same-origin']);
                }
            ],
            'Expect-CT' => [
                'name' => 'Expect-CT',
                'description' => 'Certificate Transparency monitoring',
                'points' => 1, // Reduced - Optional
                'check' => function($value) {
                    return stripos($value, 'enforce') !== false || stripos($value, 'report-uri') !== false;
                }
            ],
            'Report-To' => [
                'name' => 'Report-To',
                'description' => 'Specifies endpoints for reporting API violations',
                'points' => 1, // Standard - Optional
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'Clear-Site-Data' => [
                'name' => 'Clear-Site-Data',
                'description' => 'Clears browsing data',
                'points' => 0, // Informational only
                'check' => function($value) {
                    return !empty($value);
                }
            ],
        ];

        // Check each security header
        foreach ($securityHeaders as $headerName => $config) {
            $maxScore += $config['points'];
            $headerKey = $this->findHeaderKey($headers, $headerName);
            
            if ($headerKey === null) {
                $missing[] = [
                    'header' => $headerName,
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'recommendation' => "Add the {$headerName} header to improve security.",
                ];
                $recommendations[] = "Missing: {$config['name']}. {$config['description']}";
            } else {
                $headerValue = $headers[$headerKey];
                if ($config['check']($headerValue)) {
                    $score += $config['points'];
                } else {
                    $missing[] = [
                        'header' => $headerName,
                        'name' => $config['name'],
                        'description' => $config['description'],
                        'recommendation' => "Improve the {$headerName} header configuration.",
                        'current_value' => $headerValue,
                    ];
                    $recommendations[] = "Improve: {$config['name']} - Current value may not be optimal.";
                }
            }
        }

        // Additional recommendations
        if (isset($headers['Server'])) {
            $recommendations[] = "Consider hiding or modifying the Server header to avoid revealing server information.";
        }

        // Check for HTTPS (now included in scoring)
        $httpsPoints = 5;
        $maxScore += $httpsPoints;
        if (stripos($url, 'https://') !== false) {
            $score += $httpsPoints;
        } else {
            $recommendations[] = "Consider using HTTPS instead of HTTP for secure communication.";
            $missing[] = [
                'header' => 'HTTPS',
                'name' => 'HTTPS Protocol',
                'description' => 'Use encrypted connection for secure data transmission',
                'recommendation' => 'Switch to HTTPS protocol.',
            ];
        }

        // Calculate final score (percentage)
        $finalScore = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;

        return [
            'recommendations' => $recommendations,
            'missing' => $missing,
            'score' => $finalScore,
        ];
    }

    /**
     * Find header key (case-insensitive)
     */
    private function findHeaderKey(array $headers, string $headerName): ?string
    {
        foreach ($headers as $key => $value) {
            if (strcasecmp($key, $headerName) === 0) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Generate screenshot URL using multiple services with fallbacks
     * Tries ScreenshotOne first, then falls back to free services
     */
    private function generateScreenshotUrl(string $url): ?string
    {
        // Ensure URL is properly formatted
        $cleanUrl = trim($url);
        if (!preg_match('/^https?:\/\//', $cleanUrl)) {
            $cleanUrl = 'http://' . $cleanUrl;
        }
        
        // Validate URL format
        if (!filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
            Log::warning('Invalid URL provided for screenshot', ['url' => $url, 'clean_url' => $cleanUrl]);
            return null;
        }
        
        $encodedUrl = urlencode($cleanUrl);
        
        // Option to use only free services (set SCREENSHOTONE_DISABLE=true in .env)
        // For now, skip ScreenshotOne since both keys hit limits - use free services directly
        $useFreeServicesOnly = config('services.screenshotone.disable', true); // Default to true to use free services
        
        // Try ScreenshotOne first (if configured and not disabled)
        $accessKey = config('services.screenshotone.access_key');
        $secretKey = config('services.screenshotone.secret_key');
        
        if (!empty($accessKey) && !$useFreeServicesOnly) {
            try {
                // ScreenshotOne API URL - using minimal required parameters
                $apiUrl = 'https://api.screenshotone.com/take';
                $params = [
                    'url' => $cleanUrl,
                    'access_key' => $accessKey,
                    'viewport_width' => 1920,
                    'viewport_height' => 1080,
                    'format' => 'png',
                    'image_quality' => 80,
                ];
                
                // Only add signature if secret key is provided
                if (!empty($secretKey)) {
                    ksort($params);
                    $queryString = http_build_query($params);
                    $signature = hash_hmac('sha256', $queryString, $secretKey);
                    $params['signature'] = $signature;
                }
                
                $screenshotUrl = $apiUrl . '?' . http_build_query($params);
                
                // Return ScreenshotOne URL
                // Note: If ScreenshotOne limit is reached, the JavaScript will detect the error
                // and automatically fall back to alternative services
                Log::info('Generated ScreenshotOne URL', [
                    'original_url' => $cleanUrl,
                    'service' => 'ScreenshotOne',
                    'note' => 'JavaScript will fallback if limit reached'
                ]);
                return $screenshotUrl;
            } catch (\Exception $e) {
                Log::warning('ScreenshotOne URL generation failed, trying fallback', [
                    'error' => $e->getMessage(),
                    'url' => $cleanUrl
                ]);
            }
        } else {
            Log::info('ScreenshotOne not configured, using fallback service', ['url' => $cleanUrl]);
        }
        
        // Fallback services that work reliably
        // Using multiple services that don't require API keys or have better free tiers
        
        // Free screenshot services - no API keys required
        // Thum.io is working and reliable (tested and confirmed)
        
        // Primary: Thum.io path-based - Free, working, reliable
        $thumIoPathUrl = "https://image.thum.io/get/width/1200/crop/675/noanimate/{$cleanUrl}";
        
        // Alternative: Thum.io query-based (backup)
        $thumIoQueryUrl = "https://image.thum.io/get/width/1200/crop/675/noanimate?url={$encodedUrl}";
        
        // Alternative sizes for Thum.io
        $thumIoFullPage = "https://image.thum.io/get/fullpage/width/1200/height/675/noanimate/{$cleanUrl}";
        
        // Use Thum.io as primary (free, working, actually captures the website)
        $finalUrl = $thumIoPathUrl;
        
        $screenshotOptions = [
            $thumIoPathUrl,
            $thumIoQueryUrl,
            $thumIoFullPage,
        ];
        
        Log::info('Using free screenshot service (Thum.io)', [
            'original_url' => $url,
            'clean_url' => $cleanUrl,
            'encoded_url' => $encodedUrl,
            'service' => 'Thum.io',
            'screenshot_url' => $finalUrl,
            'all_options' => $screenshotOptions,
            'note' => 'Free service - tested and working'
        ]);
        
        return $finalUrl;
    }

    /**
     * Show details of a specific analysis
     */
    public function show(Request $request, $id)
    {
        $analysis = HeaderAnalysis::with('user')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($request->get('download') === 'pdf') {
            $pdf = Pdf::loadView('analysis.pdf', [
                'analysis' => $analysis,
                'generatedAt' => now()->timezone(config('app.timezone')),
            ])->setPaper('a4');

            $host = parse_url($analysis->url, PHP_URL_HOST) ?? $analysis->url;
            $fileName = 'security-report-' . Str::slug($host ?: 'website') . '-' . $analysis->created_at->format('Ymd-His') . '.pdf';

            return $pdf->download($fileName);
        }

        return view('analysis.show', compact('analysis'));
    }

    /**
     * Test screenshot service - useful for debugging
     */
    public function testScreenshot(Request $request)
    {
        $testUrl = $request->input('url', 'https://example.com');
        $screenshotUrl = $this->generateScreenshotUrl($testUrl);
        
        // Test if the URL is accessible
        $isAccessible = false;
        $errorMessage = null;
        
        if ($screenshotUrl) {
            try {
                $response = Http::timeout(10)->head($screenshotUrl);
                $isAccessible = $response->successful();
                
                if (!$isAccessible) {
                    // Try GET request
                    $response = Http::timeout(10)->get($screenshotUrl);
                    $isAccessible = $response->successful();
                    $errorMessage = $response->status() . ': ' . ($response->body() ?: 'No response body');
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }
        
        return response()->json([
            'test_url' => $testUrl,
            'screenshot_url' => $screenshotUrl,
            'service_used' => $this->getServiceName($screenshotUrl),
            'is_accessible' => $isAccessible,
            'error' => $errorMessage,
            'screenshotone_configured' => !empty(config('services.screenshotone.access_key')),
        ]);
    }

    /**
     * Get the name of the service being used based on URL
     */
    private function getServiceName(?string $url): string
    {
        if (empty($url)) {
            return 'None';
        }
        
        if (strpos($url, 'screenshotone.com') !== false) {
            return 'ScreenshotOne';
        } elseif (strpos($url, 'pagepeeker.com') !== false) {
            return 'PagePeeker (Fallback)';
        } elseif (strpos($url, 'thum.io') !== false) {
            return 'Thum.io (Fallback)';
        } elseif (strpos($url, 's-shot.ru') !== false) {
            return 'Mini.s-shot.ru (Fallback)';
        }
        
        return 'Unknown';
    }
}
