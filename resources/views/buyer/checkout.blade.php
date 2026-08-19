@extends('layouts.app')
@section('title', 'Checkout')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #checkout-map {
        height: 200px;
        width: 100%;
        border-radius: 14px;
        z-index: 1;
    }
</style>
@endpush

@section('content')
{{-- ── Header ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 44px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">
        <i class="bi bi-credit-card-fill me-2" style="opacity:.9;"></i>Checkout
    </h1>
    <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.75);">Konfirmasi pesananmu</p>
</div>

<div style="margin-top:-20px;padding:0 14px;">
    <form method="POST" action="{{ route('buyer.checkout.process') }}" enctype="multipart/form-data">
        @csrf

        <!-- Delivery Type -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-3" style="color:#1F2937;">🚀 Metode Pengiriman</h6>
            <div class="d-flex gap-2">
                <label class="delivery-option flex-fill text-center p-3 rounded-3"
                       style="border:2px solid #FF6B35;background:#FFF7F5;cursor:pointer;" for="delivery_delivery">
                    <input type="radio" name="delivery_type" id="delivery_delivery" value="delivery"
                           class="d-none" {{ old('delivery_type', 'delivery') === 'delivery' ? 'checked' : '' }}>
                    <div style="font-size:1.5rem;">🛵</div>
                    <div class="small fw-600 mt-1">Antar ke Lokasi</div>
                </label>
                <label class="delivery-option flex-fill text-center p-3 rounded-3"
                       style="border:2px solid #E5E7EB;cursor:pointer;" for="delivery_pickup">
                    <input type="radio" name="delivery_type" id="delivery_pickup" value="pickup"
                           class="d-none" {{ old('delivery_type') === 'pickup' ? 'checked' : '' }}>
                    <div style="font-size:1.5rem;">🏃</div>
                    <div class="small fw-600 mt-1">Ambil Sendiri</div>
                </label>
            </div>
        </div>

        <!-- Delivery Address & Map Pinpoint -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);" id="address-section">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-700 mb-0" style="color:#1F2937;">📍 Titik Lokasi Pengiriman</h6>
                <button type="button" id="btn-detect-gps" class="btn btn-sm text-white fw-600 px-3 py-1"
                        style="background:#FF6B35;border-radius:10px;font-size:.72rem;">
                    <i class="bi bi-geo-alt-fill me-1"></i>Deteksi GPS Saya
                </button>
            </div>

            {{-- Leaflet Map Picker Container --}}
            <div id="checkout-map" class="mb-2 shadow-sm border"></div>
            <p class="small text-muted mb-2" style="font-size:.7rem;">
                💡 <em>Geser pin / ketuk peta untuk menyesuaikan titik pengantaran.</em>
            </p>

            <textarea name="delivery_address" id="delivery_address" rows="2"
                      class="form-control @error('delivery_address') is-invalid @enderror"
                      placeholder="Detail Alamat (Nomor rumah, patokan, gedung)...">{{ old('delivery_address') }}</textarea>
            @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror

            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '-6.200000') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '106.816666') }}">
        </div>

        <!-- Notes -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-2" style="color:#1F2937;">📝 Catatan (Opsional)</h6>
            <input type="text" name="notes" class="form-control" placeholder="Contoh: Pedasnya dikurangi ya..." value="{{ old('notes') }}">
        </div>

        <!-- Payment Method -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-3" style="color:#1F2937;">💳 Metode Pembayaran</h6>
            <div class="d-flex flex-column gap-2">
                @foreach(['transfer' => ['🏦', 'Transfer Bank'], 'cash' => ['💵', 'Bayar Tunai'], 'qris' => ['📱', 'QRIS']] as $val => $info)
                <label class="payment-option d-flex align-items-center gap-3 p-3 rounded-3"
                       style="border:1.5px solid #E5E7EB;cursor:pointer;transition:all .2s;" for="pay_{{ $val }}">
                    <input type="radio" name="payment_method" id="pay_{{ $val }}" value="{{ $val }}"
                           class="form-check-input m-0" {{ old('payment_method', 'transfer') === $val ? 'checked' : '' }}>
                    <span style="font-size:1.3rem;">{{ $info[0] }}</span>
                    <span class="fw-600 small">{{ $info[1] }}</span>
                </label>
                @endforeach
            </div>

            <!-- Mock Midtrans Button -->
            <div class="mt-3 p-3 rounded-3" style="background:#F0FFF4;border:1px dashed #10B981;">
                <p class="small fw-600 mb-1" style="color:#065F46;">🔒 Bayar dengan Midtrans (Simulasi)</p>
                <p class="small text-muted mb-0">Integrasi Midtrans Snap siap dihubungkan. Pilih transfer untuk upload bukti manual.</p>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-3" style="color:#1F2937;">🧾 Ringkasan Pesanan</h6>
            @foreach($items as $item)
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                <span class="fw-600">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="d-flex justify-content-between small mb-1 mt-1">
                <span class="text-muted">Ongkos Kirim</span>
                <span class="fw-600 text-muted" id="delivery-fee-display">Rp 0</span>
                <input type="hidden" name="delivery_fee" id="delivery-fee-input" value="0">
            </div>
            <hr style="border-color:#F3F4F6;">
            <div class="d-flex justify-content-between fw-700">
                <span>Total Bayar</span>
                <span style="color:#FF6B35; font-size:1.05rem;">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-700 mb-3">
            <i class="bi bi-bag-check-fill me-2"></i>Buat Pesanan
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map, marker;
    const defaultLat = parseFloat(document.getElementById('latitude').value) || -6.200000;
    const defaultLng = parseFloat(document.getElementById('longitude').value) || 106.816666;

    function initMap() {
        if (map) return;
        map = L.map('checkout-map').setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateCoords(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        reverseGeocode(lat, lng);
    }

    async function reverseGeocode(lat, lng) {
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await res.json();
            if (data && data.display_name) {
                const addrInput = document.getElementById('delivery_address');
                if (!addrInput.value.trim() || addrInput.dataset.autofilled === 'true') {
                    addrInput.value = data.display_name;
                    addrInput.dataset.autofilled = 'true';
                }
            }
        } catch(err) {
            console.error("Geocoding failed", err);
        }
    }

    // Auto-detect GPS button
    document.getElementById('btn-detect-gps')?.addEventListener('click', function() {
        if ('geolocation' in navigator) {
            this.disabled = true;
            this.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Mencari GPS...`;

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoords(lat, lng);
                    this.disabled = false;
                    this.innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>Deteksi GPS Saya`;
                },
                (err) => {
                    alert('Gagal mendeteksi lokasi GPS. Pastikan izin lokasi aktif.');
                    this.disabled = false;
                    this.innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>Deteksi GPS Saya`;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
        }
    });

    function updateDeliveryState(type) {
        const isDelivery = type === 'delivery';
        const addressSection = document.getElementById('address-section');
        const addressInput = document.getElementById('delivery_address');

        if (addressSection) {
            addressSection.style.display = isDelivery ? 'block' : 'none';
        }

        if (addressInput) {
            addressInput.required = isDelivery;
        }

        document.querySelectorAll('.delivery-option').forEach(l => {
            l.style.borderColor = '#E5E7EB';
            l.style.background  = 'white';
        });

        const selectedRadio = document.querySelector(`[name="delivery_type"][value="${type}"]`);
        if (selectedRadio) {
            const label = selectedRadio.closest('.delivery-option');
            if (label) {
                label.style.borderColor = '#FF6B35';
                label.style.background  = '#FFF7F5';
            }
        }

        const fee = isDelivery ? 3000 : 0;
        const feeDisplay = document.getElementById('delivery-fee-display');
        const feeInput = document.getElementById('delivery-fee-input');
        if (feeDisplay) feeDisplay.textContent = 'Rp ' + fee.toLocaleString('id-ID');
        if (feeInput) feeInput.value = fee;

        if (isDelivery) {
            setTimeout(() => {
                initMap();
                if (map) map.invalidateSize();
            }, 200);
        }
    }

    document.querySelectorAll('[name="delivery_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateDeliveryState(this.value);
        });
    });

    document.querySelectorAll('[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-option').forEach(l => l.style.borderColor = '#E5E7EB');
            this.closest('.payment-option').style.borderColor = '#FF6B35';
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const checkedDelivery = document.querySelector('[name="delivery_type"]:checked')?.value || 'delivery';
        updateDeliveryState(checkedDelivery);

        const checkedPayment = document.querySelector('[name="payment_method"]:checked');
        if (checkedPayment) {
            checkedPayment.closest('.payment-option')?.style.setProperty('border-color', '#FF6B35');
        }
    });
</script>
@endpush
