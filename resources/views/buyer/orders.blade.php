@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-3" style="font-size:1.1rem; color:#1F2937;">
        <i class="bi bi-receipt-cutoff me-2" style="color:#FF6B35;"></i>Pesanan Saya
    </h2>

    @forelse($orders as $order)
    <a href="{{ route('buyer.order.detail', $order->id) }}" class="text-decoration-none">
        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="fw-700 small" style="color:#1F2937;">{{ $order->shop->name }}</span>
                    <p class="text-muted mb-0" style="font-size:.7rem;">
                        {{ $order->created_at->diffForHumans() }}
                    </p>
                </div>
                <span class="badge badge-status badge-{{ $order->status_badge }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="small text-muted">{{ $order->items->count() ?? 0 }} item</span>
                <span class="fw-700 price-tag">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </a>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">📋</div>
        <h5 class="fw-600 mt-3 text-muted">Belum ada pesanan</h5>
        <a href="{{ route('buyer.home') }}" class="btn btn-primary-custom px-4 mt-2">Mulai Pesan</a>
    </div>
    @endforelse

    @if($orders->hasPages())
        <div class="d-flex justify-content-center">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
