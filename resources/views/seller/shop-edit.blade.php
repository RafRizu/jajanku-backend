@extends('layouts.app')
@section('title', 'Profil Warung')

@section('content')
{{-- ── Header ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 44px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">
        <i class="bi bi-shop me-2" style="opacity:.9;"></i>Profil Warung Bu Ipa
    </h1>
    <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.75);">Pengaturan nama warung, deskripsi, dan alamat</p>
</div>

<div style="margin-top:-20px;padding:0 14px;">
    <form method="POST" action="{{ route('seller.shop.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- Shop Image -->
        <div class="card border-0 mb-3 p-3 text-center" style="border-radius:16px; box-shadow:0 3px 12px rgba(0,0,0,.07);">
            <p class="fw-700 small mb-2 text-start" style="color:#1F2937;">📷 Foto Sampul Warung</p>
            @if($shop && $shop->image)
                <img src="{{ Storage::url($shop->image) }}" alt="Shop image"
                     class="rounded-4 mb-2 mx-auto" style="width:100px;height:100px;object-fit:cover;">
            @else
                <div class="mx-auto mb-2 rounded-4 d-flex align-items-center justify-content-center"
                     style="width:100px;height:100px;background:linear-gradient(135deg,#FFE0CC,#FFD4B5);font-size:2.5rem;">
                    🏪
                </div>
            @endif
            <label class="btn btn-sm rounded-pill px-4 py-2 mx-auto" style="border:1.5px solid #FF6B35;color:#FF6B35;font-weight:600;" for="shop-image">
                <i class="bi bi-camera me-1"></i>Pilih Foto Warung
            </label>
            <input type="file" name="image" id="shop-image" class="d-none" accept="image/*">
        </div>

        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 3px 12px rgba(0,0,0,.07);">
            <div class="mb-3">
                <label class="form-label fw-700 small text-dark">Nama Warung *</label>
                <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                       style="border-radius:12px;font-size:.95rem;"
                       value="{{ old('name', $shop?->name ?? 'Warung Bu Ipa') }}" placeholder="Mis. Warung Bu Ipa" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-700 small text-dark">Slogan / Deskripsi Warung</label>
                <textarea name="description" class="form-control" rows="2" style="border-radius:12px;"
                          placeholder="Temukan jajanan SD mu disini!">{{ old('description', $shop?->description ?? 'Temukan jajanan SD mu disini!') }}</textarea>
            </div>

            <div class="mb-2">
                <label class="form-label fw-700 small text-dark">Alamat Lengkap Warung *</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" style="border-radius:12px;"
                          placeholder="Mis. Jl. Kampus No. 1, Area Kantin" required>{{ old('address', $shop?->address ?? 'Jl. Kampus No. 1, Area Kantin') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Location Coordinates -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 3px 12px rgba(0,0,0,.07);">
            <h6 class="fw-700 mb-1" style="color:#1F2937;">📍 Posisi GPS Warung (Opsional)</h6>
            <p class="text-muted small mb-3">Tekan tombol di bawah jika ingin mengisi lokasi GPS otomatis.</p>

            <button type="button" id="detect-location" class="btn btn-sm w-100 mb-3 rounded-3 py-2 fw-600"
                    style="border:1.5px dashed #FF6B35;color:#FF6B35;background:#FFF7F5;">
                <i class="bi bi-geo-alt-fill me-1"></i>Deteksi Lokasi GPS Otomatis
            </button>

            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label fw-600 small text-secondary">Latitude</label>
                    <input type="number" name="latitude" id="latitude" step="any"
                           class="form-control form-control-sm" style="border-radius:8px;"
                           value="{{ old('latitude', $shop?->latitude) }}" placeholder="-6.9147">
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 small text-secondary">Longitude</label>
                    <input type="number" name="longitude" id="longitude" step="any"
                           class="form-control form-control-sm" style="border-radius:8px;"
                           value="{{ old('longitude', $shop?->longitude) }}" placeholder="107.6098">
                </div>
            </div>

            @if($shop && $shop->latitude)
            <div class="mt-2 p-2 rounded-3" style="background:#F0FFF4;font-size:.75rem;color:#065F46;">
                <i class="bi bi-check-circle-fill me-1"></i>
                GPS tersimpan: {{ $shop->latitude }}, {{ $shop->longitude }}
            </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-700 mb-3" style="font-size:1.05rem;border-radius:14px;">
            💾 SIMPAN PROFIL WARUNG
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('detect-location').addEventListener('click', function() {
        const btn = this;
        btn.textContent = '⏳ Mendeteksi...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            pos => {
                document.getElementById('latitude').value  = pos.coords.latitude.toFixed(7);
                document.getElementById('longitude').value = pos.coords.longitude.toFixed(7);
                btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Lokasi Terdeteksi!';
                btn.style.background = '#D1FAE5';
                btn.style.color      = '#065F46';
                btn.style.border     = '1.5px solid #10B981';
                btn.disabled = false;
            },
            err => {
                btn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Deteksi Lokasi GPS Otomatis';
                btn.disabled = false;
            }
        );
    });

    // Preview shop image
    document.getElementById('shop-image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            const img = document.querySelector('.rounded-4');
            if (img && img.tagName === 'IMG') {
                img.src = url;
            }
        }
    });
</script>
@endpush
