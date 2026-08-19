@extends('layouts.app')
@section('title', 'Job Driver')

@section('content')
{{-- ── Hero ──────────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#1E293B,#334155); padding:20px 16px 48px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:140px;height:140px;
                background:rgba(255,255,255,.04);border-radius:50%;"></div>
    <p class="mb-1 small" style="color:rgba(255,255,255,.6);">Halo, {{ auth()->user()->name }} 🛵</p>
    <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">Job Pengiriman</h1>
    <p class="mb-0 mt-1 small" style="color:rgba(255,255,255,.6);">Siap antar hari ini?</p>
</div>

<div style="margin-top:-28px;padding:0 14px;">

    {{-- ── Active Jobs ──────────────────────────────────────────── --}}
    @if($myOrders->isNotEmpty())
    <div class="mb-3">
        <h3 class="section-title" style="color:#1E293B;">🔥 Job Aktifku</h3>
        @foreach($myOrders as $order)
        <div class="card border-0 mb-3" style="border-radius:16px;box-shadow:0 4px 16px rgba(255,107,53,.15);overflow:hidden;">
            <div style="height:4px;background:linear-gradient(90deg,#FF6B35,#FF8C42);"></div>
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-700 small" style="color:#1F2937;">Pesanan #{{ $order->id }}</span>
                        <p class="text-muted mb-0" style="font-size:.7rem;">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="badge badge-status badge-orange">Sedang Diantar</span>
                </div>

                <div class="p-2 rounded-3 mb-2" style="background:#FFF7F5;border:1px solid #FFE8D6;">
                    <p class="small fw-600 mb-1" style="color:#374151;">
                        <i class="bi bi-shop me-1" style="color:#FF6B35;"></i>{{ $order->shop->name }}
                    </p>
                    <p class="small text-muted mb-1" style="font-size:.72rem;">📍 {{ $order->shop->address }}</p>
                    <hr class="my-1" style="border-color:#FFE8D6;">
                    <p class="small fw-600 mb-1" style="color:#374151;">
                        <i class="bi bi-person me-1" style="color:#FF6B35;"></i>{{ $order->buyer->name }}
                    </p>
                    @if($order->delivery_address)
                    <p class="small text-muted mb-0" style="font-size:.72rem;">🏠 {{ $order->delivery_address }}</p>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('driver.delivery', $order->id) }}"
                       class="btn btn-sm flex-fill rounded-3"
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
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── Available Jobs ──────────────────────────────────────── --}}
    <h3 class="section-title" style="color:#1E293B;">📦 Job Tersedia <span class="text-muted" style="font-weight:400;">({{ $availableOrders->total() }})</span></h3>

    @forelse($availableOrders as $order)
    <div class="card border-0 mb-3" style="border-radius:16px;box-shadow:0 3px 12px rgba(0,0,0,.07);overflow:hidden;">
        <div style="height:3px;background:linear-gradient(90deg,#10B981,#34D399);"></div>
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="fw-700 small" style="color:#1F2937;">Pesanan #{{ $order->id }}</span>
                    <p class="text-muted mb-0" style="font-size:.7rem;">{{ $order->created_at->diffForHumans() }}</p>
                </div>
                <span class="fw-700 price-tag small">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

            <div class="p-2 rounded-3 mb-2" style="background:#F0FDF4;border:1px solid #BBF7D0;">
                <p class="small fw-600 mb-1" style="color:#374151;">
                    <i class="bi bi-shop me-1" style="color:#10B981;"></i>Jemput di: {{ $order->shop->name }}
                </p>
                <p class="small text-muted mb-1" style="font-size:.72rem;">{{ $order->shop->address }}</p>
                @if($order->delivery_address)
                <hr class="my-1" style="border-color:#BBF7D0;">
                <p class="small fw-600 mb-0" style="color:#374151;">
                    <i class="bi bi-geo-alt me-1" style="color:#10B981;"></i>Antar ke: {{ Str::limit($order->delivery_address, 60) }}
                </p>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                <span><i class="bi bi-bag me-1"></i>{{ $order->items->count() }} item</span>
                <span>Ongkir: <strong class="text-dark">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</strong></span>
            </div>

            <form method="POST" action="{{ route('driver.jobs.accept', $order->id) }}">
                @csrf
                <button type="submit" class="btn btn-primary-custom w-100 fw-600" style="font-size:.85rem;">
                    <i class="bi bi-hand-thumbs-up-fill me-2"></i>Ambil Job Ini
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-5 mt-2">
        <div style="font-size:4rem;">😴</div>
        <h2 class="fw-700 mt-3 mb-1" style="font-size:1.1rem;color:#1F2937;">Belum Ada Job</h2>
        <p class="text-muted small">Santai dulu, job akan muncul di sini</p>
    </div>
    @endforelse

    @if($availableOrders->hasPages())
        <div class="d-flex justify-content-center mt-2 mb-3">
            {{ $availableOrders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Dipanggil oleh global Pusher listener saat job baru tersedia
window.prependNewJobBadge = function(data) {
    if (document.getElementById('new-job-bar')) return;
    const bar = document.createElement('div');
    bar.id = 'new-job-bar';
    bar.style.cssText = 'position:sticky;top:56px;z-index:99;margin-bottom:12px;';
    bar.innerHTML = `
        <div style="background:linear-gradient(135deg,#1E293B,#334155);color:white;padding:10px 16px;
                    border-radius:12px;display:flex;align-items:center;justify-content:space-between;
                    box-shadow:0 4px 16px rgba(0,0,0,.3);font-size:.85rem;font-weight:600;">
            <span>🛵 Job baru ke ${data.delivery_address}!</span>
            <span onclick="location.reload()"
                  style="background:rgba(255,255,255,.15);padding:4px 10px;border-radius:8px;font-size:.75rem;cursor:pointer;">
                Refresh
            </span>
        </div>`;
    const ref = document.querySelector('.section-title');
    if (ref) ref.before(bar);
};
</script>
@endpush
