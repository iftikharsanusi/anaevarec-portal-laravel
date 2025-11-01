<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteUrl = 'https://anaevarec.iftikhar.app';
        $siteName = 'AnaEvaRec - Web Security Header Analyzer';
        $author = 'Iftikhar Sanusi';
    @endphp

    <!-- SEO Meta Tags -->
    @yield('meta')
    
    @php
        $defaultTitle = isset($title) ? $title . ' | ' . $siteName : $siteName;
        $defaultDescription = isset($description) ? $description : 'Analyze and evaluate web security headers for any website. Get comprehensive security recommendations and detailed header analysis reports.';
        $defaultImage = $siteUrl . '/images/og-image.png';
        
        // Set defaults for OG and Twitter if not set in child views
        $ogTitle = $ogTitle ?? $defaultTitle;
        $ogDescription = $ogDescription ?? $defaultDescription;
        $ogImage = $ogImage ?? $defaultImage;
        $ogImageAlt = $ogImageAlt ?? 'AnaEvaRec - Web Security Header Analyzer';
        $twitterTitle = $twitterTitle ?? $defaultTitle;
        $twitterDescription = $twitterDescription ?? $defaultDescription;
        $twitterImage = $twitterImage ?? $defaultImage;
        $twitterCreator = $twitterCreator ?? 'IftikharSanusi';
    @endphp

    <!-- Primary Meta Tags -->
    <title>{{ $defaultTitle }}</title>
    <meta name="title" content="{{ $defaultTitle }}">
    <meta name="description" content="{{ $defaultDescription }}">
    <meta name="keywords" content="web security, security headers, HTTP headers, security analysis, CSP, HSTS, X-Frame-Options, website security, cybersecurity, header analyzer">
    <meta name="author" content="{{ $author }}">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_US">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    <meta name="twitter:description" content="{{ $twitterDescription }}">
    <meta name="twitter:image" content="{{ $twitterImage }}">
    <meta name="twitter:image:alt" content="{{ $ogImageAlt }}">
    <meta name="twitter:creator" content="@{{ $twitterCreator }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.ico') }}">



    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* ======================= NAVBAR CUSTOM STYLES ======================= */

        .navbar-brand-custom {
            font-weight: 700;
            font-size: 1.75rem; /* matches 'AnaEvaRec' font size in welcome page */
            color: #4f46e5;
            text-decoration: none;
        }

        .navbar-brand-custom:hover {
            color: #4338ca;
        }

        /* Adjust navbar height similar to welcome page (around 5rem / 80px) */
        .navbar {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            min-height: 80px;
        }

        /* Center the nav links */
        .navbar-nav-center {
            margin: 0 auto;
        }

        /* Nav links style (Dashboard & History) */
        .navbar-nav-center .nav-link {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333 !important;
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            transition: color 0.3s ease;
        }

        .navbar-nav-center .nav-link.active,
        .navbar-nav-center .nav-link:hover {
            color: #4f46e5 !important;
        }

        /* User dropdown (Logout section) */
        .navbar-nav.ms-auto .nav-link,
        .dropdown-item {
            font-weight: 600;
            font-size: 1.05rem;
        }

        .dropdown-item:hover {
            color: #4f46e5 !important;
            background-color: #f3f4f6 !important;
        }

        /* Ensure smooth alignment with welcome page spacing */
        .navbar-light.bg-white {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <div id="app">
        {{-- ================== NAVBAR SECTION ================== --}}

        @guest
        <!-- Navbar for Guests (Login & Register) -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand navbar-brand-custom" href="{{ url('/') }}">
                    AnaEvaRec
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarGuest"
                        aria-controls="navbarGuest" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarGuest">
                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        @endguest


        @auth
        <!-- Navbar for Authenticated Users (Dashboard, History, User Dropdown) -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand navbar-brand-custom" href="{{ route('home') }}">
                    AnaEvaRec
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAuth"
                        aria-controls="navbarAuth" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarAuth">
                    <!-- Center Nav Links -->
                    <ul class="navbar-nav navbar-nav-center">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}" href="{{ route('history') }}">
                                History
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side (User Dropdown) -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @endauth

        {{-- ================== END NAVBAR SECTION ================== --}}

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
