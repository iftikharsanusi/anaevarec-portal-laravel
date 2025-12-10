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
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* ======================= NAVBAR CUSTOM STYLES ======================= */

        .brand-logo {
            font-family: 'Rubik', sans-serif;
            font-weight: 700;
            font-size: 1.85rem;
            letter-spacing: -0.025em;
            color: #4f46e5 !important;
            text-decoration: none !important;
        }

        .brand-logo:hover,
        .brand-logo:focus {
            color: #4338ca !important;
        }

        /* Adjust navbar height similar to welcome page (around 5rem / 80px) */
        .navbar {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            min-height: 80px;
            position: relative;
        }

        /* Center the nav links */
        .navbar-nav-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .navbar-nav-center {
                position: static;
                transform: none;
                flex-direction: column;
                gap: 0.75rem;
                margin: 0.5rem 0;
            }
        }

        /* Nav links style (Dashboard & History) */
        .navbar-nav-center .nav-link {
            font-family: 'Rubik', sans-serif;
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
        .navbar-nav.ms-auto .nav-link {
            font-family: 'Rubik', sans-serif;
            font-weight: 600;
            font-size: 1.05rem;
        }

        .dropdown-item {
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
            color: #374151;
        }

        .dropdown-item:hover {
            color: #4f46e5 !important;
            background-color: #f3f4f6 !important;
            transform: translateX(2px);
        }

        /* Professional logout button styling */
        .dropdown-menu {
            padding: 0;
            border: none;
            box-shadow: none;
            background: transparent;
        }

        .dropdown-item[href*="logout"] {
            color: #ffffff !important;
            background-color: #dc2626;
            font-weight: 600;
            border: 1px solid #dc2626;
            border-radius: 6px;
            margin: 0;
            padding: 0.75rem 1rem;
            text-align: center;
            width: 100%;
            display: block;
            transition: all 0.2s ease;
        }

        .dropdown-item[href*="logout"]:hover {
            background-color: #b91c1c !important;
            border-color: #b91c1c !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }

        /* Professional navbar styling with glass morphism effect */
        .navbar-light.bg-white {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 0 rgba(255, 255, 255, 0.5) inset;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Guest Navbar - Bracket-style Login and Register buttons */
        #navbarGuest .nav-link {
            font-family: 'Rubik', sans-serif;
            font-weight: 600;
            font-size: 1.15rem;
            padding: 0.75rem 2rem !important;
            margin: 0 0.5rem;
            position: relative;
            background-color: transparent;
            color: #000000 !important;
            border: none;
            border-radius: 0;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            overflow: visible;
        }

        /* Top-left bracket - positioned slightly inward, black */
        #navbarGuest .nav-link::before {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            width: 12px;
            height: 12px;
            border-top: 2px solid #000000;
            border-left: 2px solid #000000;
            transition: all 0.3s ease;
        }

        /* Bottom-right bracket - positioned slightly inward, black */
        #navbarGuest .nav-link::after {
            content: '';
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 12px;
            height: 12px;
            border-bottom: 2px solid #000000;
            border-right: 2px solid #000000;
            transition: all 0.3s ease;
        }

        /* Top-right and bottom-left brackets using background-image - positioned slightly inward, black */
        #navbarGuest .nav-link {
            background-image: 
                /* Top-right horizontal line */
                linear-gradient(to right, #000000 0%, #000000 12px, transparent 12px),
                /* Top-right vertical line */
                linear-gradient(to bottom, #000000 0%, #000000 12px, transparent 12px),
                /* Bottom-left horizontal line */
                linear-gradient(to right, transparent calc(100% - 16px), #000000 calc(100% - 16px), #000000 calc(100% - 4px)),
                /* Bottom-left vertical line */
                linear-gradient(to bottom, transparent calc(100% - 16px), #000000 calc(100% - 16px), #000000 calc(100% - 4px));
            background-size: 
                12px 2px,  /* top-right horizontal */
                2px 12px,  /* top-right vertical */
                12px 2px,  /* bottom-left horizontal */
                2px 12px;  /* bottom-left vertical */
            background-position: 
                top 4px right 4px,    /* top-right horizontal */
                top 4px right 4px,    /* top-right vertical */
                bottom 4px left 4px,  /* bottom-left horizontal */
                bottom 4px left 4px;  /* bottom-left vertical */
            background-repeat: no-repeat;
        }

        /* Hover state - white background, blue text, brackets extend and connect */
        #navbarGuest .nav-link:hover {
            background-color: #ffffff !important;
            color: #4f46e5 !important;
            transform: translateY(-1px);
            background-image: 
                /* Top-right horizontal line - extends to connect with top */
                linear-gradient(to right, transparent 0%, #4f46e5 0%, #4f46e5 100%),
                /* Top-right vertical line - extends to connect with right */
                linear-gradient(to bottom, transparent 0%, #4f46e5 0%, #4f46e5 100%),
                /* Bottom-left horizontal line - extends to connect with bottom */
                linear-gradient(to right, #4f46e5 0%, #4f46e5 100%, transparent 100%),
                /* Bottom-left vertical line - extends to connect with left */
                linear-gradient(to bottom, #4f46e5 0%, #4f46e5 100%, transparent 100%);
            background-size: 
                calc(100% - 4px) 2px,  /* top horizontal - full width */
                2px calc(100% - 4px),  /* right vertical - full height */
                calc(100% - 4px) 2px,  /* bottom horizontal - full width */
                2px calc(100% - 4px);  /* left vertical - full height */
            background-position: 
                top 0 right 0,    /* top horizontal */
                top 0 right 0,    /* right vertical */
                bottom 0 left 0,  /* bottom horizontal */
                bottom 0 left 0;  /* left vertical */
            background-repeat: no-repeat;
        }

        /* On hover, extend top-left bracket to connect with top and left sides */
        #navbarGuest .nav-link:hover::before {
            width: calc(100% - 4px);
            height: calc(100% - 4px);
            border-top: 2px solid #4f46e5;
            border-left: 2px solid #4f46e5;
            border-right: none;
            border-bottom: none;
            top: 0;
            left: 0;
        }

        /* On hover, extend bottom-right bracket to connect with bottom and right sides */
        #navbarGuest .nav-link:hover::after {
            width: calc(100% - 4px);
            height: calc(100% - 4px);
            border-bottom: 2px solid #4f46e5;
            border-right: 2px solid #4f46e5;
            border-left: none;
            border-top: none;
            bottom: 0;
            right: 0;
        }

        @media (max-width: 767.98px) {
            #navbarGuest .nav-link {
                font-size: 1rem;
                padding: 0.6rem 1.5rem !important;
                margin: 0.25rem 0;
            }
            
            /* Ensure brackets are still visible on mobile */
            #navbarGuest .nav-link::before {
                top: 3px;
                left: 3px;
                width: 10px;
                height: 10px;
            }
            
            #navbarGuest .nav-link::after {
                bottom: 3px;
                right: 3px;
                width: 10px;
                height: 10px;
            }
            
            /* Adjust background-image brackets for mobile */
            #navbarGuest .nav-link {
                background-size: 
                    10px 2px,  /* top-right horizontal */
                    2px 10px,  /* top-right vertical */
                    10px 2px,  /* bottom-left horizontal */
                    2px 10px;  /* bottom-left vertical */
                background-position: 
                    top 3px right 3px,    /* top-right horizontal */
                    top 3px right 3px,    /* top-right vertical */
                    bottom 3px left 3px,  /* bottom-left horizontal */
                    bottom 3px left 3px;  /* bottom-left vertical */
            }
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
                <a class="navbar-brand brand-logo" href="{{ url('/') }}">
                    WSHA
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
                <a class="navbar-brand brand-logo" href="{{ route('home') }} ">
                    WSHA
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
                                DASHBOARD
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}" href="{{ route('history') }}">
                                HISTORY
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}" href="{{ route('analytics') }}">
                                ANALYTICS
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
