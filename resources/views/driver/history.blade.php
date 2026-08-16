@extends('layouts.app')
@section('title', 'Riwayat Pengantaran')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-3" style="font-size:1.1rem; color:#1F2937;">
        <i class="bi bi-clock-history me-2" style="color:#FF6B35;"></i>Riwayat Pengantaran
    </h2>

    @forelse($orders as $order)
    <div class="card border-0 mb-2 p-3" style="border-radius:14px; box-shadow:0 2px 6px rgba(0,0,0,.05);">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <span class="fw-700 small" style="color:#1F2937;">Pesanan #{{ $order->id }}</span>
            <span class="badge badge-status badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
        </div>
        <p class="small text-muted mb-1">
            <i class="bi bi-shop me-1"></i>{{ $order->shop->name }}
            &bull; {{ $order->buyer->name }}
        </p>
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:.7rem;">{{ $order->created_at->format('d M Y, H:i') }}</span>
            <span class="fw-700 price-tag small">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">📋</div>
        <h5 class="fw-600 mt-3 text-muted">Belum ada riwayat pengantaran</h5>
    </div>
    @endforelse

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-2">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
