@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
{{-- ── Header ────────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 44px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">
        <i class="bi bi-receipt-cutoff me-2" style="opacity:.9;"></i>Pesanan Saya
    </h1>
    <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.75);">Riwayat & status pesananmu</p>
</div>

<div style="margin-top:-20px;padding:0 14px;">
    @forelse($orders as $order)
    <a href="{{ route('buyer.order.detail', $order->id) }}" class="text-decoration-none">
        <div class="card border-0 mb-3" style="border-radius:16px;box-shadow:0 3px 12px rgba(0,0,0,.07);overflow:hidden;">
            {{-- status color bar --}}
            <div style="height:4px;background:{{ match($order->status) {
                'pending'     => 'linear-gradient(90deg,#F59E0B,#FBBF24)',
                'confirmed'   => 'linear-gradient(90deg,#3B82F6,#60A5FA)',
                'processing'  => 'linear-gradient(90deg,#8B5CF6,#A78BFA)',
                'on_delivery' => 'linear-gradient(90deg,#FF6B35,#FF8C42)',
                'delivered'   => 'linear-gradient(90deg,#10B981,#34D399)',
                default       => '#E5E7EB',
            } }};"></div>

            <div class="p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-700" style="font-size:.9rem;color:#1F2937;">{{ $order->shop->name }}</span>
                        <p class="text-muted mb-0" style="font-size:.7rem;">
                            <i class="bi bi-clock me-1"></i>{{ $order->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="badge badge-status badge-{{ $order->status_badge }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">
                        <i class="bi bi-bag me-1" style="color:#FF6B35;"></i>
                        {{ $order->items->count() ?? 0 }} item
                    </span>
                    <span class="fw-700 price-tag">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </a>
    @empty
    <div class="text-center py-5 mt-2">
        <div style="font-size:4.5rem;">🛍️</div>
        <h2 class="fw-700 mt-3 mb-2" style="font-size:1.1rem;color:#1F2937;">Belum Ada Pesanan</h2>
        <p class="text-muted small mb-4">Yuk, mulai jajan sekarang!</p>
        <a href="{{ route('buyer.home') }}" class="btn btn-primary-custom px-5">Lihat Menu</a>
    </div>
    @endforelse

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mb-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
