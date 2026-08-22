<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (!window.Toast) {
            window.Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        }

        @if (session('success'))
            window.Toast.fire({
                icon: 'success',
                title: '{!! addslashes(session("success")) !!}'
            });
        @endif

        @if (session('error'))
            window.Toast.fire({
                icon: 'error',
                title: '{!! addslashes(session("error")) !!}'
            });
        @endif

        @if ($errors->any())
            window.Toast.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: '<ul style="text-align: left; margin: 0; padding-left: 20px;">@foreach ($errors->all() as $error)<li>{{ addslashes($error) }}</li>@endforeach</ul>'
            });
        @endif
    });
</script>
