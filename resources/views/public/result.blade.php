<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $form->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/sitc-fav-150x150.png') }}">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-input: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border: #334155;
            --radius: 16px;
            --radius-sm: 10px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .logo-img {
            height: 70px;
            width: auto;
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
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .celebration-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounce 1s ease infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-light, #818cf8), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .results-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 800px) {
            .results-grid {
                grid-template-columns: 1fr;
            }
        }

        .score-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2rem;
        }

        .score-card h2 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
        }

        .score-display {
            text-align: center;
            padding: 2rem;
            background: var(--bg-input);
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
        }

        .score-circle {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3);
        }

        .score-value {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .score-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .score-percentage {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
        }

        .participant-info {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 500;
        }

        .certificate-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2rem;
        }

        .certificate-card h2 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
        }

        .certificate-preview {
            background: var(--bg-input);
            border-radius: var(--radius-sm);
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .certificate-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .certificate-placeholder {
            padding: 3rem;
            background: linear-gradient(135deg, #fef9e7, #fdf5e6);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        .certificate-placeholder h3 {
            font-size: 1.5rem;
            color: #8B4513;
            margin-bottom: 0.5rem;
            letter-spacing: 0.1em;
        }

        .certificate-placeholder .subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .certificate-placeholder .name {
            font-size: 1.75rem;
            font-weight: 700;
            color: #003366;
            padding: 0.5rem 2rem;
            border-bottom: 2px solid #8B4513;
            display: inline-block;
            margin: 0.5rem 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: var(--bg-input);
            color: var(--text-primary);
            border: 1px solid var(--border);
            margin-top: 1rem;
        }

        .btn-secondary:hover {
            background: var(--bg-card);
        }

        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
            overflow: hidden;
        }

        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            animation: confetti-fall 4s ease-out forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="confetti" id="confetti"></div>

    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('images/sitc-logo.png') }}" alt="SITC" class="logo-img">
        </div>

        <div class="header">
            <div class="celebration-icon">🎉</div>
            <h1>Congratulations!</h1>
            <p>You have completed the {{ $form->title }}</p>
        </div>

        <div class="results-grid">
            <div class="score-card">
                <h2>📊 Your Score</h2>
                
                <div class="score-display">
                    <div class="score-circle">
                        <span class="score-value">{{ $submission->score }}/{{ $submission->total_questions }}</span>
                        <span class="score-label">Correct</span>
                    </div>
                    <div class="score-percentage">{{ $submission->score_percentage }}%</div>
                </div>

                <div class="participant-info">
                    <div class="info-row">
                        <span class="info-label">Name</span>
                        <span class="info-value">{{ $submission->full_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $submission->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Completed</span>
                        <span class="info-value">{{ $submission->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="certificate-card">
                <h2>🏆 Your Certificate</h2>
                
                <div class="certificate-preview">
                    @if($form->certificate_image)
                        <div style="position: relative;">
                            <img src="{{ Storage::url($form->certificate_image) }}" alt="Certificate Background">
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                                        font-size: 1.5rem; font-weight: 700; color: #003366; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">
                                {{ $submission->full_name }}
                            </div>
                        </div>
                    @else
                        <div class="certificate-placeholder">
                            <h3>CERTIFICATE</h3>
                            <div class="subtitle">OF PARTICIPATION</div>
                            <p style="color: #666; font-size: 0.9rem;">This is to certify that</p>
                            <div class="name">{{ $submission->full_name }}</div>
                            <p style="color: #666; font-size: 0.85rem; margin-top: 1rem;">
                                has successfully completed the {{ $form->title }}
                            </p>
                        </div>
                    @endif
                </div>

                <a href="{{ route('public.certificate.download', $form->slug) }}" class="btn btn-primary">
                    ⬇️ Download Certificate
                </a>

                <a href="{{ route('public.show', $form->slug) }}" class="btn btn-secondary">
                    Take Quiz Again
                </a>
            </div>
        </div>
    </div>

    <script>
        // Create confetti on page load
        function createConfetti() {
            const confettiContainer = document.getElementById('confetti');
            const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6'];
            
            for (let i = 0; i < 50; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                piece.style.left = Math.random() * 100 + '%';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.animationDelay = Math.random() * 2 + 's';
                piece.style.animationDuration = (3 + Math.random() * 2) + 's';
                confettiContainer.appendChild(piece);
            }

            // Remove confetti after animation
            setTimeout(() => {
                confettiContainer.innerHTML = '';
            }, 6000);
        }

        createConfetti();
    </script>
</body>
</html>
