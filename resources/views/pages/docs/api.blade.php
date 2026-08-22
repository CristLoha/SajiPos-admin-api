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
            color: #334155;
        }
        
        /* Header Styling */
        .header {
            background: linear-gradient(135deg, #3949AB, #283593);
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
            border-right: 1px solid #e2e8f0;
        }
        .sidebar h3 {
            margin-top: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
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
            margin-top: 1rem;
            font-weight: 600;
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
            color: #475569;
            display: block;
            line-height: 1.4;
            transition: color 0.2s;
        }
        .sidebar a:hover {
            color: #3949AB;
        }

        /* Main Content area */
        .content {
            flex-grow: 1;
            padding: 2rem 3rem;
            background: #ffffff;
            min-width: 0; /* prevent flex blowout */
        }
        
        .content-inner {
            max-width: 850px;
            margin: 0 auto;
        }

        /* Tweaks for zero-md light DOM */
        .markdown-body {
            font-family: inherit !important;
        }
        .markdown-body h1, .markdown-body h2 {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-top: 2rem;
            color: #1e293b;
        }
        .markdown-body h3 {
            color: #334155;
            margin-top: 1.5rem;
        }
        .markdown-body pre {
            background-color: #1e293b !important;
            border-radius: 8px !important;
        }
        .markdown-body code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
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
                border-bottom: 1px solid #e2e8f0;
                padding: 1.5rem;
            }
            .content {
                padding: 1.5rem;
            }
        }
    </style>

    <!-- Zero MD for beautiful Markdown rendering -->
    <!-- Using GitHub css for styling the light DOM -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css@5/github-markdown.min.css" />
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
        <!-- Sidebar Daftar Isi -->
        <aside class="sidebar">
            <h3>Daftar Isi</h3>
            <ul id="toc"></ul>
        </aside>

        <!-- Konten Utama -->
        <main class="content">
            <div class="content-inner markdown-body">
                <!-- Zero-MD using light DOM (no-shadow) so we can scrape it -->
                <zero-md id="md-renderer" no-shadow>
                    <script type="text/markdown">
{!! file_get_contents(base_path('API_DOCUMENTATION.md')) !!}
                    </script>
                </zero-md>
            </div>
        </main>
    </div>

    <!-- Script untuk membuat Daftar Isi (TOC) secara dinamis -->
    <script>
        document.getElementById('md-renderer').addEventListener('zero-md-rendered', () => {
            const md = document.getElementById('md-renderer');
            // Ambil semua H2 dan H3 di dalam dokumen hasil render
            const headings = md.querySelectorAll('h2, h3');
            const toc = document.getElementById('toc');
            
            headings.forEach(h => {
                // Jangan masukkan H2 pertama jika itu judul dokumen
                if(h.tagName === 'H2' && h.textContent.includes('Dokumentasi API')) return;
                
                // Buat ID unik untuk linking jika belum ada
                if(!h.id) {
                    h.id = h.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                }
                
                const li = document.createElement('li');
                li.className = 'toc-' + h.tagName.toLowerCase();
                
                const a = document.createElement('a');
                a.href = '#' + h.id;
                
                // Hapus emoji di depan teks untuk TOC agar lebih rapi
                let text = h.textContent;
                text = text.replace(/[\u{1F300}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]/gu, '').trim();
                a.textContent = text;
                
                li.appendChild(a);
                toc.appendChild(li);
                
                // Tambahkan efek scroll smooth
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.getElementById(h.id);
                    // Scroll ke elemen dengan offset header 90px
                    window.scrollTo({
                        top: target.getBoundingClientRect().top + window.scrollY - 90,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>
