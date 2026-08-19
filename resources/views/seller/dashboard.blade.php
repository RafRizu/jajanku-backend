@extends('layouts.app')
@section('title', 'Dasbor Bu Ipa')

@section('content')
{{-- ── Hero ──────────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 52px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:150px;height:150px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <p class="small mb-1" style="color:rgba(255,255,255,.8);">Selamat datang, 👋</p>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.4rem; line-height:1.2;">
        {{ auth()->user()->name }}
    </h1>
    <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.75);">🏪 Warung Bu Ipa</p>
</div>

<div style="margin-top:-28px; padding:0 14px;">

    @if(!$shop)
    {{-- Belum ada warung --}}
    <div class="card border-0 mb-3 p-4 text-center" style="border-radius:20px;box-shadow:0 8px 24px rgba(0,0,0,.1);">
        <div style="font-size:4rem;">😅</div>
        <h2 class="fw-800 mt-2 mb-1" style="font-size:1.1rem;color:#C2410C;">Warung belum terdaftar</h2>
        <p class="text-muted small mb-3">Tap tombol di bawah untuk mulai setup warungmu.</p>
        <a href="{{ route('seller.shop.edit') }}" class="btn btn-primary-custom py-3 fw-700" style="font-size:1rem;">
            ✏️ Setup Warung Sekarang
        </a>
    </div>

    @else

    {{-- ── Stats ─────────────────────────────────────────────────── --}}
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px;background:linear-gradient(135deg,#FF6B35,#FF8C42);box-shadow:0 4px 16px rgba(255,107,53,.3);">
                <div class="text-white fw-800" style="font-size:2rem;">{{ $stats['pending_orders'] }}</div>
                <div class="text-white small" style="opacity:.9;">⏳ Pesanan Baru</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px;background:linear-gradient(135deg,#10B981,#34D399);box-shadow:0 4px 16px rgba(16,185,129,.25);">
                <div class="text-white fw-800" style="font-size:2rem;">{{ $stats['total_orders'] }}</div>
                <div class="text-white small" style="opacity:.9;">📋 Total Pesanan</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px;background:linear-gradient(135deg,#6366F1,#818CF8);box-shadow:0 4px 16px rgba(99,102,241,.25);">
                <div class="text-white fw-800" style="font-size:1.1rem;line-height:1.5;">
                    Rp {{ number_format($stats['revenue'] / 1000, 0, ',', '.') }}k
                </div>
                <div class="text-white small" style="opacity:.9;">💰 Pendapatan</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 p-3 text-center" style="border-radius:16px;background:linear-gradient(135deg,#F59E0B,#FBBF24);box-shadow:0 4px 16px rgba(245,158,11,.25);">
                <div class="text-white fw-800" style="font-size:2rem;">{{ $stats['total_products'] }}</div>
                <div class="text-white small" style="opacity:.9;">🍢 Jumlah Menu</div>
            </div>
        </div>
    </div>

    {{-- ── Panduan Singkat (gaptek-friendly) ───────────────────── --}}
    <div class="card border-0 mb-3 p-3" style="border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,.07);background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:1.5px solid #FDE68A;">
        <p class="fw-800 mb-2" style="font-size:.9rem;color:#92400E;">📌 Cara Pakai: Bacanya Sekali Aja Ya!</p>
        <div class="d-flex gap-2 mb-2">
            <span style="font-size:1.3rem;">1️⃣</span>
            <p class="small mb-0" style="color:#78350F;">Kalau ada pesanan baru, kamu dapat notif. Buka menu <strong>Pesanan</strong>.</p>
        </div>
        <div class="d-flex gap-2 mb-2">
            <span style="font-size:1.3rem;">2️⃣</span>
            <p class="small mb-0" style="color:#78350F;">Tap <strong>"Konfirmasi"</strong> buat terima pesanan, lalu <strong>"Mulai Proses"</strong> saat lagi masak.</p>
        </div>
        <div class="d-flex gap-2 mb-2">
            <span style="font-size:1.3rem;">3️⃣</span>
            <p class="small mb-0" style="color:#78350F;">Setelah siap, tap <strong>"Request Driver"</strong> biar driver ambil pesanan.</p>
        </div>
        <div class="d-flex gap-2">
            <span style="font-size:1.3rem;">4️⃣</span>
            <p class="small mb-0" style="color:#78350F;">Pesanan beres! Kamu bisa cek menu <strong>Riwayat</strong> kapan saja.</p>
        </div>
    </div>

    {{-- ── Aksi Cepat ─────────────────────────────────────────── --}}
    <h3 class="section-title mb-2">⚡ Aksi Cepat</h3>
    <div class="row g-2 mb-3">
        <div class="col-6">
            <a href="{{ route('seller.orders') }}" class="text-decoration-none">
                <div class="card border-0 p-3 text-center" style="border-radius:16px;box-shadow:0 3px 12px rgba(0,0,0,.08);background:white;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <div style="font-size:2rem;">📋</div>
                    <div class="fw-700 mt-1" style="font-size:.85rem;color:#1F2937;">Lihat Pesanan</div>
                    @if($stats['pending_orders'] > 0)
                    <span class="badge rounded-pill mt-1" style="background:#FF6B35;color:white;font-size:.65rem;">
                        {{ $stats['pending_orders'] }} baru!
                    </span>
                    @endif
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('seller.products') }}" class="text-decoration-none">
                <div class="card border-0 p-3 text-center" style="border-radius:16px;box-shadow:0 3px 12px rgba(0,0,0,.08);background:white;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <div style="font-size:2rem;">🍢</div>
                    <div class="fw-700 mt-1" style="font-size:.85rem;color:#1F2937;">Kelola Menu</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('seller.products.create') }}" class="text-decoration-none">
                <div class="card border-0 p-3 text-center" style="border-radius:16px;box-shadow:0 3px 12px rgba(0,0,0,.08);background:white;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <div style="font-size:2rem;">➕</div>
                    <div class="fw-700 mt-1" style="font-size:.85rem;color:#1F2937;">Tambah Menu</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('seller.shop.edit') }}" class="text-decoration-none">
                <div class="card border-0 p-3 text-center" style="border-radius:16px;box-shadow:0 3px 12px rgba(0,0,0,.08);background:white;min-height:90px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <div style="font-size:2rem;">🏪</div>
                    <div class="fw-700 mt-1" style="font-size:.85rem;color:#1F2937;">Edit Warung</div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── Pesanan Terbaru ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h3 class="section-title mb-0">📋 Pesanan Terbaru</h3>
        <a href="{{ route('seller.orders') }}" class="small fw-600" style="color:#FF6B35;">Lihat semua →</a>
    </div>

    @forelse($orders as $order)
    <div class="card border-0 mb-2" style="border-radius:14px;box-shadow:0 2px 6px rgba(0,0,0,.05);overflow:hidden;"
         data-order-id="{{ $order->id }}">
        <div style="height:4px;background:{{ match($order->status) {
            'pending'     => 'linear-gradient(90deg,#F59E0B,#FBBF24)',
            'confirmed'   => 'linear-gradient(90deg,#3B82F6,#60A5FA)',
            'processing'  => 'linear-gradient(90deg,#8B5CF6,#A78BFA)',
            'on_delivery' => 'linear-gradient(90deg,#FF6B35,#FF8C42)',
            'delivered'   => 'linear-gradient(90deg,#10B981,#34D399)',
            default       => '#E5E7EB',
        } }};"></div>
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-700 small" style="color:#1F2937;">{{ $order->buyer->name }}</span>
                <span class="badge badge-status badge-{{ $order->status_badge }} order-status-badge">{{ $order->status_label }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">{{ $order->items->count() }} item · {{ $order->created_at->diffForHumans() }}</span>
                <span class="price-tag fw-700 small">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            @if($order->status === 'pending')
            <form method="POST" action="{{ route('seller.orders.process', $order->id) }}" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-700" style="font-size:.9rem;">
                    ✅ Konfirmasi Pesanan Ini
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-4">
        <div style="font-size:3rem;">😴</div>
        <p class="text-muted small mt-2">Belum ada pesanan. Santai dulu ya!</p>
    </div>
    @endforelse

    @endif
</div>
@endsection
