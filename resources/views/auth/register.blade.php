@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="p-4">
    <div class="text-center mb-4 pt-2">
        <div style="font-size:2.5rem;">🍱</div>
        <h1 style="font-weight:800; font-size:1.5rem; color:#2D3748;">Buat Akun Baru</h1>
        <p class="text-muted small">Bergabung dan nikmati kemudahan pesan makan</p>
    </div>

    <div class="card border-0" style="border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,.1);">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-600 small text-secondary">Nama Lengkap</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Nama kamu" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-secondary">Email</label>
                    <input type="email" name="email" id="reg-email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="kamu@kampus.ac.id" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-secondary">No. HP (opsional)</label>
                    <input type="tel" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-secondary">Daftar Sebagai</label>
                    <div class="d-flex gap-2">
                        @foreach(['buyer' => ['🛒', 'Pembeli'], 'seller' => ['🏪', 'Penjual'], 'driver' => ['🚴', 'Driver']] as $val => $info)
                        <label class="role-option flex-fill text-center p-2 rounded-3 border-2 {{ old('role') === $val ? 'selected' : '' }}"
                               style="cursor:pointer; border:2px solid #E5E7EB; transition:all .2s;"
                               for="role_{{ $val }}">
                            <input type="radio" name="role" id="role_{{ $val }}" value="{{ $val }}"
                                   class="d-none" {{ old('role') === $val ? 'checked' : '' }}>
                            <div style="font-size:1.5rem;">{{ $info[0] }}</div>
                            <div class="small fw-600 mt-1">{{ $info[1] }}</div>
                        </label>
                        @endforeach
                    </div>
                    @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600 small text-secondary">Password</label>
                    <input type="password" name="password" id="reg-password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 8 karakter" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-600 small text-secondary">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-600">
                    <i class="bi bi-person-plus-fill me-2"></i>Daftar Sekarang
                </button>
            </form>
        </div>
    </div>

    <p class="text-center mt-4 text-muted small">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color:#FF6B35; font-weight:600;">Masuk</a>
    </p>
</div>
@endsection

@push('styles')
<style>
    .role-option.selected { border-color: #FF6B35 !important; background: #FFF7F5; }
    .role-option input:checked ~ div { color: #FF6B35; }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.role-option').forEach(label => {
        label.addEventListener('click', () => {
            document.querySelectorAll('.role-option').forEach(l => l.classList.remove('selected'));
            label.classList.add('selected');
            label.querySelector('input').checked = true;
        });
    });
</script>
@endpush
