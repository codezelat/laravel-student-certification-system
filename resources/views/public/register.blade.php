<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/sitc-fav-150x150.png') }}">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-input: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #334155;
            --danger: #ef4444;
            --radius: 16px;
            --radius-sm: 10px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .logo-img {
            height: 80px;
            width: auto;
            border-radius: var(--radius-sm);
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
            max-width: 500px;
        }

        .card {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(51, 65, 85, 0.5);
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .form-label .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            background: rgba(51, 65, 85, 0.5);
            border: 1px solid rgba(51, 65, 85, 0.8);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            background: rgba(51, 65, 85, 0.8);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: var(--radius-sm);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .info-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: var(--radius-sm);
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .info-box .info-icon {
            font-size: 1.25rem;
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

        .circle:nth-child(1) { width: 500px; height: 500px; top: -150px; right: -150px; }
        .circle:nth-child(2) { width: 400px; height: 400px; bottom: -100px; left: -100px; animation-delay: -8s; }
        .circle:nth-child(3) { width: 300px; height: 300px; top: 40%; left: 60%; animation-delay: -15s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(40px, -40px) rotate(5deg); }
            66% { transform: translate(-30px, 30px) rotate(-3deg); }
        }
    </style>
</head>
<body>
    <div class="animated-bg">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo-container">
                    <img src="{{ asset('images/sitc-logo.png') }}" alt="SITC" class="logo-img">
                </div>
                <h1 class="title">{{ $form->title }}</h1>
                @if($form->description)
                    <p class="subtitle">{{ $form->description }}</p>
                @else
                    <p class="subtitle">Complete this questionnaire to receive your certificate</p>
                @endif
            </div>

            <div class="info-box">
                <span class="info-icon">📋</span>
                <span>{{ $form->questions->count() }} questions • Receive certificate upon completion</span>
            </div>

            @if($errors->any())
                <div class="alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('public.register', $form->slug) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" 
                           name="full_name" 
                           class="form-control" 
                           placeholder="Enter your full name"
                           value="{{ old('full_name') }}"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address <span class="required">*</span></label>
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="your@email.com"
                           value="{{ old('email') }}"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number <span class="required">*</span></label>
                    <input type="tel" 
                           name="mobile" 
                           class="form-control" 
                           placeholder="+94 XX XXX XXXX"
                           value="{{ old('mobile') }}"
                           required>
                </div>

                <button type="submit" class="btn">
                    Start Quiz <span>→</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
