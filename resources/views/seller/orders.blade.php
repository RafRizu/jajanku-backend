@extends('layouts.app')
@section('title', 'Pesanan Masuk')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-3" style="font-size:1.1rem; color:#1F2937;">
        <i class="bi bi-receipt-cutoff me-2" style="color:#FF6B35;"></i>Pesanan Masuk
    </h2>

    <!-- Filter Tabs -->
    <div class="d-flex gap-2 overflow-auto pb-2 mb-3" style="scrollbar-width:none;">
        @foreach(['all' => 'Semua', 'pending' => '⏳ Baru', 'confirmed' => '✅ Konfirmasi', 'processing' => '👨‍🍳 Proses', 'on_delivery' => '🛵 Kirim', 'delivered' => '✅ Selesai'] as $status => $label)
        <a href="{{ route('seller.orders', $status !== 'all' ? ['status' => $status] : []) }}"
           class="btn btn-sm flex-shrink-0 rounded-pill {{ request('status', 'all') === $status ? 'btn-primary-custom' : '' }}"
           style="{{ request('status', 'all') !== $status ? 'border:1.5px solid #E5E7EB;color:#374151;' : '' }} font-size:.75rem;">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @forelse($orders as $order)
    <div class="card border-0 mb-3 p-3"
         id="seller-orders-list"
         data-order-id="{{ $order->id }}"
         style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <span class="fw-700 small" style="color:#1F2937;">
                    #{{ $order->id }} — {{ $order->buyer->name }}
                </span>
                <p class="text-muted mb-0" style="font-size:.7rem;">
                    <i class="bi bi-clock me-1"></i>{{ $order->created_at->diffForHumans() }}
                    &bull; {{ ucfirst($order->delivery_type) }}
                </p>
            </div>
            <span class="badge badge-status badge-{{ $order->status_badge }} order-status-badge">
                {{ $order->status_label }}
            </span>
        </div>

        <!-- Items -->
        @foreach($order->items as $item)
        <div class="d-flex justify-content-between small text-muted mb-1">
            <span>{{ $item->product->name ?? '-' }} × {{ $item->quantity }}</span>
            <span class="fw-600 text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach

        <hr class="my-2" style="border-color:#F3F4F6;">

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-700 price-tag">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                @if($order->payment)
                <span class="ms-2 badge badge-status {{ $order->payment->status === 'paid' ? 'badge-success' : 'badge-warning' }}" style="font-size:.65rem;">
                    {{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                </span>
                @endif
            </div>
            @if($order->delivery_address)
            <span class="small text-muted" style="font-size:.7rem; max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                📍 {{ $order->delivery_address }}
            </span>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 mt-2">
            @if($order->status === 'pending')
            <form method="POST" action="{{ route('seller.orders.process', $order->id) }}" class="flex-fill">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary-custom w-100" style="font-size:.8rem;">
                    <i class="bi bi-check2-circle me-1"></i>Konfirmasi
                </button>
            </form>
            @elseif($order->status === 'confirmed')
            <form method="POST" action="{{ route('seller.orders.process', $order->id) }}" class="flex-fill">
                @csrf
                <button type="submit" class="btn btn-sm w-100 fw-600 rounded-3"
                        style="background:#EDE9FE;color:#5B21B6;border:none;font-size:.8rem;">
                    <i class="bi bi-fire me-1"></i>Mulai Proses
                </button>
            </form>
            @elseif($order->status === 'processing')
            <form method="POST" action="{{ route('seller.orders.driver', $order->id) }}" class="flex-fill">
                @csrf
                <button type="submit" class="btn btn-sm w-100 fw-600 rounded-3"
                        style="background:#FFEDD5;color:#C2410C;border:none;font-size:.8rem;">
                    <i class="bi bi-bicycle me-1"></i>Request Driver
                </button>
            </form>
            @elseif($order->status === 'on_delivery')
            <span class="small text-muted flex-fill text-center" style="font-size:.75rem;">
                🛵 Menunggu driver...
                @if($order->driver) | {{ $order->driver->name }} @endif
            </span>
            @elseif($order->status === 'delivered')
            <span class="small flex-fill text-center fw-600" style="color:#065F46;font-size:.75rem;">
                ✅ Pesanan selesai diantar
            </span>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">📭</div>
        <h5 class="fw-600 mt-3 text-muted">Belum ada pesanan</h5>
    </div>
    @endforelse

    @if($orders->hasPages())
        <div class="d-flex justify-content-center">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Dipanggil oleh global Pusher listener di layout saat ada pesanan baru
window.prependNewOrderBadge = function(data) {
    // Tampilkan tombol "Lihat Pesanan Baru" di atas list
    const existing = document.getElementById('new-order-alert-bar');
    if (existing) {
        // Sudah ada banner, tambahkan counter saja
        const counter = existing.querySelector('.new-order-count');
        if (counter) counter.textContent = parseInt(counter.textContent) + 1;
        return;
    }

    const bar = document.createElement('div');
    bar.id = 'new-order-alert-bar';
    bar.style.cssText = 'position:sticky;top:56px;z-index:99;margin:-4px -0px 12px;cursor:pointer;';
    bar.innerHTML = `
        <div style="background:linear-gradient(135deg,#FF6B35,#FF8C42);color:white;padding:10px 16px;
                    border-radius:12px;display:flex;align-items:center;justify-content:space-between;
                    box-shadow:0 4px 16px rgba(255,107,53,.4);font-size:.85rem;font-weight:600;">
            <span>🛒 <span class="new-order-count">1</span> Pesanan baru masuk dari ${data.buyer_name}!</span>
            <span onclick="location.reload()"
                  style="background:rgba(255,255,255,.25);padding:4px 10px;border-radius:8px;font-size:.75rem;">
                Refresh
            </span>
        </div>`;

    const list = document.querySelector('.p-3');
    if (list) list.prepend(bar);
};
</script>
@endpush
