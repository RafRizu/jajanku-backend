@extends('layouts.app')
@section('title', 'Manajemen Menu')

@section('content')
<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-700 mb-0" style="font-size:1.1rem; color:#1F2937;">
            <i class="bi bi-bag-heart-fill me-2" style="color:#FF6B35;"></i>Menu Saya
        </h2>
        <a href="{{ route('seller.products.create') }}" class="btn btn-sm btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>Tambah
        </a>
    </div>

    @if(!$shop)
    <div class="alert rounded-3 border-0 p-4" style="background:#FFF7F5;">
        <p class="fw-600 mb-2" style="color:#C2410C;">⚠️ Setup warung terlebih dahulu!</p>
        <a href="{{ route('seller.shop.edit') }}" class="btn btn-sm btn-primary-custom">Setup Warung</a>
    </div>
    @else
    @forelse($products as $product)
    <div class="card border-0 mb-2 p-3" style="border-radius:14px; box-shadow:0 2px 6px rgba(0,0,0,.05);">
        <div class="d-flex gap-3 align-items-center">
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" class="product-card-img" alt="{{ $product->name }}">
            @else
                <div class="product-card-img-placeholder">🍽️</div>
            @endif
            <div class="flex-fill">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-600 mb-1" style="font-size:.85rem;color:#1F2937;">{{ $product->name }}</h6>
                        <span class="price-tag small">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="ms-2 small text-muted">Stok: {{ $product->stock }}</span>
                    </div>
                    <div class="d-flex gap-1">
                        <span class="badge badge-status {{ $product->is_available ? 'badge-success' : 'badge-danger' }}">
                            {{ $product->is_available ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-1 mt-2">
                    <a href="{{ route('seller.products.edit', $product) }}"
                       class="btn btn-sm px-2 py-1 rounded-2"
                       style="border:1.5px solid #E5E7EB;color:#374151;font-size:.7rem;">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('seller.products.toggle', $product) }}" class="d-inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm px-2 py-1 rounded-2"
                                style="border:1.5px solid {{ $product->is_available ? '#FCA5A5' : '#6EE7B7' }};
                                       color:{{ $product->is_available ? '#991B1B' : '#065F46' }};font-size:.7rem;">
                            {{ $product->is_available ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('seller.products.destroy', $product) }}" class="d-inline"
                          onsubmit="return confirm('Hapus produk ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm px-2 py-1 rounded-2"
                                style="border:1.5px solid #FCA5A5;color:#991B1B;font-size:.7rem;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:4rem;">🍽️</div>
        <h5 class="fw-600 mt-3 text-muted">Belum ada menu</h5>
        <a href="{{ route('seller.products.create') }}" class="btn btn-primary-custom px-4 mt-2">Tambah Menu Pertama</a>
    </div>
    @endforelse

    @if($products->hasPages())
        {{ $products->links('pagination::bootstrap-5') }}
    @endif
    @endif
</div>
@endsection
