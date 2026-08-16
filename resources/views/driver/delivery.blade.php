@extends('layouts.app')
@section('title', 'Pengantaran #' . $order->id)

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
        <div class="d-flex align-items-center gap-3">
            <div style="font-size:2.5rem;">🛵</div>
            <div>
                <p class="mb-0 fw-700" style="font-size:1rem;">Sedang Dalam Pengiriman</p>
                <p class="mb-0 opacity-75 small">Pesanan untuk {{ $order->buyer->name }}</p>
            </div>
        </div>
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
