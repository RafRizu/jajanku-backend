@extends('layouts.app')
@section('title', 'Dashboard Penjual')

@section('content')
<div class="p-3">
    <!-- Greeting -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-muted small mb-0">Selamat datang,</p>
            <h2 class="fw-800 mb-0" style="font-size:1.1rem; color:#1F2937;">{{ auth()->user()->name }} 👋</h2>
        </div>
        @if(!$shop)
        <a href="{{ route('seller.shop.edit') }}" class="btn btn-sm btn-primary-custom">Setup Warung</a>
        @endif
    </div>

    @if(!$shop)
    <!-- No shop warning -->
    <div class="alert rounded-3 border-0 p-4 mb-3"
         style="background:linear-gradient(135deg,#FFF7F5,#FFE8D6);">
        <h5 class="fw-700" style="color:#C2410C;">⚠️ Warung Belum Terdaftar</h5>
        <p class="small mb-3" style="color:#92400E;">Silakan lengkapi profil warung Anda untuk mulai menerima pesanan.</p>
        <a href="{{ route('seller.shop.edit') }}" class="btn btn-sm btn-primary-custom">Daftarkan Warung</a>
    </div>
    @else
    <!-- Stats Grid -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px; background:linear-gradient(135deg,#FF6B35,#FF8C42);">
                <div class="text-white fw-800" style="font-size:1.6rem;">{{ $stats['total_orders'] }}</div>
                <div class="text-white opacity-75 small">Total Pesanan</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px; background:linear-gradient(135deg,#F59E0B,#FBBF24);">
                <div class="text-white fw-800" style="font-size:1.6rem;">{{ $stats['pending_orders'] }}</div>
                <div class="text-white opacity-75 small">Pesanan Baru</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px; background:linear-gradient(135deg,#10B981,#34D399);">
                <div class="text-white fw-800" style="font-size:1.6rem;">{{ $stats['total_products'] }}</div>
                <div class="text-white opacity-75 small">Jumlah Menu</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px; background:linear-gradient(135deg,#6366F1,#818CF8);">
                <div class="text-white fw-800 lh-sm" style="font-size:1rem;">Rp {{ number_format($stats['revenue'] / 1000, 0, ',', '.') }}k</div>
                <div class="text-white opacity-75 small">Total Pendapatan</div>
            </div>
        </div>
    </div>

    <!-- Shop Status Card -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-700 mb-1">{{ $shop->name }}</h6>
                <p class="text-muted small mb-0">{{ $shop->address }}</p>
            </div>
            <span class="badge badge-status badge-{{ $shop->status === 'active' ? 'success' : 'warning' }}">
                {{ $shop->status === 'active' ? '🟢 Buka' : '🟡 Tutup' }}
            </span>
        </div>
        <div class="d-flex gap-2 mt-2">
            <a href="{{ route('seller.shop.edit') }}" class="btn btn-sm flex-fill rounded-3" style="border:1.5px solid #E5E7EB;color:#374151;font-size:.8rem;">
                <i class="bi bi-pencil me-1"></i>Edit Warung
            </a>
            <a href="{{ route('seller.products.create') }}" class="btn btn-sm flex-fill rounded-3 btn-primary-custom" style="font-size:.8rem;">
                <i class="bi bi-plus me-1"></i>Tambah Menu
            </a>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h3 class="section-title mb-0">📋 Pesanan Terbaru</h3>
        <a href="{{ route('seller.orders') }}" class="small" style="color:#FF6B35;">Lihat semua</a>
    </div>

    @forelse($orders as $order)
    <div class="card border-0 mb-2 p-3" style="border-radius:14px; box-shadow:0 2px 6px rgba(0,0,0,.05);">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <span class="fw-600 small" style="color:#1F2937;">{{ $order->buyer->name }}</span>
                <p class="text-muted mb-0" style="font-size:.7rem;">{{ $order->created_at->diffForHumans() }}</p>
            </div>
            <span class="badge badge-status badge-{{ $order->status_badge }}">{{ $order->status_label }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">{{ $order->items->count() }} item</span>
            <span class="price-tag fw-700 small">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        @if($order->status === 'pending')
        <form method="POST" action="{{ route('seller.orders.process', $order->id) }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary-custom w-100" style="font-size:.8rem;">
                <i class="bi bi-check2-circle me-1"></i>Konfirmasi Pesanan
            </button>
        </form>
        @endif
    </div>
    @empty
    <div class="text-center py-3">
        <p class="text-muted small">Belum ada pesanan masuk</p>
    </div>
    @endforelse
    @endif
</div>
@endsection
