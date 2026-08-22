<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>403 &mdash; SajiPOS</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sajipos-modern.css') }}">
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="page-error">
                    <div class="page-inner">
                        <div class="mb-4 text-warning">
                            <i class="fas fa-lock fa-5x"></i>
                        </div>
                        <h1>403</h1>
                        <div class="page-description">
                            Akses Dibatasi. Akun Anda terdaftar sebagai <strong>{{ auth()->check() ? ucfirst(auth()->user()->roles) : 'Tamu' }}</strong>.
                        </div>
                        <div class="page-search">
                            <p class="text-muted">
                                Role Anda tidak memiliki izin untuk mengakses halaman dashboard web admin ini.<br>
                                Silakan gunakan aplikasi <strong>POS Kasir SajiPOS (Flutter)</strong> untuk melakukan transaksi.
                            </p>
                            <div class="mt-4">
                                @if (auth()->check())
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                    <a href="#" class="btn btn-danger btn-lg" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout Akun
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Kembali ke Login</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="simple-footer mt-5">
                    Copyright &copy; SajiPOS 2026
                </div>
            </div>
        </section>
    </div>
</body>

</html>
