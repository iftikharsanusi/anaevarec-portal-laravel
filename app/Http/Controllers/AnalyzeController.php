<?php

namespace App\Http\Controllers;

use App\Models\HeaderAnalysis;
use Barryvdh\DomPDF\Facade\Pdf;
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

        } catch (\Exception $e) {
            Log::error('Header analysis error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'An error occurred while analyzing the URL: ' . $e->getMessage());
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
        $securityHeaders = [
            'Strict-Transport-Security' => [
                'name' => 'HSTS (HTTP Strict Transport Security)',
                'description' => 'Forces browsers to use HTTPS connections',
                'points' => 15,
                'check' => function($value) {
                    return stripos($value, 'max-age') !== false;
                }
            ],
            'X-Frame-Options' => [
                'name' => 'X-Frame-Options',
                'description' => 'Prevents clickjacking attacks',
                'points' => 10,
                'check' => function($value) {
                    $value = strtolower($value);
                    return in_array($value, ['deny', 'sameorigin']) || stripos($value, 'allow-from') !== false;
                }
            ],
            'X-Content-Type-Options' => [
                'name' => 'X-Content-Type-Options',
                'description' => 'Prevents MIME type sniffing',
                'points' => 10,
                'check' => function($value) {
                    return strtolower($value) === 'nosniff';
                }
            ],
            'X-XSS-Protection' => [
                'name' => 'X-XSS-Protection',
                'description' => 'Enables XSS filtering in browsers',
                'points' => 8,
                'check' => function($value) {
                    $value = strtolower(trim($value));
                    // Valid values: "1", "1; mode=block", "0" (disabled)
                    // We want "1" or "1; mode=block" for security
                    return $value === '1' || $value === '1; mode=block' || $value === '1;mode=block';
                }
            ],
            'Content-Security-Policy' => [
                'name' => 'Content Security Policy (CSP)',
                'description' => 'Prevents XSS and injection attacks',
                'points' => 20,
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'Referrer-Policy' => [
                'name' => 'Referrer Policy',
                'description' => 'Controls referrer information',
                'points' => 8,
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'Permissions-Policy' => [
                'name' => 'Permissions Policy',
                'description' => 'Controls browser features and APIs',
                'points' => 10,
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'X-Permitted-Cross-Domain-Policies' => [
                'name' => 'X-Permitted-Cross-Domain-Policies',
                'description' => 'Controls cross-domain policy files',
                'points' => 5,
                'check' => function($value) {
                    return !empty($value);
                }
            ],
            'Expect-CT' => [
                'name' => 'Expect-CT',
                'description' => 'Certificate Transparency monitoring',
                'points' => 5,
                'check' => function($value) {
                    return stripos($value, 'enforce') !== false || stripos($value, 'report-uri') !== false;
                }
            ],
            'Clear-Site-Data' => [
                'name' => 'Clear-Site-Data',
                'description' => 'Clears browsing data',
                'points' => 4,
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
        
        $encodedUrl = urlencode($cleanUrl);
        
        // Try ScreenshotOne first (if configured)
        $accessKey = config('services.screenshotone.access_key');
        $secretKey = config('services.screenshotone.secret_key');
        
        if (!empty($accessKey)) {
            try {
                // Simplified ScreenshotOne URL - basic parameters that work
                $apiUrl = 'https://api.screenshotone.com/take';
                $params = [
                    'url' => $cleanUrl,
                    'access_key' => $accessKey,
                    'viewport_width' => 1920,
                    'viewport_height' => 1080,
                    'format' => 'png',
                ];
                
                // Only add signature if secret key is provided
                if (!empty($secretKey)) {
                    ksort($params);
                    $queryString = http_build_query($params);
                    $signature = hash_hmac('sha256', $queryString, $secretKey);
                    $params['signature'] = $signature;
                }
                
                $screenshotUrl = $apiUrl . '?' . http_build_query($params);
                Log::info('Generated ScreenshotOne URL', ['url' => substr($screenshotUrl, 0, 100) . '...']);
                return $screenshotUrl;
            } catch (\Exception $e) {
                Log::warning('ScreenshotOne failed, trying fallback', ['error' => $e->getMessage()]);
            }
        }
        
        // Fallback services that work reliably
        // Using multiple services that don't require API keys or have better free tiers
        
        // Fallback 1: Thum.io - Simple and reliable (free tier)
        $thumUrl = "https://image.thum.io/get/width/1200/crop/675/noanimate/{$encodedUrl}";
        
        // Fallback 2: PagePeeker - Free screenshot service
        $pagePeekerUrl = "https://api.pagepeeker.com/v2/thumbs.php?size=x&url={$encodedUrl}";
        
        // Fallback 3: Mini.s-shot.ru - Free but slow (as backup)
        $miniShotUrl = "https://mini.s-shot.ru/1024x768/PNG/1024/?{$encodedUrl}";
        
        // Return Thum.io as primary fallback (most reliable)
        $finalUrl = $thumUrl;
        
        Log::info('Using fallback screenshot service', [
            'original_url' => $url,
            'clean_url' => $cleanUrl,
            'screenshot_url' => $finalUrl
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
}
