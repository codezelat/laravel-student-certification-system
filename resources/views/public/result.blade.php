<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $form->title }}</title>
    
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
                    },
                    animation: {
                        bounce: 'bounce 1s infinite',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 font-sans text-gray-900">
    
    <!-- Background decoration -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[10%] right-[10%] w-[40%] h-[40%] rounded-full bg-indigo-50 blur-3xl opacity-60"></div>
        <div class="absolute bottom-[10%] left-[10%] w-[30%] h-[30%] rounded-full bg-blue-50 blur-3xl opacity-60"></div>
    </div>

    <div class="max-w-4xl w-full relative z-10 space-y-8">
        
        <!-- Header Section -->
        <div class="text-center">
            <div class="mx-auto h-16 w-auto flex items-center justify-center mb-6">
                 <img src="{{ asset('images/sitc-logo.png') }}" alt="SITC Logo" class="h-full object-contain drop-shadow-sm">
            </div>
            
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100/50 text-green-600 mb-6 ring-8 ring-white shadow-lg backdrop-blur-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">
                Assessment Completed
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                You have successfully recorded your response for <span class="font-bold text-indigo-900">{{ $form->title }}</span>.
            </p>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            
            <!-- Result Summary Card -->
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl border border-white/50 overflow-hidden flex flex-col h-full">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Score Summary</h2>
                    <span class="text-xs font-medium px-2 py-1 bg-gray-200 rounded text-gray-600">ID: #{{ $submission->id }}</span>
                </div>
                
                <div class="p-8 flex flex-col items-center justify-center flex-grow">
                    <div class="relative w-48 h-48">
                        <!-- Circular Progress/Score Display -->
                        <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-indigo-500 border-t-transparent animate-[spin_1s_ease-out_reverse]" style="transform: rotate({{ ($submission->score_percentage / 100) * 360 }}deg)"></div>
                        <div class="absolute inset-2 rounded-full bg-gradient-to-br from-indigo-50 leading-tight to-white shadow-inner flex flex-col items-center justify-center">
                             <span class="text-5xl font-black text-indigo-600">{{ $submission->score }}<span class="text-2xl text-gray-400 font-medium">/{{ $submission->total_questions }}</span></span>
                             <span class="text-sm font-bold text-indigo-400 uppercase tracking-widest mt-1">Score</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 text-center space-y-2">
                         <div class="text-2xl font-bold text-gray-900">{{ $submission->score_percentage }}% Accuracy</div>
                         <p class="text-sm text-gray-500">
                            @if($submission->score_percentage >= 50)
                                Great job! You passed the assessment.
                            @else
                                Good effort! Keep learning.
                            @endif
                         </p>
                    </div>
                </div>

                <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-200/50">
                        <span class="text-gray-500">Name</span>
                        <span class="font-medium text-gray-900">{{ $submission->full_name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-200/50">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-gray-900">{{ $submission->email }}</span>
                    </div>
                     <div class="flex justify-between py-1">
                        <span class="text-gray-500">Date</span>
                        <span class="font-medium text-gray-900">{{ $submission->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Certificate Card -->
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl border border-white/50 overflow-hidden flex flex-col h-full">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                     <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Digital Certificate</h2>
                </div>
                
                <div class="p-6 flex-grow bg-gray-100/50 flex items-center justify-center">
                    <div class="relative w-full shadow-2xl rounded bg-white transform transition-transform hover:scale-[1.02] duration-300 border-[8px] border-white">
                         <img src="{{ route('public.certificate.view', $form->slug) }}" alt="Certificate Preview" class="w-full h-auto block select-none">
                    </div>
                </div>

                <div class="p-6 bg-white border-t border-gray-100 space-y-4">
                     <a href="{{ route('public.certificate.download', $form->slug) }}" class="group w-full flex items-center justify-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="mr-2 h-6 w-6 group-hover:animate-bounce" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Certificate (HQ)
                    </a>
                    
                    <a href="{{ route('public.show', $form->slug) }}" class="block w-full text-center py-3 px-4 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                        Take Quiz Again
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center">
            <p class="text-xs text-slate-400">
                &copy; {{ date('Y') }} SITC Certifier. Verified Assessment.
            </p>
        </div>
    </div>


</body>
</html>
