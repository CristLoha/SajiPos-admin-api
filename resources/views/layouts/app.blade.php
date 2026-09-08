<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"
        name="viewport">
    <title>@yield('title') &mdash; SajiPOS</title>
    <!-- Favicon Sendok -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <!-- General CSS Files -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @stack('style')

    <!-- Template CSS -->
    <link rel="stylesheet"
        href="{{ asset('css/style.css') }}">
    <link rel="stylesheet"
        href="{{ asset('css/components.css') }}">

    <!-- SajiPOS Modern Theme -->
    <link rel="stylesheet"
        href="{{ asset('css/sajipos-modern.css') }}">
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <!-- Header -->
            @include('components.header')

            <!-- Sidebar -->
            @include('components.sidebar')

            <!-- Content -->
            @yield('main')

            <!-- Footer -->
            @include('components.footer')
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>

    @stack('scripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    @if(auth()->check() && in_array(auth()->user()->roles, ['admin', 'staff']))
        <!-- Global Audio Element for Notification -->
        <audio id="global-notif-sound" preload="auto">
            <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
        </audio>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notifSound = document.getElementById('global-notif-sound');
                if(notifSound) notifSound.volume = 0.3;
                
                let baseTitle = document.title.replace(/^\(\d+\)\s+/, '');
                let lastUsersJson = null;

                function pollGlobalStatus() {
                    fetch('{{ route("users.polling") }}')
                        .then(r => r.json())
                        .then(data => {
                            let currentUsersJson = JSON.stringify(data.users);
                            let pendingCount = data.users.length;
                            
                            // Update Badge Notifikasi di Tab Browser
                            if (pendingCount > 0) {
                                document.title = `(${pendingCount}) ${baseTitle}`;
                            } else {
                                document.title = baseTitle;
                            }

                            // Update Sidebar Badge
                            let sidebarBadge = document.getElementById('sidebar-pending-badge');
                            if (sidebarBadge) {
                                sidebarBadge.textContent = pendingCount;
                                sidebarBadge.style.display = pendingCount > 0 ? 'inline-block' : 'none';
                            }
                            
                            // If state changed from previous poll (ignore initial load)
                            if (lastUsersJson !== null && lastUsersJson !== currentUsersJson) {
                                // Play sound
                                notifSound.play().catch(e => console.log('Audio blocked:', e));
                                
                                // Jika kita sedang di halaman users (ada tabel live), reload tabelnya via PJAX
                                let container = document.getElementById('live-users-container');
                                if (container) {
                                    fetch(window.location.href)
                                        .then(res => res.text())
                                        .then(html => {
                                            let doc = new DOMParser().parseFromString(html, 'text/html');
                                            let newContainer = doc.getElementById('live-users-container');
                                            if(newContainer) {
                                                container.innerHTML = newContainer.innerHTML;
                                                
                                                // Highlight sejenak
                                                let tbody = document.querySelector('tbody');
                                                if(tbody && tbody.firstElementChild) {
                                                    tbody.firstElementChild.style.transition = "background-color 0.5s ease";
                                                    tbody.firstElementChild.style.backgroundColor = "#d4edda";
                                                    setTimeout(() => { tbody.firstElementChild.style.backgroundColor = "transparent"; }, 2000);
                                                }
                                            }
                                        });
                                }
                            }
                            
                            lastUsersJson = currentUsersJson;
                        })
                        .catch(err => console.error("Global polling error:", err));
                }

                // Run polling every 5 seconds
                setInterval(pollGlobalStatus, 5000);
                // Run once on load to init
                pollGlobalStatus();
            });
        </script>
    @endif
</body>

</html>
