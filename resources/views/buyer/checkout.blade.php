@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-3" style="font-size:1.1rem; color:#1F2937;">
        <i class="bi bi-credit-card-fill me-2" style="color:#FF6B35;"></i>Checkout
    </h2>

    <form method="POST" action="{{ route('buyer.checkout.process') }}" enctype="multipart/form-data">
        @csrf

        <!-- Delivery Type -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-3" style="color:#1F2937;">🚀 Metode Pengiriman</h6>
            <div class="d-flex gap-2">
                <label class="delivery-option flex-fill text-center p-3 rounded-3"
                       style="border:2px solid #FF6B35;background:#FFF7F5;cursor:pointer;" for="delivery_delivery">
                    <input type="radio" name="delivery_type" id="delivery_delivery" value="delivery"
                           class="d-none" checked>
                    <div style="font-size:1.5rem;">🛵</div>
                    <div class="small fw-600 mt-1">Antar ke Lokasi</div>
                </label>
                <label class="delivery-option flex-fill text-center p-3 rounded-3"
                       style="border:2px solid #E5E7EB;cursor:pointer;" for="delivery_pickup">
                    <input type="radio" name="delivery_type" id="delivery_pickup" value="pickup"
                           class="d-none">
                    <div style="font-size:1.5rem;">🏃</div>
                    <div class="small fw-600 mt-1">Ambil Sendiri</div>
                </label>
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);" id="address-section">
            <h6 class="fw-700 mb-2" style="color:#1F2937;">📍 Alamat Pengiriman</h6>
            <textarea name="delivery_address" id="delivery_address" rows="3"
                      class="form-control @error('delivery_address') is-invalid @enderror"
                      placeholder="Contoh: Gedung A Lantai 2, Kampus Utama">{{ old('delivery_address') }}</textarea>
            @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Notes -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-2" style="color:#1F2937;">📝 Catatan (Opsional)</h6>
            <input type="text" name="notes" class="form-control" placeholder="Contoh: Pedasnya dikurangi ya...">
        </div>

        <!-- Payment Method -->
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h6 class="fw-700 mb-3" style="color:#1F2937;">💳 Metode Pembayaran</h6>
            <div class="d-flex flex-column gap-2">
                @foreach(['transfer' => ['🏦', 'Transfer Bank'], 'cash' => ['💵', 'Bayar Tunai'], 'qris' => ['📱', 'QRIS']] as $val => $info)
                <label class="payment-option d-flex align-items-center gap-3 p-3 rounded-3"
                       style="border:1.5px solid #E5E7EB;cursor:pointer;transition:all .2s;" for="pay_{{ $val }}">
                    <input type="radio" name="payment_method" id="pay_{{ $val }}" value="{{ $val }}"
                           class="form-check-input m-0" {{ $val === 'transfer' ? 'checked' : '' }}>
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

        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-700">
            <i class="bi bi-bag-check-fill me-2"></i>Buat Pesanan
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Delivery type toggle
    document.querySelectorAll('[name="delivery_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isDelivery = this.value === 'delivery';
            document.getElementById('address-section').style.display = isDelivery ? 'block' : 'none';
            document.getElementById('delivery_address').required = isDelivery;

            // Update delivery options styling
            document.querySelectorAll('.delivery-option').forEach(l => {
                l.style.borderColor = '#E5E7EB';
                l.style.background  = 'white';
            });
            this.closest('.delivery-option').style.borderColor = '#FF6B35';
            this.closest('.delivery-option').style.background  = '#FFF7F5';

            // Simulate delivery fee
            const fee = isDelivery ? 3000 : 0;
            document.getElementById('delivery-fee-display').textContent = 'Rp ' + fee.toLocaleString('id-ID');
            document.getElementById('delivery-fee-input').value = fee;
        });
    });

    // Payment method styling
    document.querySelectorAll('[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-option').forEach(l => l.style.borderColor = '#E5E7EB');
            this.closest('.payment-option').style.borderColor = '#FF6B35';
        });
    });

    // Initial state
    document.querySelector('[name="payment_method"]:checked')?.closest('.payment-option')
        ?.style.setProperty('border-color', '#FF6B35');
</script>
@endpush
