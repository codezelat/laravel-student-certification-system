<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/sitc-fav-150x150.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col items-center justify-center p-4">
    
    <!-- Background decoration -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[20%] left-[20%] w-[30%] h-[30%] rounded-full bg-primary-100/40 blur-3xl"></div>
        <div class="absolute bottom-[20%] right-[20%] w-[35%] h-[35%] rounded-full bg-indigo-100/40 blur-3xl"></div>
    </div>

    <div class="relative z-10 text-center">
        <!-- Logo -->
        <div class="mb-8 flex justify-center">
            <img src="{{ asset('images/sitc-logo.png') }}" alt="SITC" class="h-16 w-auto drop-shadow-sm">
        </div>

        <!-- 404 Content -->
        <h1 class="text-9xl font-black text-gray-200">404</h1>
        
        <div class="mt-[-4rem] mb-6">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight sm:text-4xl">Page not found</h2>
            <p class="mt-3 text-base text-gray-500">Sorry, we couldn't find the page you're looking for.</p>
        </div>

        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transistion-all duration-200">
                Go back home
            </a>
            <a href="#" onclick="history.back()" class="text-sm font-semibold text-gray-900 hover:text-primary-600 transition-colors">
                Go back previous page <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>

    <div class="mt-16 text-center text-xs text-gray-400 relative z-10">
        &copy; {{ date('Y') }} SITC Certifier. All rights reserved.
    </div>

</body>
</html>
