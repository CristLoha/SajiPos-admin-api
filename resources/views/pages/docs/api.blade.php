<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SajiPOS API Documentation</title>
    <!-- Favicon Sendok -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(135deg, #3949AB, #283593);
            color: white;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
        }
        .header p {
            margin-top: 0.5rem;
            opacity: 0.9;
        }
        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        
        /* Loading state */
        .loading {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>

    <!-- Zero MD for beautiful Markdown rendering -->
    <script type="module" src="https://cdn.jsdelivr.net/gh/zerodevx/zero-md@2/dist/zero-md.min.js"></script>
</head>
<body>

    <div class="header">
        <h1>📚 SajiPOS API Documentation</h1>
        <p>Referensi integrasi lengkap untuk tim Mobile / Frontend Developer</p>
    </div>

    <div class="container">
        <!-- Zero-MD will render the markdown here -->
        <zero-md>
            <script type="text/markdown">
{!! file_get_contents(base_path('API_DOCUMENTATION.md')) !!}
            </script>
        </zero-md>
    </div>

</body>
</html>
