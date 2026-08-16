@extends('layouts.app')
@section('title', 'Job Driver')

@section('content')
<div class="p-3">
    <h2 class="fw-700 mb-1" style="font-size:1.1rem; color:#1F2937;">
        <i class="bi bi-bicycle me-2" style="color:#FF6B35;"></i>Job Pengiriman
    </h2>
    <p class="text-muted small mb-3">Halo {{ auth()->user()->name }}, siap antar hari ini? 🚴</p>

    <!-- My Active Jobs -->
    @if($myOrders->isNotEmpty())
    <div class="mb-4">
        <h3 class="section-title">🔥 Job Aktifku</h3>
        @foreach($myOrders as $order)
        <div class="card border-0 mb-2 p-3" style="border-radius:16px; box-shadow:0 4px 16px rgba(255,107,53,.15); border-left:4px solid #FF6B35 !important;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="fw-700 small" style="color:#1F2937;">Pesanan #{{ $order->id }}</span>
                    <p class="text-muted mb-0" style="font-size:.7rem;">{{ $order->created_at->diffForHumans() }}</p>
                </div>
                <span class="badge badge-status badge-orange">Sedang Diantar</span>
            </div>
            <div class="mb-2">
                <p class="small fw-600 mb-1" style="color:#374151;">
                    <i class="bi bi-shop me-1" style="color:#FF6B35;"></i>{{ $order->shop->name }}
                </p>
                <p class="small text-muted mb-1">📍 {{ $order->shop->address }}</p>
                <p class="small fw-600 mb-0" style="color:#374151;">
                    <i class="bi bi-person me-1" style="color:#FF6B35;"></i>{{ $order->buyer->name }}
                </p>
                @if($order->delivery_address)
                <p class="small text-muted mb-0">🏠 {{ $order->delivery_address }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('driver.delivery', $order->id) }}"
                   class="btn btn-sm flex-fill fw-600 rounded-3"
                   style="background:#EDE9FE;color:#5B21B6;border:none;font-size:.8rem;">
                    <i class="bi bi-info-circle me-1"></i>Detail
                </a>
                <form method="POST" action="{{ route('driver.delivery.delivered', $order->id) }}" class="flex-fill">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary-custom w-100" style="font-size:.8rem;"
                            onclick="return confirm('Konfirmasi pesanan sudah sampai?')">
                        <i class="bi bi-check2-all me-1"></i>Selesai Antar
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Available Jobs -->
    <h3 class="section-title">📦 Job Tersedia ({{ $availableOrders->total() }})</h3>

    @forelse($availableOrders as $order)
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <span class="fw-700 small" style="color:#1F2937;">Pesanan #{{ $order->id }}</span>
                <p class="text-muted mb-0" style="font-size:.7rem;">{{ $order->created_at->diffForHumans() }}</p>
            </div>
            <span class="fw-700 price-tag small">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>

        <div class="mb-2 p-2 rounded-3" style="background:#F9FAFB;">
            <p class="small fw-600 mb-1" style="color:#374151;">
                <i class="bi bi-shop me-1" style="color:#FF6B35;"></i>Jemput di: {{ $order->shop->name }}
            </p>
            <p class="small text-muted mb-1" style="font-size:.75rem;">{{ $order->shop->address }}</p>
            @if($order->delivery_address)
            <hr class="my-1" style="border-color:#E5E7EB;">
            <p class="small fw-600 mb-0" style="color:#374151;">
                <i class="bi bi-geo-alt me-1" style="color:#10B981;"></i>Antar ke: {{ Str::limit($order->delivery_address, 60) }}
            </p>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small text-muted">{{ $order->items->count() }} item</span>
            <span class="small text-muted">Ongkir: Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
        </div>

        <form method="POST" action="{{ route('driver.jobs.accept', $order->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary-custom w-100 fw-600" style="font-size:.85rem;">
                <i class="bi bi-hand-thumbs-up-fill me-2"></i>Ambil Job Ini
            </button>
        </form>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">😴</div>
        <h5 class="fw-600 mt-3 text-muted">Belum ada job tersedia</h5>
        <p class="text-muted small">Santai dulu, job akan muncul di sini</p>
    </div>
    @endforelse

    @if($availableOrders->hasPages())
        <div class="d-flex justify-content-center mt-2">{{ $availableOrders->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
