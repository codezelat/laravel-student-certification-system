<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} - Currently Unavailable</title>
    
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
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col items-center justify-center p-4">
    
    <!-- Logo -->
    <div class="mb-8">
        <img src="{{ asset('images/sitc-logo.png') }}" alt="SITC" class="h-12 w-auto opacity-50 grayscale">
    </div>

    <!-- Content -->
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-6">
            <svg class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1 class="text-xl font-bold text-gray-900 mb-2">Form currently unavailable</h1>
        <p class="text-gray-500 mb-6">
            The form <span class="font-medium text-gray-900">"{{ $form->title }}"</span> is not accepting submissions at this time.
        </p>

        <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
            Please check back later or contact the administrator if you believe this is an mistake.
        </div>
    </div>

    <div class="mt-8 text-xs text-gray-400">
        &copy; {{ date('Y') }} SITC Certifier
    </div>

</body>
</html>
