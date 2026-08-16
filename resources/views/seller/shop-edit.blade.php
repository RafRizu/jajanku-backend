@extends('layouts.app')
@section('title', 'Profil Warung')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-3" style="font-size:1.1rem; color:#1F2937;">
        <i class="bi bi-shop me-2" style="color:#FF6B35;"></i>Profil Warung
    </h2>

    <form method="POST" action="{{ route('seller.shop.update') }}" enctype="multipart/form-data">
        @csrf

        <!-- Shop Image -->
        <div class="card border-0 mb-3 p-3 text-center" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            @if($shop && $shop->image)
                <img src="{{ Storage::url($shop->image) }}" alt="Shop image"
                     class="rounded-4 mb-2 mx-auto" style="width:100px;height:100px;object-fit:cover;">
            @else
                <div class="mx-auto mb-2 rounded-4 d-flex align-items-center justify-content-center"
                     style="width:100px;height:100px;background:linear-gradient(135deg,#FFE0CC,#FFD4B5);font-size:2.5rem;">
                    🏪
                </div>
            @endif
            <label class="btn btn-sm rounded-pill px-3" style="border:1.5px solid #FF6B35;color:#FF6B35;" for="shop-image">
                <i class="bi bi-camera me-1"></i>Ganti Foto
            </label>
            <input type="file" name="image" id="shop-image" class="d-none" accept="image/*">
        </div>

        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div class="mb-3">
                <label class="form-label fw-600 small text-secondary">Nama Warung *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $shop?->name) }}" placeholder="Mis. Warung Bu Siti" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-600 small text-secondary">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Ceritakan tentang warungmu...">{{ old('description', $shop?->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-600 small text-secondary">Alamat Warung *</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2"
                          placeholder="Mis. Jl. Kampus No. 1, Depan Gedung A" required>{{ old('address', $shop?->address) }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Location Coordinates -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-1" style="color:#1F2937;">📍 Koordinat Lokasi</h6>
            <p class="text-muted small mb-3">Koordinat digunakan untuk fitur "Warung Terdekat"</p>

            <button type="button" id="detect-location" class="btn btn-sm w-100 mb-3 rounded-3"
                    style="border:1.5px dashed #FF6B35;color:#FF6B35;padding:10px;">
                <i class="bi bi-geo-alt-fill me-1"></i>Deteksi Lokasi Otomatis
            </button>

            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label fw-600 small text-secondary">Latitude</label>
                    <input type="number" name="latitude" id="latitude" step="any"
                           class="form-control form-control-sm"
                           value="{{ old('latitude', $shop?->latitude) }}" placeholder="-6.9147">
                </div>
                <div class="col-6">
                    <label class="form-label fw-600 small text-secondary">Longitude</label>
                    <input type="number" name="longitude" id="longitude" step="any"
                           class="form-control form-control-sm"
                           value="{{ old('longitude', $shop?->longitude) }}" placeholder="107.6098">
                </div>
            </div>

            @if($shop && $shop->latitude)
            <div class="mt-2 p-2 rounded-3" style="background:#F0FFF4;font-size:.75rem;color:#065F46;">
                <i class="bi bi-check-circle-fill me-1"></i>
                Koordinat tersimpan: {{ $shop->latitude }}, {{ $shop->longitude }}
            </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-700">
            <i class="bi bi-check2-all me-2"></i>Simpan Profil Warung
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
                btn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Deteksi Lokasi Otomatis';
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
