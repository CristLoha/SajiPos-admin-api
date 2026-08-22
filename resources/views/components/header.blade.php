<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
        <span class="navbar-brand-text d-none d-sm-inline-block" style="font-weight: 700; font-size: 1.1rem; color: var(--sp-primary, #3949AB); letter-spacing: -0.3px;">
            SajiPOS
        </span>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown"><a href="#" data-toggle="dropdown"
                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <div class="user-avatar-sm d-inline-flex align-items-center justify-content-center rounded-circle mr-1"
                    style="width: 34px; height: 34px; background: linear-gradient(135deg, var(--sp-primary, #3949AB), var(--sp-primary-dark, #283593)); color: #fff; font-weight: 600; font-size: 14px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="d-sm-none d-lg-inline-block" style="text-transform: none;">Hi, {{ auth()->user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile.edit') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item has-icon text-danger"
                    onclick="event.preventDefault(); $('#logoutModal').modal('show');">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>

{{-- Modern Logout Confirmation Modal --}}
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 420px;">
        <div class="modal-content"
            style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-body text-center" style="padding: 40px 32px 32px;">
                {{-- Icon --}}
                <div
                    style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #fee2e2, #fecaca); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-sign-out-alt" style="font-size: 28px; color: #ef4444;"></i>
                </div>

                <h5 style="font-weight: 700; font-size: 20px; color: #1e293b; margin-bottom: 8px;">Keluar dari SajiPOS?
                </h5>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 28px; line-height: 1.6;">
                    Sesi Anda akan diakhiri dan Anda perlu<br>login kembali untuk mengakses dashboard.
                </p>

                {{-- Buttons --}}
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button type="button" class="btn" data-dismiss="modal"
                        style="padding: 10px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; background: #f1f5f9; color: #475569; border: none; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Batal
                    </button>
                    <button type="button" class="btn" onclick="document.getElementById('logout-form').submit();"
                        style="padding: 10px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; box-shadow: 0 4px 12px rgba(239,68,68,0.3); transition: all 0.2s ease;"
                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(239,68,68,0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(239,68,68,0.3)'">
                        <i class="fas fa-sign-out-alt mr-1"></i> Ya, Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #logoutModal .modal-content {
        animation: modalSlideUp 0.3s ease;
    }

    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    #logoutModal.fade .modal-dialog {
        transition: transform 0.3s ease;
    }
</style>
