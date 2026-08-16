@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<!-- Hero / Location Banner -->
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 40px;">
    <p class="text-white opacity-75 small mb-1">👋 Halo, {{ auth()->user()->name }}!</p>
    <h2 class="text-white fw-700 mb-3" style="font-size:1.2rem;">Mau jajan apa hari ini? 🍜</h2>

    <!-- Location Search -->
    <div class="d-flex gap-2">
        <button id="get-location-btn" class="btn btn-sm fw-600 w-100"
                style="background:white;color:#FF6B35;border-radius:12px;padding:10px 16px;">
            <i class="bi bi-geo-alt-fill me-1"></i>
            <span id="location-text">Gunakan Lokasiku</span>
        </button>
    </div>
    <form id="location-form" method="GET" action="{{ route('buyer.home') }}" class="d-none">
        <input type="hidden" name="lat" id="lat-input">
        <input type="hidden" name="lng" id="lng-input">
        <input type="hidden" name="radius" value="5">
    </form>
</div>

<!-- White pill card that overlaps hero -->
<div style="margin-top:-24px; padding:0 12px;">
    <div class="card border-0 p-3 mb-3" style="border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,.1);">
        <!-- Category Quick Filters -->
        <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width:none;">
            <a href="{{ route('buyer.home', array_merge(request()->query(), ['cat' => null])) }}"
               class="btn btn-sm flex-shrink-0 {{ !request('cat') ? 'btn-primary-custom' : 'btn-outline-secondary' }}"
               style="border-radius:20px; font-size:.75rem;">
               Semua
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('buyer.home', array_merge(request()->query(), ['cat' => $cat->slug])) }}"
               class="btn btn-sm flex-shrink-0 {{ request('cat') === $cat->slug ? 'btn-primary-custom' : '' }}"
               style="border-radius:20px; font-size:.75rem; {{ request('cat') !== $cat->slug ? 'border:1.5px solid #E5E7EB;color:#374151;' : '' }}">
                {{ $cat->icon }} {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<div class="px-3">
    <!-- Section Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="section-title mb-0">
            @if($latitude && $longitude)
                📍 Warung Terdekat
            @else
                🏪 Semua Warung
            @endif
        </h3>
        <span class="small text-muted">{{ $shops->total() }} warung</span>
    </div>

    <!-- Shop Cards -->
    @forelse($shops as $shop)
    <a href="{{ route('buyer.shop', $shop->id) }}" class="text-decoration-none">
        <div class="food-card card mb-3">
            <!-- Shop Image -->
            @if($shop->image)
                <img src="{{ Storage::url($shop->image) }}" alt="{{ $shop->name }}" class="shop-card-img">
            @else
                <div class="shop-card-img-placeholder">
                    🏪
                </div>
            @endif

            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-fill">
                        <h4 class="fw-700 mb-1" style="font-size:.95rem; color:#1F2937;">{{ $shop->name }}</h4>
                        <p class="text-muted small mb-2 lh-sm">{{ Str::limit($shop->description, 60) }}</p>
                        <p class="small mb-0" style="color:#6B7280;">
                            <i class="bi bi-geo-alt me-1" style="color:#FF6B35;"></i>{{ $shop->address }}
                        </p>
                    </div>
                    <span class="badge {{ $shop->status === 'active' ? 'badge-success' : 'badge-danger' }} badge-status ms-2">
                        {{ $shop->status === 'active' ? 'Buka' : 'Tutup' }}
                    </span>
                </div>

                <hr class="my-2" style="border-color:#F3F4F6;">

                <div class="d-flex align-items-center justify-content-between">
                    <span class="small text-muted">
                        <i class="bi bi-bag-fill me-1" style="color:#FF6B35;"></i>
                        {{ $shop->activeProducts->count() }} menu tersedia
                    </span>
                    @if(isset($shop->distance_meters))
                    <span class="distance-badge">
                        <i class="bi bi-bicycle me-1"></i>
                        {{ number_format($shop->distance_meters / 1000, 1) }} km
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </a>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">🍽️</div>
        <h5 class="fw-600 mt-3 text-muted">Belum ada warung tersedia</h5>
        <p class="text-muted small">Coba perluas radius pencarianmu</p>
        <a href="{{ route('buyer.home') }}" class="btn btn-primary-custom px-4">Tampilkan Semua</a>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($shops->hasPages())
    <div class="d-flex justify-content-center my-3">
        {{ $shops->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('get-location-btn').addEventListener('click', function () {
        const btn  = this;
        const text = document.getElementById('location-text');

        if (!navigator.geolocation) {
            text.textContent = 'GPS tidak didukung';
            return;
        }

        text.textContent = 'Mendeteksi lokasi...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            pos => {
                document.getElementById('lat-input').value = pos.coords.latitude;
                document.getElementById('lng-input').value = pos.coords.longitude;
                document.getElementById('location-form').submit();
            },
            err => {
                text.textContent = 'Gagal mendapat lokasi';
                btn.disabled = false;
            },
            { timeout: 10000 }
        );
    });

    // Show active location if already set
    @if($latitude && $longitude)
        document.getElementById('location-text').textContent = '📍 Lokasi Aktif ({{ number_format($latitude, 4) }}, {{ number_format($longitude, 4) }})';
    @endif
</script>
@endpush
