@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="p-3">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('buyer.orders') }}" class="btn p-1" style="color:#374151;">
            <i class="bi bi-arrow-left" style="font-size:1.2rem;"></i>
        </a>
        <h2 class="fw-700 mb-0" style="font-size:1rem; color:#1F2937;">Pesanan #{{ $order->id }}</h2>
    </div>

    <!-- Status Timeline -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h6 class="fw-700 mb-3" style="color:#1F2937;">📦 Status Pesanan</h6>

        <span class="badge badge-status badge-{{ $order->status_badge }} mb-3" style="font-size:.8rem;">
            {{ $order->status_label }}
        </span>

        @php
            $steps = [
                'pending'     => ['label' => 'Menunggu Konfirmasi', 'icon' => '⏳'],
                'confirmed'   => ['label' => 'Dikonfirmasi Penjual', 'icon' => '✅'],
                'processing'  => ['label' => 'Sedang Diproses', 'icon' => '👨‍🍳'],
                'on_delivery' => ['label' => 'Dalam Pengiriman', 'icon' => '🛵'],
                'delivered'   => ['label' => 'Selesai Diantar', 'icon' => '🎉'],
            ];
            $statusOrder = array_keys($steps);
            $currentIdx  = array_search($order->status, $statusOrder);
        @endphp

        @foreach($steps as $statusKey => $step)
        @php $idx = array_search($statusKey, $statusOrder); @endphp
        <div class="d-flex align-items-center gap-3 py-2">
            <div class="status-dot {{ $idx < $currentIdx ? 'done' : ($idx === $currentIdx ? 'active' : '') }}"
                 style="{{ $idx === $currentIdx ? 'width:14px;height:14px;box-shadow:0 0 0 4px rgba(255,107,53,.25);' : '' }}">
            </div>
            <div>
                <span style="font-size:.8rem;" class="fw-{{ $idx === $currentIdx ? '700' : '500' }}
                     {{ $idx > $currentIdx ? 'text-muted' : '' }}">
                    {{ $step['icon'] }} {{ $step['label'] }}
                </span>
            </div>
        </div>
        @if(!$loop->last)
        <div style="width:2px;height:16px;background:#E5E7EB;margin-left:5px;"></div>
        @endif
        @endforeach
    </div>

    <!-- Order Items -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h6 class="fw-700 mb-3" style="color:#1F2937;">🛍️ Item Pesanan</h6>
        @foreach($order->items as $item)
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:#F3F4F6!important;">
            <div>
                <p class="fw-600 mb-0 small">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                <p class="text-muted mb-0" style="font-size:.75rem;">{{ $item->quantity }}× Rp {{ number_format($item->price, 0, ',', '.') }}</p>
            </div>
            <span class="fw-700 price-tag small">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="d-flex justify-content-between mt-2 fw-700">
            <span>Total</span>
            <span style="color:#FF6B35;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Shop & Driver Info -->
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h6 class="fw-700 mb-2" style="color:#1F2937;">🏪 Warung</h6>
        <p class="mb-1 small fw-600">{{ $order->shop->name }}</p>
        <p class="text-muted small mb-0">{{ $order->shop->address }}</p>

        @if($order->driver)
        <hr style="border-color:#F3F4F6;">
        <h6 class="fw-700 mb-1" style="color:#1F2937;">🛵 Driver</h6>
        <p class="small fw-600 mb-0">{{ $order->driver->name }}</p>
        <p class="text-muted small mb-0">{{ $order->driver->phone }}</p>
        @endif
    </div>

    <!-- Payment -->
    @if($order->payment)
    <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h6 class="fw-700 mb-2" style="color:#1F2937;">💳 Pembayaran</h6>
        <div class="d-flex justify-content-between small mb-1">
            <span class="text-muted">Metode</span>
            <span class="fw-600 text-capitalize">{{ $order->payment->method }}</span>
        </div>
        <div class="d-flex justify-content-between small mb-1">
            <span class="text-muted">Status</span>
            <span class="badge badge-status badge-{{ $order->payment->status === 'paid' ? 'success' : 'warning' }}">
                {{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
            </span>
        </div>

        @if($order->payment->status === 'pending' && $order->payment->method === 'transfer')
        <div class="mt-3 p-3 rounded-3" style="background:#FFF7F5;border:1px solid #FFD4B5;">
            <p class="small fw-600 mb-2" style="color:#FF6B35;">Upload Bukti Transfer</p>
            <p class="text-muted small mb-2">Rekening BCA: <strong>123-456-789</strong> a.n. Jajanku</p>
            <form method="POST" action="{{ route('buyer.order.proof', $order->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="proof_image" id="proof_image" class="form-control form-control-sm mb-2" accept="image/*" required>
                <button type="submit" class="btn btn-sm btn-primary-custom w-100">
                    <i class="bi bi-upload me-1"></i>Upload Bukti
                </button>
            </form>
        </div>
        @elseif($order->payment->proof_image)
        <div class="mt-2">
            <img src="{{ Storage::url($order->payment->proof_image) }}" alt="Bukti Transfer"
                 class="w-100 rounded-3" style="max-height:200px;object-fit:cover;">
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
