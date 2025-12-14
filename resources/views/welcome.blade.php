<!DOCTYPE html>
<html lang="en" class="scroll-smooth" style="scroll-padding-top: 6rem;">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>AnaEvaRec - Web Security Header Analyzer | Analyze Website Security Headers</title>
    <meta name="title" content="AnaEvaRec - Web Security Header Analyzer | Analyze Website Security Headers">
    <meta name="description" content="Free web security header analyzer. Test and analyze HTTP security headers for any website. Get comprehensive security recommendations, missing header reports, and detailed analysis. Created by Iftikhar Sanusi.">
    <meta name="keywords" content="web security, security headers, HTTP headers, security analysis, CSP, HSTS, X-Frame-Options, website security, cybersecurity, header analyzer, security scanner, web security tool">
    <meta name="author" content="Iftikhar Sanusi">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <link rel="canonical" href="{{ url('/') }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="AnaEvaRec - Web Security Header Analyzer | Analyze Website Security Headers">
    <meta property="og:description" content="Free web security header analyzer. Test and analyze HTTP security headers for any website. Get comprehensive security recommendations and detailed analysis reports.">
    <meta property="og:image" content="{{ url('/images/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="AnaEvaRec - Web Security Header Analyzer">
    <meta property="og:site_name" content="AnaEvaRec">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="AnaEvaRec - Web Security Header Analyzer">
    <meta name="twitter:description" content="Free web security header analyzer. Test and analyze HTTP security headers for any website.">
    <meta name="twitter:image" content="{{ url('/images/og-image.png') }}">
    <meta name="twitter:image:alt" content="AnaEvaRec - Web Security Header Analyzer">
    <meta name="twitter:creator" content="@IftikharSanusi">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"></noscript>

    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebApplication',
        'name' => 'AnaEvaRec',
        'url' => url('/'),
        'description' => 'Free web security header analyzer. Test and analyze HTTP security headers for any website.',
        'applicationCategory' => 'SecurityApplication',
        'operatingSystem' => 'Web',
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'USD'
        ],
        'author' => [
            '@type' => 'Person',
            'name' => 'Iftikhar Sanusi',
            'url' => 'https://anaevarec.iftikhar.app'
        ],
        'creator' => [
            '@type' => 'Person',
            'name' => 'Iftikhar Sanusi'
        ],
        'featureList' => [
            'HTTP Security Header Analysis',
            'Security Score Calculation',
            'Missing Header Detection',
            'Security Recommendations',
            'Detailed Header Reports'
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800 antialiased overflow-x-hidden">

    <nav class="sticky top-0 z-50 w-full bg-white/95 shadow-md backdrop-blur-sm transition-all duration-300">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex justify-between items-center h-20">
                <a href="#" class="font-bold text-indigo-600 tracking-tight hover:scale-105 transition-transform" style="font-size: 1.85rem;">
                    WSHA
                </a>

                <div class="hidden md:flex space-x-6 items-center">
                    <a href="#overview" class="text-gray-600 hover:text-indigo-600 transition-colors font-medium">Overview</a>
                    <a href="#challenges" class="text-gray-600 hover:text-indigo-600 transition-colors font-medium">Challenges</a>
                    <a href="#features" class="text-gray-600 hover:text-indigo-600 transition-colors font-medium">Features</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-indigo-600 transition-colors font-medium">How It Works</a>
                    <a href="#tech-stack" class="text-gray-600 hover:text-indigo-600 transition-colors font-medium">Tech Stack</a>
                    <a href="#benefits" class="text-gray-600 hover:text-indigo-600 transition-colors font-medium">Benefits</a>
                    <a href="#start-scanning" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-semibold shadow-lg hover:bg-indigo-700 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        Get Started
                    </a>
                </div>

                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-800 focus:outline-none p-2 rounded hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">
            <div class="flex flex-col px-6 py-4 space-y-4">
                <a href="#overview" class="text-gray-600 hover:text-indigo-600 transition-colors mobile-link">Overview</a>
                <a href="#challenges" class="text-gray-600 hover:text-indigo-600 transition-colors mobile-link">Challenges</a>
                <a href="#features" class="text-gray-600 hover:text-indigo-600 transition-colors mobile-link">Features</a>
                <a href="#how-it-works" class="text-gray-600 hover:text-indigo-600 transition-colors mobile-link">How It Works</a>
                <a href="#tech-stack" class="text-gray-600 hover:text-indigo-600 transition-colors mobile-link">Tech Stack</a>
                <a href="#benefits" class="text-gray-600 hover:text-indigo-600 transition-colors mobile-link">Benefits</a>
                <a href="#start-scanning" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-semibold text-center shadow-md hover:bg-indigo-700 transition-all mobile-link">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 px-6 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
            <div class="absolute right-0 top-0 bg-white w-96 h-96 rounded-full mix-blend-overlay filter blur-3xl transform translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
            <div class="absolute left-0 bottom-0 bg-white w-96 h-96 rounded-full mix-blend-overlay filter blur-3xl transform -translate-x-1/2 translate-y-1/2 animate-pulse"></div>
        </div>

        <div class="max-w-4xl mx-auto relative z-10">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4" data-aos="fade-down" data-aos-duration="1000">
                Web Security Header Analyzer
            </h1>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8 opacity-90" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                A Laravel-based web application that detects and evaluates website security headers to improve protection against online vulnerabilities.
            </p>
            <div data-aos="zoom-in" data-aos-delay="400">
                <a href="/login" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-bold shadow-lg hover:bg-gray-100 transform hover:scale-105 transition duration-300 ease-in-out" aria-label="Start Scanning Now - Requires Login">
                    Start Scanning Now
                </a>
            </div>
        </div>
    </header>

    <main>

        <section id="overview" class="py-20 px-6 max-w-5xl mx-auto">
            <div data-aos="fade-right" data-aos-duration="800">
                <h2 class="text-3xl font-bold mb-10 text-gray-900 border-b-4 border-indigo-500 inline-block pb-2">Overview</h2>
                
                <div class="bg-white rounded-lg shadow-md p-8 mb-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="100">
                    <p class="text-lg leading-relaxed text-justify text-gray-700 font-sans font-bold">
                        The Web Security Header Analyzer (WSHA) is a web-based platform designed to assess the security posture of websites by analyzing their HTTP response headers.
                        These headers play a vital role in protecting web applications from threats such as cross-site scripting (XSS), clickjacking, and data exposure.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-md p-8 mb-6 hover:shadow-lg transition-shadow duration-300" data-aos="fade-up" data-aos-delay="200">
                    <p class="text-lg leading-relaxed text-justify text-gray-700 font-sans font-bold">
                        WSHA simplifies this process by automatically scanning target URLs, grading security configurations, and providing clear recommendations.
                        Its modern design and interactive results make it suitable for developers, students, and cybersecurity practitioners.
                    </p>
                </div>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <section id="challenges" class="bg-gray-100 py-20 px-6">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6 text-gray-900" data-aos="fade-up">Challenges in Web Security</h2>
                <p class="text-lg mb-10 text-black" data-aos="fade-up" data-aos-delay="100" style="font-family: 'Rubik', sans-serif;">
                    Despite the availability of tools, many websites still lack proper security headers due to:
                </p>

                <ul class="grid md:grid-cols-3 gap-6 text-lg">
                    <li class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500 hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="200">
                        <span class="font-semibold text-red-600 text-xl block mb-2">❌ Complexity</span>
                        <p class="text-black" style="font-family: 'Rubik', sans-serif;">Existing tools can be overly technical and difficult for beginners to use.</p>
                    </li>
                    <li class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500 hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="400">
                        <span class="font-semibold text-red-600 text-xl block mb-2">❌ Limited Features</span>
                        <p class="text-black" style="font-family: 'Rubik', sans-serif;">Many tools lack scan history, user accounts, or report generation capabilities.</p>
                    </li>
                    <li class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500 hover:shadow-xl transition-shadow duration-300" data-aos="fade-up" data-aos-delay="600">
                        <span class="font-semibold text-red-600 text-xl block mb-2">❌ Lack of Awareness</span>
                        <p class="text-black" style="font-family: 'Rubik', sans-serif;">Developers may not fully understand the role of headers in web security.</p>
                    </li>
                </ul>
                <div class="mt-12" data-aos="zoom-in" data-aos-delay="800">
                    <p class="text-lg text-center bg-white p-6 rounded-lg shadow-inner inline-block max-w-3xl text-black" style="font-family: 'Rubik', sans-serif;">
                        WSHA solves these problems by offering a <strong>simple, insightful, and secure</strong> way to assess and improve web header configurations.
                    </p>
                </div>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <section id="features" class="py-20 px-6 max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-10 text-gray-900" data-aos="fade-down">Key Features</h2>
            <ul class="grid md:grid-cols-2 gap-8 text-lg">
                <li class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500 transform transition duration-500 hover:scale-105" data-aos="fade-right" data-aos-delay="100">
                    <div class="text-4xl mb-4">🔍</div>
                    <strong class="text-xl block mb-2 text-indigo-700">Automatic Header Detection</strong>
                    <span class="text-black">Scans target websites and detects missing or misconfigured security headers.</span>
                </li>
                <li class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500 transform transition duration-500 hover:scale-105" data-aos="fade-left" data-aos-delay="200">
                    <div class="text-4xl mb-4">📊</div>
                    <strong class="text-xl block mb-2 text-indigo-700">Real-Time Scoring</strong>
                    <span class="text-black">Generates instant grades and recommendations for improvement.</span>
                </li>
                <li class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500 transform transition duration-500 hover:scale-105" data-aos="fade-right" data-aos-delay="300">
                    <div class="text-4xl mb-4">🕒</div>
                    <strong class="text-xl block mb-2 text-indigo-700">Scan History</strong>
                    <span class="text-black">Stores past analyses, enabling users to monitor changes over time.</span>
                </li>
                <li class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500 transform transition duration-500 hover:scale-105" data-aos="fade-left" data-aos-delay="400">
                    <div class="text-4xl mb-4">📄</div>
                    <strong class="text-xl block mb-2 text-indigo-700">Report Generation</strong>
                    <span class="text-black">Exports detailed PDF reports using DomPDF for documentation or sharing.</span>
                </li>
            </ul>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <section id="how-it-works" class="bg-gray-100 py-20 px-6">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold mb-8 text-gray-900 text-center" data-aos="fade-up">How It Works</h2>
                <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12" data-aos="zoom-in">
                    <ol class="space-y-6 text-lg text-black">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 bg-indigo-100 text-indigo-600 font-bold rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1">1</span>
                            <span>Register or log in to access the analyzer dashboard.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 bg-indigo-100 text-indigo-600 font-bold rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1">2</span>
                            <span>Enter a website URL to initiate the scanning process.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 bg-indigo-100 text-indigo-600 font-bold rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1">3</span>
                            <span>WSHA retrieves the HTTP security headers using a backend request client.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 bg-indigo-100 text-indigo-600 font-bold rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1">4</span>
                            <span>The system evaluates the headers, assigns a grade, and gives recommendations.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 bg-indigo-100 text-indigo-600 font-bold rounded-full w-8 h-8 flex items-center justify-center mr-4 mt-1">5</span>
                            <span>Results are stored in the database and can be exported as a PDF report.</span>
                        </li>
                    </ol>
                </div>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <section id="tech-stack" class="py-20 px-6 max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-gray-900 text-center" data-aos="fade-down">Technology Stack</h2>
            <p class="text-lg text-center text-gray-600 max-w-2xl mx-auto mb-10" data-aos="fade-up">
                WSHA is powered by modern and secure technologies that ensure reliable performance and scalability.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png" alt="Laravel Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">Laravel</p>
                    <p class="text-sm text-gray-500">Backend Framework</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b2/Bootstrap_logo.svg/1200px-Bootstrap_logo.svg.png" alt="Bootstrap Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">Bootstrap</p>
                    <p class="text-sm text-gray-500">UI Framework</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/d/dd/MySQL_logo.svg/1200px-MySQL_logo.svg.png" alt="MySQL Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">MySQL</p>
                    <p class="text-sm text-gray-500">Database</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="400">
                    <img src="https://raw.githubusercontent.com/guzzle/guzzle/master/.github/logo.png" alt="Guzzle HTTP Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">Guzzle HTTP</p>
                    <p class="text-sm text-gray-500">HTTP Client</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="500">
                    <img src="{{ asset('images/dompdf.svg') }}" alt="DomPDF Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">DomPDF</p>
                    <p class="text-sm text-gray-500">PDF Generator</p>
                </div>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <section id="benefits" class="py-20 px-6 max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-gray-900 text-center" data-aos="fade-down">Why Choose WSHA?</h2>
            <div class="grid md:grid-cols-2 gap-8 text-lg">
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500 hover:bg-gray-50 transition-colors" data-aos="flip-up" data-aos-delay="100">
                    <p class="font-bold mb-2 text-indigo-700" style="font-family: 'Rubik', sans-serif;">User-Friendly Interface</p>
                    Simple, clean, and intuitive design for a smooth user experience.
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500 hover:bg-gray-50 transition-colors" data-aos="flip-up" data-aos-delay="200">
                    <p class="font-bold mb-2 text-indigo-700" style="font-family: 'Rubik', sans-serif;">Educational Insights</p>
                    Learn about each security header's purpose and configuration.
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500 hover:bg-gray-50 transition-colors" data-aos="flip-up" data-aos-delay="300">
                    <p class="font-bold mb-2 text-indigo-700" style="font-family: 'Rubik', sans-serif;">Professional Reporting</p>
                    Download well-formatted, printable reports for documentation or compliance.
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500 hover:bg-gray-50 transition-colors" data-aos="flip-up" data-aos-delay="400">
                    <p class="font-bold mb-2 text-indigo-700" style="font-family: 'Rubik', sans-serif;">Security Awareness</p>
                    Encourages better cybersecurity practices aligned with OWASP standards.
                </div>
            </div>
        </section>

        <section id="start-scanning" class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 px-6 text-center" aria-labelledby="start-scanning-heading">
            <h2 id="start-scanning-heading" class="text-3xl md:text-4xl font-bold mb-4" data-aos="fade-down">Get Started</h2>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8" data-aos="fade-up" data-aos-delay="200">
                Try WSHA today — analyze your website, view your report, and start improving your web security in seconds.
            </p>
            <div data-aos="zoom-in" data-aos-delay="400">
                <a href="/login" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-bold shadow-xl hover:bg-gray-100 transform hover:scale-105 transition duration-300 ease-in-out" aria-label="Start Scanning Now - Requires Login">
                    Start Scanning Now
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-gray-400 text-center py-8">
        <div class="max-w-6xl mx-auto px-6">
            <p>&copy; 2025 Web Security Header Analyzer. Built by <strong>Iftikhar Sanusi</strong>.</p>
            <p class="text-sm text-gray-500 mt-2">Diploma in Computer Forensic · Management and Science University</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" onload="initAOSLibrary()"></script>
    <script>
        // Initialize AOS library
        function initAOSLibrary() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    once: true,
                    offset: 50,
                    duration: 800,
                    easing: 'ease-out-cubic',
                    delay: 0,
                    disable: false,
                });
            }
        }

        // Fallback initialization if onload doesn't fire
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof AOS !== 'undefined') {
                    initAOSLibrary();
                }
            }, 200);
        });

        // Refresh AOS after all images load
        window.addEventListener('load', function() {
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        });

        // Mobile Menu Logic
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const links = document.querySelectorAll('.mobile-link');

            if(btn && menu) {
                // Toggle menu on button click
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });

                // Close menu when a link is clicked
                links.forEach(link => {
                    link.addEventListener('click', () => {
                        menu.classList.add('hidden');
                    });
                });
            }
        });
    </script>
</body>
</html>