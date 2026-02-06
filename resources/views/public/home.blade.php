<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITC - Student Certification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f9fafb;
            color: #6b7280;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        p {
            font-size: 0.875rem;
        }
        .logo {
            height: 48px;
            width: auto;
            margin-bottom: 1.5rem;
            opacity: 0.8;
            filter: grayscale(100%);
        }
    </style>
</head>
<body>
    <div class="container">
        @if(file_exists(public_path('images/sitc-logo.png')))
            <img src="{{ asset('images/sitc-logo.png') }}" alt="SITC" class="logo">
        @endif
        <h1>Student Certification System</h1>
        <p>Restricted Access Portal</p>
    </div>
</body>
</html>
