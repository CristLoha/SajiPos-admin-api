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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #3949AB;
            --primary-dark: #283593;
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            color: var(--text-main);
        }
        
        /* Header Styling */
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-title h1 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-title p {
            margin: 0;
            margin-top: 0.25rem;
            opacity: 0.9;
            font-size: 0.85rem;
        }

        /* Layout Grid */
        .layout {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }

        /* Sidebar TOC */
        .sidebar {
            width: 300px;
            flex-shrink: 0;
            height: calc(100vh - 80px);
            position: sticky;
            top: 80px;
            overflow-y: auto;
            padding: 2rem 1.5rem;
            background: #ffffff;
            border-right: 1px solid var(--border-color);
        }
        .sidebar h3 {
            margin-top: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar li {
            margin-bottom: 0.5rem;
        }
        .sidebar li.toc-h2 {
            margin-top: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .sidebar li.toc-h3 {
            padding-left: 1rem;
            font-size: 0.9rem;
            position: relative;
        }
        .sidebar li.toc-h3::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #cbd5e1;
        }
        .sidebar a {
            text-decoration: none;
            color: var(--text-muted);
            display: block;
            line-height: 1.4;
            transition: color 0.2s;
        }
        .sidebar a:hover {
            color: var(--primary);
        }

        /* Main Content area */
        .content {
            flex-grow: 1;
            padding: 3rem 4rem;
            background: #ffffff;
            min-width: 0; /* prevent flex blowout */
        }
        
        .content-inner {
            max-width: 850px;
            margin: 0 auto;
        }

        /* Customizing GitHub Markdown to look like Stripe/Scalar */
        .markdown-body {
            font-family: inherit !important;
            color: var(--text-main) !important;
            font-size: 15px !important;
            line-height: 1.6 !important;
        }
        .markdown-body h1 {
            border-bottom: none !important;
            font-size: 2.2rem !important;
            font-weight: 800 !important;
            margin-bottom: 2rem !important;
        }
        .markdown-body h2 {
            border-bottom: 1px solid var(--border-color) !important;
            padding-bottom: 0.75rem !important;
            margin-top: 3.5rem !important;
            margin-bottom: 1.5rem !important;
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            color: var(--primary-dark) !important;
        }
        .markdown-body h3 {
            color: var(--text-main) !important;
            margin-top: 2.5rem !important;
            margin-bottom: 1rem !important;
            font-size: 1.25rem !important;
            font-weight: 600 !important;
        }
        .markdown-body h4 {
            color: var(--text-muted) !important;
            text-transform: uppercase;
            font-size: 0.85rem !important;
            letter-spacing: 0.5px;
            margin-top: 1.5rem !important;
        }
        .markdown-body pre {
            background-color: #0f172a !important;
            border-radius: 8px !important;
            padding: 1.25rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .markdown-body pre code {
            font-family: 'JetBrains Mono', ui-monospace, monospace !important;
            font-size: 13.5px !important;
            color: #f8fafc !important;
        }
        .markdown-body code {
            font-family: 'JetBrains Mono', ui-monospace, monospace !important;
            font-size: 0.85em !important;
            background-color: #f1f5f9 !important;
            padding: 0.2em 0.4em !important;
            border-radius: 4px !important;
            color: #ef4444 !important;
        }
        .markdown-body ul {
            padding-left: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
        .markdown-body ul li {
            margin-bottom: 0.5rem !important;
            color: var(--text-muted) !important;
        }
        .markdown-body ul li strong {
            color: var(--text-main) !important;
        }
        .markdown-body blockquote {
            border-left-color: var(--primary) !important;
            background-color: #eff6ff !important;
            padding: 1rem 1.5rem !important;
            color: #1e3a8a !important;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0 !important;
        }
        
        /* Badges for HTTP Methods */
        .api-badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: white;
            text-transform: uppercase;
            margin-right: 8px;
            vertical-align: text-bottom;
        }
        .badge-get { background-color: #10b981; } /* Emerald Green */
        .badge-post { background-color: #3b82f6; } /* Blue */
        .badge-put { background-color: #f59e0b; } /* Amber/Orange */
        .badge-delete { background-color: #ef4444; } /* Red */
        
        /* API Info Block */
        .api-info-list {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem !important;
            margin: 1.5rem 0 !important;
            list-style: none !important;
        }
        .api-info-list li {
            margin-bottom: 0.75rem !important;
            display: flex;
            align-items: center;
        }
        .api-info-list li:last-child {
            margin-bottom: 0 !important;
        }
        .api-info-label {
            width: 130px;
            flex-shrink: 0;
            font-weight: 600;
            color: var(--text-main);
        }

        /* URL Endpoint styling */
        .endpoint-url {
            background-color: #f1f5f9;
            color: var(--primary-dark) !important;
            font-weight: 600;
            font-size: 14px !important;
            padding: 4px 8px !important;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        
        @media (max-width: 900px) {
            .layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 1.5rem;
            }
            .content {
                padding: 1.5rem;
            }
            .api-info-list li {
                flex-direction: column;
                align-items: flex-start;
            }
            .api-info-label {
                margin-bottom: 0.25rem;
            }
        }
    </style>

    <!-- Menggunakan versi 'light' murni dari github-markdown-css agar tidak bentrok dengan dark mode OS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css@5/github-markdown-light.min.css" />
    <script type="module" src="https://cdn.jsdelivr.net/gh/zerodevx/zero-md@2/dist/zero-md.min.js"></script>
</head>
<body>

    <div class="header">
        <div class="header-title">
            <h1>📚 SajiPOS API Docs</h1>
            <p>Referensi integrasi Backend ↔ Frontend</p>
        </div>
    </div>

    <div class="layout">
        <aside class="sidebar">
            <h3>Daftar Isi</h3>
            <ul id="toc"></ul>
        </aside>

        <main class="content">
            <div class="content-inner markdown-body">
                <!-- Zero-MD menggunakan no-shadow agar styling custom CSS di atas berfungsi -->
                <zero-md id="md-renderer" no-shadow>
                    <script type="text/markdown">
{!! file_get_contents(base_path('API_DOCUMENTATION.md')) !!}
                    </script>
                </zero-md>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('md-renderer').addEventListener('zero-md-rendered', () => {
            const md = document.getElementById('md-renderer');
            const headings = md.querySelectorAll('h2, h3');
            const toc = document.getElementById('toc');
            
            // Generate TOC
            headings.forEach(h => {
                if(h.tagName === 'H2' && h.textContent.includes('Dokumentasi API')) return;
                
                if(!h.id) h.id = h.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                
                const li = document.createElement('li');
                li.className = 'toc-' + h.tagName.toLowerCase();
                
                const a = document.createElement('a');
                a.href = '#' + h.id;
                
                let text = h.textContent;
                text = text.replace(/[\u{1F300}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]/gu, '').trim();
                a.textContent = text;
                
                li.appendChild(a);
                toc.appendChild(li);
                
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.getElementById(h.id);
                    window.scrollTo({
                        top: target.getBoundingClientRect().top + window.scrollY - 90,
                        behavior: 'smooth'
                    });
                });
            });

            // Percantik Tampilan Endpoint (Badges & API Info Card)
            // Mencari tag <ul> yang berisi metadata URL, Method, Headers
            const uls = md.querySelectorAll('ul');
            uls.forEach(ul => {
                const textContent = ul.textContent;
                // Cek apakah list ini adalah blok info endpoint
                if (textContent.includes('URL:') && textContent.includes('Method:')) {
                    ul.classList.add('api-info-list');
                    
                    const lis = ul.querySelectorAll('li');
                    lis.forEach(li => {
                        let html = li.innerHTML;
                        
                        // Style Method dengan Badge Berwarna
                        if (html.includes('**Method:**') || html.includes('Method:')) {
                            html = html.replace(/\*\*Method:\*\*/g, '<span class="api-info-label">Method</span>');
                            if (html.includes('GET')) html = html.replace('`GET`', '').replace('GET', '<span class="api-badge badge-get">GET</span>');
                            if (html.includes('POST')) html = html.replace('`POST`', '').replace('POST', '<span class="api-badge badge-post">POST</span>');
                            if (html.includes('PUT')) html = html.replace('`PUT`', '').replace('PUT', '<span class="api-badge badge-put">PUT</span>');
                            if (html.includes('DELETE')) html = html.replace('`DELETE`', '').replace('DELETE', '<span class="api-badge badge-delete">DELETE</span>');
                        }
                        
                        // Style URL Endpoint
                        if (html.includes('**URL:**') || html.includes('URL:')) {
                            html = html.replace(/\*\*URL:\*\*/g, '<span class="api-info-label">Endpoint</span>');
                            // Ganti <code> bawaan menjadi class endpoint-url
                            html = html.replace(/<code>(.*?)<\/code>/g, '<code class="endpoint-url">$1</code>');
                        }

                        // Style Label Lainnya
                        if (html.includes('**Headers:**') || html.includes('Headers:')) {
                            html = html.replace(/\*\*Headers:\*\*/g, '<span class="api-info-label">Headers</span>');
                        }
                        if (html.includes('**Auth Required:**') || html.includes('Auth Required:')) {
                            html = html.replace(/\*\*Auth Required:\*\*/g, '<span class="api-info-label">Auth</span>');
                        }
                        
                        li.innerHTML = html;
                    });
                }
            });
        });
    </script>
</body>
</html>
