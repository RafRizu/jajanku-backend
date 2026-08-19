@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: calc(100vh - 60px); padding: 24px;">

    <!-- Logo Area -->
    <div class="text-center mb-4">
        <div style="font-size: 3.5rem; line-height: 1;">🏪</div>
        <h1 style="font-weight:800; font-size:1.8rem; color:#2D3748; letter-spacing:-.5px;" class="mt-2">
           Jajan<span style="color:#FF6B35;">Ku</span>
        </h1>
        <p class="text-muted small">Temukan jajanan SD mu disini! 🍢</p>
    </div>

    <div class="card border-0 w-100" style="border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,.12); max-width:400px;">
        <div class="card-body p-4">
            <h2 class="fw-700 mb-1" style="font-size:1.2rem; color:#2D3748;">Selamat Datang Kembali 👋</h2>
            <p class="text-muted small mb-4">Masuk untuk melanjutkan pesananmu</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-600 small text-secondary">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="kamu@jajanku.id" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small text-secondary">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password"
                                style="border-radius:0 12px 12px 0; border-color:#E5E7EB;">
                            <i class="bi bi-eye-slash" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-secondary" for="remember">Ingat saya</label>
                </div>

                <button type="submit" id="login-btn" class="btn btn-primary-custom w-100 py-3 fw-600">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            {{-- Demo Credentials --}}
            <div class="mt-4 p-3 rounded-3" style="background:#FFF7F5;border:1px solid #FFE8D6;">
                <p class="small fw-700 mb-2" style="color:#FF6B35;">🧪 Akun Demo (klik untuk isi otomatis):</p>
                <div class="d-flex flex-column gap-2">
                    <button class="btn btn-sm text-start py-2 px-3 rounded-3 demo-fill"
                            style="background:#FFF;border:1.5px solid #FFD4B5;font-size:.75rem;"
                            data-email="pembeli@jajanku.id" data-pass="password">
                        🛒 <strong>Pembeli</strong>: pembeli@jajanku.id
                    </button>
                    <button class="btn btn-sm text-start py-2 px-3 rounded-3 demo-fill"
                            style="background:#FFF;border:1.5px solid #FFD4B5;font-size:.75rem;"
                            data-email="pemilik@jajanku.id" data-pass="password">
                        🏪 <strong>Pemilik Warung</strong>: pemilik@jajanku.id
                    </button>
                    <button class="btn btn-sm text-start py-2 px-3 rounded-3 demo-fill"
                            style="background:#FFF;border:1.5px solid #FFD4B5;font-size:.75rem;"
                            data-email="driver@jajanku.id" data-pass="password">
                        🛵 <strong>Driver</strong>: driver@jajanku.id
                    </button>
                    <button class="btn btn-sm text-start py-2 px-3 rounded-3 demo-fill"
                            style="background:#FFF;border:1.5px solid #E5E7EB;font-size:.75rem;"
                            data-email="admin@jajanku.id" data-pass="password">
                        ⚙️ <strong>Admin</strong>: admin@jajanku.id
                    </button>
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size:.7rem;">Password semua: <strong>password</strong></p>
            </div>
        </div>
    </div>

    <p class="mt-4 text-muted small">
        Belum punya akun?
        <a href="{{ route('register') }}" style="color:#FF6B35; font-weight:600;">Daftar sekarang</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('toggle-password').addEventListener('click', function() {
        const pw   = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.className = 'bi bi-eye';
        } else {
            pw.type = 'password';
            icon.className = 'bi bi-eye-slash';
        }
    });

    document.querySelectorAll('.demo-fill').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('email').value    = btn.dataset.email;
            document.getElementById('password').value = btn.dataset.pass;
        });
    });
</script>
@endpush
