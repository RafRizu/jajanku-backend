@extends('layouts.app')
@section('title', 'Pesanan Masuk')

@section('content')
{{-- ── Header ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 48px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <h1 class="text-white fw-800 mb-1" style="font-size:1.3rem;">
        📋 Pesanan Masuk
    </h1>
    <p class="mb-0 small" style="color:rgba(255,255,255,.8);">Pastikan bukti pembayaran valid sebelum mulai memasak</p>
</div>

<div style="margin-top:-28px;padding:0 14px;">

    {{-- ── Filter Tabs ─────────────────────────────────────────── --}}
    <div class="card border-0 p-2 mb-3" style="border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,.1);">
        <div class="d-flex gap-1 overflow-auto" style="scrollbar-width:none;">
            @foreach(['all'=>'📋 Semua','pending'=>'⏳ Baru','confirmed'=>'✅ Terkonfirmasi','processing'=>'🍳 Masak','on_delivery'=>'🛵 Kirim','delivered'=>'🎉 Selesai','cancelled'=>'❌ Batal'] as $status=>$label)
            <a href="{{ route('seller.orders', $status !== 'all' ? ['status'=>$status] : []) }}"
               class="btn btn-sm flex-shrink-0 rounded-pill {{ request('status','all')===$status ? 'btn-primary-custom' : '' }}"
               style="{{ request('status','all')!==$status ? 'border:1.5px solid #E5E7EB;color:#374151;' : '' }}font-size:.72rem;white-space:nowrap;font-weight:600;">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Order List ──────────────────────────────────────────── --}}
    @forelse($orders as $order)
    <div class="card border-0 mb-3" style="border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,.08);overflow:hidden;"
         id="seller-orders-list" data-order-id="{{ $order->id }}">

        {{-- status color bar --}}
        <div style="height:6px;background:{{ match($order->status) {
            'pending'     => 'linear-gradient(90deg,#F59E0B,#FBBF24)',
            'confirmed'   => 'linear-gradient(90deg,#3B82F6,#60A5FA)',
            'processing'  => 'linear-gradient(90deg,#8B5CF6,#A78BFA)',
            'on_delivery' => 'linear-gradient(90deg,#FF6B35,#FF8C42)',
            'delivered'   => 'linear-gradient(90deg,#10B981,#34D399)',
            'cancelled'   => '#EF4444',
            default       => '#E5E7EB',
        } }};"></div>

        <div class="p-3">
            {{-- Nama Pembeli + Status --}}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <p class="text-muted mb-0" style="font-size:.7rem;">Pesanan #{{ $order->id }}</p>
                    <span class="fw-800" style="font-size:1rem;color:#1F2937;">{{ $order->buyer->name }}</span>
                    <p class="text-muted mb-0 small">
                        🕐 {{ $order->created_at->diffForHumans() }}
                        @if($order->delivery_type === 'delivery') · 🛵 Antar ke lokasi
                        @else · 🏃 Ambil sendiri
                        @endif
                    </p>
                </div>
                <span class="badge badge-status badge-{{ $order->status_badge }} order-status-badge" style="font-size:.75rem;">
                    {{ $order->status_label }}
                </span>
            </div>

            {{-- Daftar Item --}}
            <div class="p-2 rounded-3 mb-2" style="background:#F9FAFB;border:1px solid #E5E7EB;">
                @foreach($order->items as $item)
                <div class="d-flex justify-content-between small mb-1">
                    <span class="fw-600" style="color:#374151;">{{ $item->product->name ?? '-' }} × {{ $item->quantity }}</span>
                    <span class="fw-700" style="color:#FF6B35;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
                @if($order->delivery_address)
                <hr class="my-1" style="border-color:#E5E7EB;">
                <p class="small mb-0 text-muted">📍 {{ $order->delivery_address }}</p>
                @endif
                @if($order->notes)
                <p class="small mb-0 text-secondary mt-1">📝 Catatan: <em>"{{ $order->notes }}"</em></p>
                @endif
            </div>

            {{-- Total + Info Pembayaran & Bukti --}}
            <div class="p-2 rounded-3 mb-3" style="background:#FFF7F5;border:1px solid #FFE8D6;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-800 price-tag" style="font-size:1.05rem;">
                        Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                    @if($order->payment)
                    <span class="badge badge-status {{ $order->payment->status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                        {{ $order->payment->status === 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                    </span>
                    @endif
                </div>

                @if($order->payment)
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color:#FFE8D6!important;">
                    <span class="small text-muted">Metode: <strong class="text-capitalize text-dark">{{ $order->payment->method }}</strong></span>
                    @if($order->payment->proof_image)
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-700"
                                style="font-size:.75rem;"
                                data-bs-toggle="modal" data-bs-target="#proofModal-{{ $order->id }}">
                            📸 Lihat Bukti Transfer
                        </button>
                    @elseif($order->payment->method === 'transfer')
                        <span class="badge bg-light text-danger border border-danger" style="font-size:.65rem;">
                            ⚠️ Belum ada bukti
                        </span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Modal Bukti Transfer --}}
            @if($order->payment && $order->payment->proof_image)
            <div class="modal fade" id="proofModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0" style="border-radius:20px;overflow:hidden;">
                        <div class="modal-header bg-light">
                            <h6 class="modal-title fw-800 text-dark">📸 Bukti Transfer Pesanan #{{ $order->id }}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-3">
                            <img src="{{ Storage::url($order->payment->proof_image) }}"
                                 alt="Bukti Transfer" class="img-fluid rounded-3 shadow-sm mb-3"
                                 style="max-height:400px;object-fit:contain;">
                            <p class="small text-muted mb-0">Pembeli: <strong>{{ $order->buyer->name }}</strong> · Nominal: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                        </div>
                        <div class="modal-footer bg-light p-2">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── TOMBOL AKSI BESAR (gaptek-friendly) ─────────── --}}
            @if($order->status === 'pending')
            {{-- Step 1: Cek Bukti & Terima / Tolak --}}
            <div class="p-2 rounded-3 mb-2" style="background:#FFF7F5;border:1px solid #FFE8D6;">
                <p class="small fw-700 mb-2" style="color:#C2410C;">⏳ Pesanan Baru! Cek bukti bayar dulu sebelum konfirmasi.</p>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('seller.orders.process', $order->id) }}" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-primary-custom w-100 fw-700 py-3" style="font-size:.9rem;border-radius:12px;">
                            ✅ BUKTI VALID (TERIMA)
                        </button>
                    </form>
                    <form method="POST" action="{{ route('seller.orders.cancel', $order->id) }}" class="flex-fill"
                          onsubmit="return confirm('Yakin batalkan pesanan ini? Stok akan dikembalikan.')">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 fw-700 py-3" style="font-size:.9rem;border-radius:12px;background:#EF4444;border:none;">
                            ❌ BUKTI INVALID (TOLAK)
                        </button>
                    </form>
                </div>
            </div>

            @elseif($order->status === 'confirmed')
            {{-- Step 2: Mulai masak atau batalkan --}}
            <div class="p-2 rounded-3 mb-2" style="background:#EDE9FE;border:1px solid #C4B5FD;">
                <p class="small fw-700 mb-2" style="color:#5B21B6;">🍳 Pesanan Terkonfirmasi! Tap jika siap mulai masak.</p>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('seller.orders.process', $order->id) }}" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn w-100 fw-700 py-3 text-white" style="font-size:.9rem;border-radius:12px;background:linear-gradient(135deg,#7C3AED,#8B5CF6);border:none;">
                            🍳 MULAI MASAK
                        </button>
                    </form>
                    <form method="POST" action="{{ route('seller.orders.cancel', $order->id) }}"
                          onsubmit="return confirm('Yakin batalkan pesanan sebelum masak? Stok akan dikembalikan.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger fw-700 py-3 px-3" style="font-size:.9rem;border-radius:12px;" title="Batalkan">
                            ❌ BATAL
                        </button>
                    </form>
                </div>
            </div>

            @elseif($order->status === 'processing')
            {{-- Step 3: Minta driver --}}
            <div class="p-2 rounded-3 mb-2" style="background:#FFEDD5;border:1px solid #FED7AA;">
                <p class="small fw-700 mb-1" style="color:#C2410C;">🍳 Sedang dimasak. Tap jika makanan sudah siap!</p>
                <form method="POST" action="{{ route('seller.orders.driver', $order->id) }}">
                    @csrf
                    <button type="submit" class="btn w-100 fw-700 py-3 text-white" style="font-size:1rem;border-radius:14px;background:linear-gradient(135deg,#EA580C,#F97316);border:none;">
                        🛵 MAKANAN SIAP: MINTA DRIVER
                    </button>
                </form>
            </div>

            @elseif($order->status === 'on_delivery')
            <div class="p-2 rounded-3 text-center" style="background:#FFF7F5;border:1px solid #FFE8D6;">
                <p class="fw-700 mb-0" style="color:#C2410C;font-size:.9rem;">🛵 Driver sedang mengantarkan pesanan...</p>
                @if($order->driver)
                <p class="small text-muted mb-0 mt-1">Driver: <strong>{{ $order->driver->name }}</strong></p>
                @endif
            </div>

            @elseif($order->status === 'delivered')
            <div class="p-2 rounded-3 text-center" style="background:#D1FAE5;border:1px solid #6EE7B7;">
                <p class="fw-700 mb-0" style="color:#065F46;font-size:.9rem;">🎉 Pesanan sudah selesai diantar!</p>
            </div>

            @elseif($order->status === 'cancelled')
            <div class="p-2 rounded-3 text-center" style="background:#FEE2E2;border:1px solid #FCA5A5;">
                <p class="fw-700 mb-0" style="color:#991B1B;font-size:.88rem;">❌ Pesanan Dibatalkan (Stok Produk Dikembalikan)</p>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5 mt-2">
        <div style="font-size:4.5rem;">😴</div>
        <h2 class="fw-800 mt-3 mb-1" style="font-size:1.1rem;color:#1F2937;">Belum Ada Pesanan</h2>
        <p class="text-muted">Santai dulu ya, Bu Ipa 😊<br>Pesanan baru akan muncul di sini.</p>
    </div>
    @endforelse

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mb-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.prependNewOrderBadge = function(data) {
    const existing = document.getElementById('new-order-alert-bar');
    if (existing) {
        const counter = existing.querySelector('.new-order-count');
        if (counter) counter.textContent = parseInt(counter.textContent) + 1;
        return;
    }
    const bar = document.createElement('div');
    bar.id = 'new-order-alert-bar';
    bar.style.cssText = 'position:sticky;top:56px;z-index:99;margin-bottom:12px;';
    bar.innerHTML = `
        <div style="background:linear-gradient(135deg,#FF6B35,#FF8C42);color:white;padding:14px 16px;
                    border-radius:14px;display:flex;align-items:center;justify-content:space-between;
                    box-shadow:0 4px 16px rgba(255,107,53,.4);">
            <span style="font-size:.9rem;font-weight:700;">🛒 <span class="new-order-count">1</span> Pesanan baru dari ${data.buyer_name}!</span>
            <span onclick="location.reload()"
                  style="background:rgba(255,255,255,.25);padding:8px 14px;border-radius:10px;font-size:.8rem;font-weight:700;cursor:pointer;">
                Refresh
            </span>
        </div>`;
    const container = document.querySelector('[style*="margin-top:-28px"]');
    if (container?.firstChild) container.insertBefore(bar, container.firstChild);
};
</script>
@endpush
