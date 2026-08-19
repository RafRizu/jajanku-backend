@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')
{{-- ── Header ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 44px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">
        <i class="bi bi-bag-fill me-2" style="opacity:.9;"></i>Keranjangku
    </h1>
    <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.75);">Item yang akan kamu pesan</p>
</div>

<div style="margin-top:-20px;padding:0 14px;">

    @if(empty($items))
    <div class="text-center py-5">
        <div style="font-size:5rem;">🛒</div>
        <h4 class="fw-600 mt-3 text-muted">Keranjangmu kosong</h4>
        <p class="text-muted small">Yuk cari makanan yang kamu suka!</p>
        <a href="{{ route('buyer.home') }}" class="btn btn-primary-custom px-4 mt-2">Cari Makanan</a>
    </div>
    @else
    <!-- Cart Items -->
    @foreach($items as $productId => $item)
    <div class="card border-0 mb-2 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="d-flex gap-3 align-items-center">
            <div style="width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#FFE0CC,#FFD4B5);
                        display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                🍽️
            </div>
            <div class="flex-fill">
                <h6 class="fw-600 mb-1" style="font-size:.85rem;">{{ $item['name'] }}</h6>
                <span class="price-tag" style="font-size:.85rem;">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="qty-btn minus-btn btn p-0 d-flex align-items-center justify-content-center"
                        style="width:28px;height:28px;border-radius:8px;background:#F3F4F6;border:none;"
                        data-product-id="{{ $productId }}" data-action="minus">
                    <i class="bi bi-dash fw-700"></i>
                </button>
                <span class="fw-700 qty-display" style="min-width:20px;text-align:center;" data-product-id="{{ $productId }}">
                    {{ $item['quantity'] }}
                </span>
                <button class="qty-btn plus-btn btn p-0 d-flex align-items-center justify-content-center"
                        style="width:28px;height:28px;border-radius:8px;background:#FF6B35;border:none;"
                        data-product-id="{{ $productId }}" data-action="plus">
                    <i class="bi bi-plus fw-700 text-white"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Order Summary -->
    <div class="card border-0 mt-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h6 class="fw-700 mb-3" style="color:#1F2937;">Ringkasan Pesanan</h6>
        @foreach($items as $item)
        <div class="d-flex justify-content-between small mb-1">
            <span class="text-muted">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
            <span class="fw-600">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
        </div>
        @endforeach
        <hr style="border-color:#F3F4F6;">
        <div class="d-flex justify-content-between fw-700">
            <span>Total</span>
            <span id="cart-total" style="color:#FF6B35;">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Checkout Button -->
    <a href="{{ route('buyer.checkout') }}" class="btn btn-primary-custom w-100 mt-3 py-3">
        <i class="bi bi-credit-card-fill me-2"></i>Lanjut ke Pembayaran
    </a>
    @endif
</div>
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const productId = this.dataset.productId;
        const action    = this.dataset.action;
        const display   = document.querySelector(`.qty-display[data-product-id="${productId}"]`);
        let qty = parseInt(display.textContent.trim());

        if (action === 'plus') qty++;
        else qty--;

        try {
            const res = await fetch('{{ route("buyer.cart.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: qty })
            });
            const data = await res.json();
            if (data.success) {
                if (qty <= 0) {
                    // Remove row
                    btn.closest('.card').remove();
                } else {
                    display.textContent = qty;
                }
                // Update total display
                document.getElementById('cart-total').textContent =
                    'Rp ' + parseInt(data.total).toLocaleString('id-ID');
            } else if (data.message) {
                alert(data.message);
            }
        } catch (e) { console.error(e); }
    });
});
</script>
@endpush
