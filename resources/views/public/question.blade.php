<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} - Question {{ $index + 1 }}</title>
    
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
<body class="h-full bg-gray-50 flex flex-col">
    
    <!-- Progress Bar (Fixed Top) -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-gray-200 z-50">
        <div class="h-full bg-gradient-to-r from-primary-500 to-indigo-600 transition-all duration-500 ease-out" style="width: {{ $progress }}%"></div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8 relative z-10">
        <!-- Decoration -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-[50%] h-[50%] rounded-full bg-indigo-50 blur-3xl opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-[50%] h-[50%] rounded-full bg-blue-50 blur-3xl opacity-50"></div>
        </div>

        <div class="w-full max-w-2xl relative z-10">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/sitc-logo.png') }}" alt="Logo" class="h-10 w-auto">
                    <span class="h-5 w-px bg-gray-300"></span>
                    <span class="text-sm font-medium text-gray-500">
                        Question {{ $index + 1 }} of {{ $totalQuestions }}
                    </span>
                </div>
                <div class="text-sm font-bold text-primary-600 bg-primary-50 px-3 py-1 rounded-full">
                    {{ $progress }}% Completed
                </div>
            </div>

            <!-- Question Card -->
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-10">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight mb-8">
                        {{ $question->question_text }}
                    </h2>

                    <form action="{{ route('public.answer', $form->slug) }}" method="POST" id="answerForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <input type="hidden" name="current_index" value="{{ $index }}">

                        <div class="space-y-3">
                            @foreach($question->answers as $answer)
                                <label class="group relative flex items-center p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                                    <input type="radio" 
                                           name="answer_id" 
                                           value="{{ $answer->id }}" 
                                           class="peer sr-only" 
                                           required>
                                    
                                    <!-- Checked State Styles (via peer) -->
                                    <div class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-primary-500 peer-checked:bg-primary-50/50 transition-all pointer-events-none"></div>
                                    
                                    <div class="flex items-center justify-center h-6 w-6 rounded-full border-2 border-gray-300 bg-white group-hover:border-gray-400 peer-checked:border-primary-500 peer-checked:bg-primary-500 transition-all z-10 mr-4 flex-shrink-0">
                                        <svg class="h-3 w-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    
                                    <span class="text-lg text-gray-700 font-medium peer-checked:text-primary-900 z-10">{{ $answer->answer_text }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="pt-8 flex justify-end">
                            <button type="submit" id="submitBtn" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transform hover:-translate-y-0.5 transition-all duration-200">
                                @if($index + 1 >= $totalQuestions)
                                    Complete Quiz
                                    <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    Next Question
                                    <svg class="ml-2 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-6 text-center text-xs text-gray-400">
                Powered by SITC Certifier
            </div>
        </div>
    </main>

    <script>
        // Disable double-submit and show loading state
        document.getElementById('answerForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
        });
    </script>
</body>
</html>
