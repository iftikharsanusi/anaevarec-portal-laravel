<!DOCTYPE html>
<html lang="en" class="scroll-smooth" style="scroll-padding-top: 6rem;">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Web Security Header Analyzer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 w-full bg-white/95 shadow-md backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex justify-between items-center h-20">
                <a href="#" class="text-2xl font-bold text-indigo-600">
                    AnaEvaRec
                </a>

                <div class="hidden md:flex space-x-6 items-center">
                    <a href="#overview" class="text-gray-600 hover:text-indigo-600 transition-colors">Overview</a>
                    <a href="#challenges" class="text-gray-600 hover:text-indigo-600 transition-colors">Challenges</a>
                    <a href="#features" class="text-gray-600 hover:text-indigo-600 transition-colors">Features</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-indigo-600 transition-colors">How It Works</a>
                    <a href="#tech-stack" class="text-gray-600 hover:text-indigo-600 transition-colors">Tech Stack</a>
                    <a href="#benefits" class="text-gray-600 hover:text-indigo-600 transition-colors">Benefits</a>
                    <a href="#start-scanning" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-semibold shadow-lg hover:bg-indigo-700 transition-all">
                        Get Started
                    </a>
                </div>
                <div class="md:hidden">
                    <button class="text-gray-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Web Security Header Analyzer</h1>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8">
                A Laravel-based web application that detects and evaluates website security headers to improve protection against online vulnerabilities.
            </p>
            <a href="/login" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-bold shadow-lg hover:bg-gray-100 transform hover:scale-105 transition duration-300 ease-in-out" aria-label="Start Scanning Now - Requires Login">
                Start Scanning Now
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main>

        <!-- Overview -->
        <section id="overview" class="py-20 px-6 max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold mb-6 text-gray-900">Overview</h2>
            <p class="text-lg leading-relaxed mb-4 text-justify">
                The <strong>Web Security Header Analyzer (WSHA)</strong> is a web-based platform designed to assess the security posture of websites by analyzing their HTTP response headers.
                These headers play a vital role in protecting web applications from threats such as <em>cross-site scripting (XSS)</em>, <em>clickjacking</em>, and <em>data exposure</em>.
            </p>
            <p class="text-lg leading-relaxed text-justify">
                WSHA simplifies this process by automatically scanning target URLs, grading security configurations, and providing clear recommendations.
                Its modern design and interactive results make it suitable for developers, students, and cybersecurity practitioners.
            </p>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <!-- Challenges -->
        <section id="challenges" class="bg-gray-100 py-20 px-6">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6 text-gray-900">Challenges in Web Security</h2>
                <p class="text-lg mb-6">
                    Despite the availability of tools, many websites still lack proper security headers due to:
                </p>
                <ul class="grid md:grid-cols-3 gap-6 text-lg">
                    <li class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
                        <span class="font-semibold text-red-600">❌ Complexity</span>
                        <p class="text-gray-600 mt-2">Existing tools can be overly technical and difficult for beginners to use.</p>
                    </li>
                    <li class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
                        <span class="font-semibold text-red-600">❌ Limited Features</span>
                        <p class="text-gray-600 mt-2">Many tools lack scan history, user accounts, or report generation capabilities.</p>
                    </li>
                    <li class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
                        <span class="font-semibold text-red-600">❌ Lack of Awareness</span>
                        <p class="text-gray-600 mt-2">Developers may not fully understand the role of headers in web security.</p>
                    </li>
                </ul>
                <p class="text-lg mt-8 text-center bg-white p-6 rounded-lg shadow-inner">
                    WSHA solves these problems by offering a <strong>simple, insightful, and secure</strong> way to assess and improve web header configurations.
                </p>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <!-- Features -->
        <section id="features" class="py-20 px-6 max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6 text-gray-900">Key Features</h2>
            <ul class="grid md:grid-cols-2 gap-6 text-lg">
                <li class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-500">
                    <strong>Automatic Header Detection</strong><br>Scans target websites and detects missing or misconfigured security headers.
                </li>
                <li class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-500">
                    <strong>Real-Time Scoring</strong><br>Generates instant grades and recommendations for improvement.
                </li>
                <li class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-500">
                    <strong>Scan History</strong><br>Stores past analyses, enabling users to monitor changes over time.
                </li>
                <li class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-500">
                    <strong>Report Generation</strong><br>Exports detailed PDF reports using <strong>DomPDF</strong> for documentation or sharing.
                </li>
            </ul>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <!-- How It Works -->
        <section id="how-it-works" class="bg-gray-100 py-20 px-6">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl font-bold mb-6 text-gray-900">How It Works</h2>
                <ol class="list-decimal list-inside space-y-4 text-lg text-gray-700 pl-5">
                    <li>Register or log in to access the analyzer dashboard.</li>
                    <li>Enter a website URL to initiate the scan process.</li>
                    <li>WSHA retrieves headers using Laravel’s <strong>Guzzle HTTP Client</strong>.</li>
                    <li>The system evaluates the headers, assigns a grade, and gives recommendations.</li>
                    <li>Results are saved in the <strong>MySQL database</strong> and can be exported as a <strong>PDF report</strong>.</li>
                </ol>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <!-- Tech Stack -->
        <section id="tech-stack" class="py-20 px-6 max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-gray-900 text-center">Technology Stack</h2>
            <p class="text-lg text-center text-gray-600 max-w-2xl mx-auto mb-10">
                WSHA is powered by modern and secure technologies that ensure reliable performance and scalability.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/1200px-Laravel.svg.png" alt="Laravel Logo" class="h-16 mx-auto mb-3">
                    <p class="font-semibold text-lg">Laravel</p>
                    <p class="text-sm text-gray-500">Backend Framework</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Tailwind_CSS_Logo.svg/1200px-Tailwind_CSS_Logo.svg.png" alt="Tailwind CSS Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">Tailwind CSS</p>
                    <p class="text-sm text-gray-500">Frontend Styling</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/d/dd/MySQL_logo.svg/1200px-MySQL_logo.svg.png" alt="MySQL Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">MySQL</p>
                    <p class="text-sm text-gray-500">Database</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-lg">
                    <img src="https://raw.githubusercontent.com/guzzle/guzzle/master/.github/logo.png" alt="Guzzle HTTP Logo" class="h-16 mx-auto mb-3 object-contain">
                    <p class="font-semibold text-lg">Guzzle HTTP</p>
                    <p class="text-sm text-gray-500">HTTP Client</p>
                </div>
            </div>
        </section>

        <hr class="border-gray-200 max-w-5xl mx-auto">

        <!-- Benefits -->
        <section id="benefits" class="py-20 px-6 max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold mb-8 text-gray-900 text-center">Why Choose WSHA?</h2>
            <div class="grid md:grid-cols-2 gap-8 text-lg">
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <p class="font-semibold mb-2">User-Friendly Interface</p>
                    Simple, clean, and intuitive design for a smooth user experience.
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <p class="font-semibold mb-2">Educational Insights</p>
                    Learn about each security header’s purpose and configuration.
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <p class="font-semibold mb-2">Professional Reporting</p>
                    Download well-formatted, printable reports for documentation or compliance.
                </div>
                <div class="p-6 bg-white rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <p class="font-semibold mb-2">Security Awareness</p>
                    Encourages better cybersecurity practices aligned with OWASP standards.
                </div>
            </div>
        </section>

        <!-- Get Started -->
        <section id="start-scanning" class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 px-6 text-center" aria-labelledby="start-scanning-heading">
            <h2 id="start-scanning-heading" class="text-3xl md:text-4xl font-bold mb-4">Get Started</h2>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8">
                Try WSHA today — analyze your website, view your report, and start improving your web security in seconds.
            </p>
            <a href="/login" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-full font-bold shadow-xl hover:bg-gray-100 transform hover:scale-105 transition duration-300 ease-in-out">
                Start Scanning Now
            </a>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 text-center py-8">
        <div class="max-w-6xl mx-auto px-6">
            <p>&copy; 2025 Web Security Header Analyzer. Built by <strong>Iftikhar Sanusi</strong>.</p>
            <p class="text-sm text-gray-500 mt-2">Diploma in Computer Forensic · Management and Science University</p>
        </div>
    </footer>

</body>

</html>