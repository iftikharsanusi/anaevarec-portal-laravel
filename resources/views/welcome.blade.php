<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Web Security Header Analyzer</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Hero Section -->
  <section class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-bold mb-4">Web Security Header Analyzer</h1>
    <p class="text-lg md:text-xl max-w-2xl mx-auto mb-6">
      A Laravel-based Security Header Evaluation System to protect websites against common vulnerabilities.
    </p>
    <a href="/login" class="bg-white text-indigo-600 px-6 py-3 rounded-full font-semibold shadow hover:bg-gray-100 transition">
      Start Scanning Now
    </a>
  </section>

  <!-- Introduction -->
  <section class="py-16 px-6 max-w-5xl mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-900">🔒 Introduction</h2>
    <p class="text-lg leading-relaxed mb-4">
      In today’s digital landscape, websites are exposed to countless security threats caused by weak or missing HTTP response headers. These headers—such as 
      <strong>Content-Security-Policy (CSP)</strong> and <strong>Strict-Transport-Security (HSTS)</strong>—are critical in preventing vulnerabilities like 
      <em>clickjacking</em>, <em>cross-site scripting (XSS)</em>, and <em>protocol downgrade attacks</em>.
    </p>
    <p class="text-lg leading-relaxed">
      The <strong>Web Security Header Analyzer</strong> is a web-based application built with <strong>Laravel, Tailwind CSS, and JavaScript</strong>, running on 
      <strong>XAMPP with MySQL</strong>. It allows users to enter any website URL, fetch security headers via Laravel’s 
      <strong>HTTP Client (Guzzle)</strong>, and evaluate them against best practices. The system provides a 
      <strong>security score</strong>, highlights <strong>misconfigurations</strong>, and offers clear recommendations to strengthen website protection.
    </p>
  </section>

  <!-- Problem Statement -->
  <section class="bg-gray-100 py-16 px-6">
    <div class="max-w-5xl mx-auto">
      <h2 class="text-3xl font-bold mb-6 text-gray-900">🚨 Problem Statement</h2>
      <p class="text-lg mb-4">
        Many small and medium-sized organizations fail to properly implement security headers, leaving their websites vulnerable. While some tools exist, they are often:
      </p>
      <ul class="list-disc list-inside space-y-2 text-lg">
        <li>❌ Not user-friendly</li>
        <li>❌ Limited in functionality</li>
        <li>❌ Missing reporting capabilities</li>
      </ul>
      <p class="text-lg mt-4">
        This project addresses the gap by providing a <strong>user-friendly, educational, and report-generating platform</strong> that analyzes website headers in real-time.
      </p>
    </div>
  </section>

  <!-- Objectives -->
  <section class="py-16 px-6 max-w-5xl mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-900">🎯 Objectives</h2>
    <ol class="list-decimal list-inside space-y-2 text-lg">
      <li>Develop a web-based analyzer for security headers.</li>
      <li>Detect missing or misconfigured HTTP headers.</li>
      <li>Provide real-time results with scores and recommendations.</li>
      <li>Store scan history in a database for easy tracking.</li>
      <li>Generate downloadable reports (PDF/Excel) for documentation.</li>
    </ol>
  </section>

  <!-- Scope -->
  <section class="bg-gray-100 py-16 px-6">
    <div class="max-w-5xl mx-auto">
      <h2 class="text-3xl font-bold mb-6 text-gray-900">📌 Scope of the Project</h2>
      <ul class="list-disc list-inside space-y-2 text-lg">
        <li>✅ User Accounts – Register/Login to access the analyzer.</li>
        <li>✅ URL Scanning – Input any valid website to perform a scan.</li>
        <li>✅ Header Fetching – Uses Laravel HTTP Client (Guzzle) to retrieve headers.</li>
        <li>✅ Header Evaluation – Checks for the following key headers:</li>
        <ul class="ml-6 list-disc list-inside space-y-1">
          <li>Content-Security-Policy (CSP)</li>
          <li>Strict-Transport-Security (HSTS)</li>
          <li>X-Frame-Options</li>
          <li>X-Content-Type-Options</li>
          <li>Referrer-Policy</li>
          <li>Permissions-Policy</li>
        </ul>
        <li>✅ Report Generation – Download scan results in PDF or Excel.</li>
        <li>✅ Scan History – Track previous scans through the database.</li>
      </ul>
    </div>
  </section>

  <!-- Why Choose -->
  <section class="py-16 px-6 max-w-5xl mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-900">🚀 Why Choose This Analyzer?</h2>
    <div class="grid md:grid-cols-2 gap-6 text-lg">
      <div class="p-6 bg-white rounded-xl shadow">
        Built with <strong>Laravel + Tailwind CSS</strong> for speed and scalability.
      </div>
      <div class="p-6 bg-white rounded-xl shadow">
        Provides <strong>educational insights</strong> alongside technical results.
      </div>
      <div class="p-6 bg-white rounded-xl shadow">
        Generates <strong>professional reports</strong> for developers, students, and organizations.
      </div>
      <div class="p-6 bg-white rounded-xl shadow">
        Helps businesses <strong>close security gaps</strong> and improve compliance.
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section id="start" class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-20 px-6 text-center">
    <h2 class="text-3xl md:text-4xl font-bold mb-4">📥 Get Started</h2>
    <p class="text-lg md:text-xl max-w-2xl mx-auto mb-6">
      Try the Web Security Header Analyzer today! Enter a website URL, analyze headers in seconds, and take the first step towards a more secure web presence.
    </p>
    <a href="/login" class="bg-white text-indigo-600 px-6 py-3 rounded-full font-semibold shadow hover:bg-gray-100 transition">
      Start Scanning Now
    </a>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-gray-400 text-center py-6">
    <p>&copy; 2025 Web Security Header Analyzer. Built by Iftikhar Sanusi.</p>
  </footer>

</body>
</html>
