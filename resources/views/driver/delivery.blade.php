@extends('layouts.app')
@section('title', 'Pengantaran #' . $order->id)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #driver-delivery-map {
        height: 220px;
        width: 100%;
        border-radius: 16px;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="p-3">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('driver.jobs') }}" class="btn p-1" style="color:#374151;">
            <i class="bi bi-arrow-left" style="font-size:1.2rem;"></i>
        </a>
        <h2 class="fw-700 mb-0" style="font-size:1rem; color:#1F2937;">Pengantaran #{{ $order->id }}</h2>
    </div>

    <!-- Progress -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; background:linear-gradient(135deg,#FF6B35,#FF8C42); color:white;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size:2.5rem;">🛵</div>
                <div>
                    <p class="mb-0 fw-700" style="font-size:1rem;">Sedang Dalam Pengiriman</p>
                    <p class="mb-0 opacity-75 small">Pesanan untuk {{ $order->buyer->name }}</p>
                </div>
            </div>
            <span id="gps-status-badge" class="badge bg-white text-dark small px-2 py-1" style="font-size:.68rem;">
                🟢 GPS On
            </span>
        </div>
    </div>

    <!-- Map & Google Maps Route Button -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-700 mb-0" style="color:#1F2937;">🗺️ Rute Pengantaran</h6>
            @php
                $destLat = $order->latitude ?? -6.205000;
                $destLng = $order->longitude ?? 106.820000;
                $gmapsUrl = "https://www.google.com/maps/dir/?api=1&destination={$destLat},{$destLng}";
            @endphp
            <a href="{{ $gmapsUrl }}" target="_blank" class="btn btn-sm text-white fw-600 px-2 py-1"
               style="background:#2563EB;border-radius:10px;font-size:.72rem;">
                <i class="bi bi-geo-fill me-1"></i>Buka di Google Maps
            </a>
        </div>
        <div id="driver-delivery-map" class="shadow-sm border mb-2"></div>
    </div>

    <!-- Pickup Info -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #FF6B35;">
        <div class="d-flex align-items-start gap-3">
            <div class="text-center" style="min-width:32px;">
                <i class="bi bi-shop-window" style="font-size:1.4rem;color:#FF6B35;"></i>
                <div style="width:2px;height:20px;background:#E5E7EB;margin:4px auto;"></div>
                <i class="bi bi-house-check-fill" style="font-size:1.4rem;color:#10B981;"></i>
            </div>
            <div class="flex-fill">
                <div class="mb-3">
                    <p class="fw-700 mb-0 small" style="color:#FF6B35;">JEMPUT DI</p>
                    <p class="fw-600 mb-0">{{ $order->shop->name }}</p>
                    <p class="text-muted small mb-0">{{ $order->shop->address }}</p>
                </div>
                <div>
                    <p class="fw-700 mb-0 small" style="color:#10B981;">ANTAR KE</p>
                    <p class="fw-600 mb-0">{{ $order->buyer->name }}</p>
                    <p class="text-muted small mb-0">{{ $order->delivery_address ?? 'Ambil sendiri' }}</p>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-telephone me-1"></i>{{ $order->buyer->phone ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h6 class="fw-700 mb-2" style="color:#1F2937;">🛍️ Isi Pesanan</h6>
        @foreach($order->items as $item)
        <div class="d-flex justify-content-between small py-1 border-bottom" style="border-color:#F3F4F6!important;">
            <span class="text-muted">{{ $item->product->name ?? '-' }} × {{ $item->quantity }}</span>
            <span class="fw-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="d-flex justify-content-between fw-700 mt-2">
            <span>Total</span>
            <span style="color:#FF6B35;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        @if($order->notes)
        <div class="mt-2 p-2 rounded-3" style="background:#FFF7F5;font-size:.8rem;color:#92400E;">
            📝 {{ $order->notes }}
        </div>
        @endif
    </div>

    <!-- Payment Info -->
    @if($order->payment)
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="d-flex justify-content-between align-items-center">
            <span class="small fw-600 text-muted">Metode Bayar:</span>
            <span class="small fw-700 text-capitalize">{{ $order->payment->method }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="small fw-600 text-muted">Status Bayar:</span>
            <span class="badge badge-status {{ $order->payment->status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                {{ $order->payment->status === 'paid' ? '✅ Lunas' : '⏳ Belum Lunas' }}
            </span>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('driver.delivery.pickup', $order->id) }}" class="flex-fill">
            @csrf
            <button type="submit" class="btn w-100 fw-600 rounded-3"
                    style="background:#EDE9FE;color:#5B21B6;border:none;padding:12px;font-size:.85rem;">
                <i class="bi bi-bag-check me-1"></i>Ambil Pesanan
            </button>
        </form>
        <form method="POST" action="{{ route('driver.delivery.delivered', $order->id) }}" class="flex-fill">
            @csrf
            <button type="submit" class="btn btn-primary-custom w-100 fw-700"
                    style="padding:12px;font-size:.85rem;"
                    onclick="return confirm('Konfirmasi pesanan sudah diantar?')">
                <i class="bi bi-check2-all me-1"></i>Selesai Antar
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const shopCoords  = [{{ $order->shop->latitude ?? -6.200000 }}, {{ $order->shop->longitude ?? 106.816666 }}];
    const buyerCoords = [{{ $order->latitude ?? -6.205000 }}, {{ $order->longitude ?? 106.820000 }}];
    let driverCoords  = [{{ $order->driver_latitude ?? -6.200000 }}, {{ $order->driver_longitude ?? 106.816666 }}];

    let driverMap, driverMarker;

    function initDriverMap() {
        driverMap = L.map('driver-delivery-map').setView(driverCoords, 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(driverMap);

        const createIcon = (emoji, bg) => L.divIcon({
            html: `<div style="background:${bg};color:white;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 3px 8px rgba(0,0,0,.3);border:2px solid white;">${emoji}</div>`,
            className: '',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        L.marker(shopCoords, { icon: createIcon('🏪', '#10B981') }).addTo(driverMap).bindPopup('Warung');
        L.marker(buyerCoords, { icon: createIcon('🏠', '#EF4444') }).addTo(driverMap).bindPopup('Tujuan Pembeli');
        driverMarker = L.marker(driverCoords, { icon: createIcon('🛵', '#FF6B35') }).addTo(driverMap).bindPopup('Posisi Saya');

        L.polyline([shopCoords, buyerCoords], { color: '#FF6B35', weight: 3, dashArray: '6, 6' }).addTo(driverMap);
        driverMap.fitBounds(L.latLngBounds([shopCoords, buyerCoords, driverCoords]), { padding: [30, 30] });
    }

    // Send driver GPS location to server periodically
    function sendDriverLocation(lat, lng) {
        fetch('{{ route("driver.delivery.location", $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ latitude: lat, longitude: lng })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('[GPS] Location broadcasted:', lat, lng);
            }
        })
        .catch(err => console.error('[GPS] Location broadcast error:', err));
    }

    function startLocationTracking() {
        if ('geolocation' in navigator) {
            navigator.geolocation.watchPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    driverCoords = [lat, lng];
                    if (driverMarker) driverMarker.setLatLng(driverCoords);
                    sendDriverLocation(lat, lng);
                },
                (err) => {
                    console.warn('[GPS] Watch position error:', err);
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 5000 }
            );
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initDriverMap();
        startLocationTracking();
    });
</script>
@endpush
