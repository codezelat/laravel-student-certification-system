<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question {{ $index + 1 }} - {{ $form->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-input: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #334155;
            --secondary: #10b981;
            --radius: 16px;
            --radius-sm: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .progress-bar {
            height: 6px;
            background: var(--bg-input);
            border-radius: 3px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .progress-text {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .card {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(51, 65, 85, 0.5);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .question-text {
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .answers {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .answer-option {
            position: relative;
        }

        .answer-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .answer-label {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: var(--bg-input);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.3s;
        }

        .answer-label:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
        }

        .answer-option input:checked + .answer-label {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.15);
        }

        .answer-marker {
            width: 24px;
            height: 24px;
            border: 2px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .answer-option input:checked + .answer-label .answer-marker {
            border-color: var(--primary);
            background: var(--primary);
        }

        .answer-option input:checked + .answer-label .answer-marker::after {
            content: '✓';
            color: white;
            font-size: 0.8rem;
        }

        .answer-text {
            font-size: 1rem;
            line-height: 1.4;
        }

        .btn {
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: var(--radius-sm);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
        }

        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg .circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.03));
            animation: float 25s infinite ease-in-out;
        }

        .circle:nth-child(1) { width: 400px; height: 400px; top: -100px; right: -100px; }
        .circle:nth-child(2) { width: 350px; height: 350px; bottom: -80px; left: -80px; animation-delay: -8s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }
    </style>
</head>
<body>
    <div class="animated-bg">
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="progress-text">
            <span>Question {{ $index + 1 }} of {{ $totalQuestions }}</span>
            <span>{{ $progress }}% Complete</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $progress }}%"></div>
        </div>

        <div class="card">
            <div class="question-number">{{ $index + 1 }}</div>
            <h2 class="question-text">{{ $question->question_text }}</h2>

            <form action="{{ route('public.answer', $form->slug) }}" method="POST" id="answerForm">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                <input type="hidden" name="current_index" value="{{ $index }}">

                <div class="answers">
                    @foreach($question->answers as $answer)
                        <div class="answer-option">
                            <input type="radio" 
                                   name="answer_id" 
                                   id="answer_{{ $answer->id }}" 
                                   value="{{ $answer->id }}"
                                   required>
                            <label class="answer-label" for="answer_{{ $answer->id }}">
                                <span class="answer-marker"></span>
                                <span class="answer-text">{{ $answer->answer_text }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="actions">
                    <button type="submit" class="btn" id="submitBtn">
                        @if($index + 1 >= $totalQuestions)
                            Complete Quiz <span>✓</span>
                        @else
                            Next Question <span>→</span>
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Disable double-submit
        document.getElementById('answerForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</body>
</html>
