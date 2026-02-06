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
<body class="h-full bg-gray-50 flex flex-col">
    <!-- Confetti Container -->
    <div id="confetti" class="fixed inset-0 pointer-events-none z-50 overflow-hidden"></div>

    <main class="flex-grow p-4 sm:p-6 lg:p-8">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="mb-4 inline-block animate-bounce">
                    <span class="text-6xl">🎉</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight mb-2">
                    Congratulations!
                </h1>
                <p class="text-lg text-gray-600">
                    You have successfully completed the <span class="font-semibold text-primary-600">{{ $form->title }}</span>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                
                <!-- Score Card -->
                <div class="md:col-span-5 space-y-6">
                    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <span class="mr-2">📊</span> Your Results
                            </h2>
                        </div>
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-40 h-40 rounded-full bg-gradient-to-br from-primary-500 to-indigo-600 shadow-lg shadow-primary-500/30 mb-6">
                                <div class="text-center text-white">
                                    <div class="text-4xl font-bold">{{ $submission->score }}/{{ $submission->total_questions }}</div>
                                    <div class="text-xs font-medium uppercase tracking-wider opacity-80 mt-1">Correct</div>
                                </div>
                            </div>
                            
                            <div class="text-2xl font-bold text-gray-900 mb-1">{{ $submission->score_percentage }}% Score</div>
                            <p class="text-sm text-gray-500">
                                @if($submission->score_percentage >= 50)
                                    Great job! You passed the quiz.
                                @else
                                    Good effort! Keep learning.
                                @endif
                            </p>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Name</span>
                                    <span class="font-medium text-gray-900">{{ $submission->full_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email</span>
                                    <span class="font-medium text-gray-900">{{ $submission->email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Date</span>
                                    <span class="font-medium text-gray-900">{{ $submission->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('public.show', $form->slug) }}" class="block w-full text-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                        Take Quiz Again
                    </a>
                </div>

                <!-- Certificate Card -->
                <div class="md:col-span-7">
                    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <span class="mr-2">🏆</span> Your Certificate
                            </h2>
                        </div>
                        <div class="p-6 flex-grow flex flex-col items-center justify-center bg-gray-50/30">
                            <div class="relative w-full rounded-lg shadow-lg overflow-hidden border border-gray-200 group">
                                @if($form->certificate_image)
                                    <img src="{{ Storage::url($form->certificate_image) }}" alt="Certificate" class="w-full h-auto">
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <!-- Overlay Name (Simulated preview) -->
                                        <span class="text-2xl sm:text-3xl font-bold text-blue-900 drop-shadow-md transform translate-y-4">
                                            {{ $submission->full_name }}
                                        </span>
                                    </div>
                                @else
                                    <div class="bg-[#fdf5e6] p-8 text-center min-h-[300px] flex flex-col items-center justify-center">
                                        <div class="text-2xl text-yellow-800 font-serif mb-2">CERTIFICATE</div>
                                        <div class="text-sm text-gray-500 mb-6 uppercase tracking-widest">Of Participation</div>
                                        <div class="text-3xl font-bold text-blue-900 font-serif border-b-2 border-yellow-800 pb-2 mb-4 px-8">{{ $submission->full_name }}</div>
                                        <p class="text-gray-600 text-sm">Has successfully completed the {{ $form->title }}</p>
                                    </div>
                                @endif
                                
                                <!-- Hover Overlay hint -->
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="bg-white/90 backdrop-blur text-gray-800 px-4 py-2 rounded-full text-xs font-semibold shadow-sm">Preview</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white border-t border-gray-100">
                             <a href="{{ route('public.certificate.download', $form->slug) }}" class="w-full flex justify-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform hover:-translate-y-1 transition-all duration-200">
                                <svg class="mr-2 -ml-1 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Certificate
                            </a>
                            <p class="mt-3 text-center text-xs text-gray-400">
                                High-quality PNG format ready for printing.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        // Confetti Effect
        function createConfetti() {
            const container = document.getElementById('confetti');
            const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6'];
            
            for (let i = 0; i < 60; i++) {
                const piece = document.createElement('div');
                piece.style.position = 'absolute';
                piece.style.width = Math.random() * 8 + 6 + 'px';
                piece.style.height = Math.random() * 8 + 6 + 'px';
                piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                piece.style.left = Math.random() * 100 + 'vw';
                piece.style.top = -20 + 'px';
                piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                piece.style.opacity = Math.random() * 0.5 + 0.5;
                
                // Random animation properties
                const duration = Math.random() * 3 + 3; // 3-6s
                const delay = Math.random() * 2; // 0-2s
                
                piece.style.transition = `top ${duration}s ease-in, transform ${duration}s linear, opacity ${duration}s ease-in`;
                
                container.appendChild(piece);

                // Trigger animation
                setTimeout(() => {
                   piece.style.top = '110vh';
                   piece.style.transform = `rotate(${Math.random() * 720}deg) translateX(${Math.random() * 100 - 50}px)`;
                   piece.style.opacity = '0';
                }, delay * 100);
            }
        }

        // Run confetti on load
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>
