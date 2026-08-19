@extends('layouts.app')
@section('title', isset($product) ? 'Edit Menu' : 'Tambah Menu')

@section('content')
{{-- ── Header ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#FF6B35,#FF8C42); padding:20px 16px 44px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:rgba(255,255,255,.08);border-radius:50%;"></div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('seller.products') }}" class="btn p-0 text-white me-2">
            <i class="bi bi-arrow-left" style="font-size:1.4rem;"></i>
        </a>
        <div>
            <h1 class="text-white fw-800 mb-0" style="font-size:1.2rem;">
                {{ isset($product) ? '✏️ Edit Menu' : '➕ Tambah Menu Baru' }}
            </h1>
            <p class="mb-0 small" style="color:rgba(255,255,255,.75);">Isi nama, harga, dan kategori menu makanan/minuman</p>
        </div>
    </div>
</div>

<div style="margin-top:-20px;padding:0 14px;">
    <form method="POST"
          action="{{ isset($product) ? route('seller.products.update', $product) : route('seller.products.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <!-- Foto Menu -->
        <div class="card border-0 mb-3 p-3 text-center" style="border-radius:16px; box-shadow:0 3px 12px rgba(0,0,0,.07);">
            <p class="fw-700 small mb-2 text-start" style="color:#1F2937;">📷 Foto Menu (Boleh Dikosongkan)</p>
            <div id="image-preview-wrap">
                @if(isset($product) && $product->image)
                    <img src="{{ Storage::url($product->image) }}" id="product-preview"
                         class="rounded-4 mb-2 mx-auto" style="width:100px;height:100px;object-fit:cover;">
                @else
                    <div id="image-placeholder" class="mx-auto mb-2 rounded-4 d-flex align-items-center justify-content-center"
                         style="width:100px;height:100px;background:linear-gradient(135deg,#FFE0CC,#FFD4B5);font-size:2.5rem;">
                        🍢
                    </div>
                @endif
            </div>
            <label class="btn btn-sm rounded-pill px-4 py-2 mx-auto" style="border:1.5px solid #FF6B35;color:#FF6B35;font-weight:600;" for="product-img">
                <i class="bi bi-camera me-1"></i>Pilih Gambar Foto
            </label>
            <input type="file" name="image" id="product-img" class="d-none" accept="image/*">
        </div>

        <div class="card border-0 mb-3 p-3" style="border-radius:16px; box-shadow:0 3px 12px rgba(0,0,0,.07);">
            <div class="mb-3">
                <label class="form-label fw-700 small text-dark">Nama Jajanan / Menu *</label>
                <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                       style="border-radius:12px;font-size:.95rem;"
                       value="{{ old('name', $product->name ?? '') }}"
                       placeholder="Contoh: Cireng Ayam Suwir, Es Teh Tarik" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-700 small text-dark">Kategori *</label>
                <select name="category_id" class="form-select form-select-lg @error('category_id') is-invalid @enderror"
                        style="border-radius:12px;font-size:.95rem;" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->icon }} {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fw-700 small text-dark">Harga (Rupiah) *</label>
                    <input type="number" name="price" class="form-control form-control-lg @error('price') is-invalid @enderror"
                           style="border-radius:12px;font-size:.95rem;"
                           value="{{ old('price', $product->price ?? '') }}"
                           placeholder="1000" min="0" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-700 small text-dark">Jumlah Stok</label>
                    <input type="number" name="stock" class="form-control form-control-lg"
                           style="border-radius:12px;font-size:.95rem;"
                           value="{{ old('stock', $product->stock ?? 50) }}"
                           placeholder="50" min="0">
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label fw-700 small text-dark">Keterangan / Catatan Singkat</label>
                <textarea name="description" class="form-control" rows="2" style="border-radius:12px;"
                          placeholder="Contoh: Gurih, pedas manis, porsi kenyang...">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-700 mb-3" style="font-size:1.05rem;border-radius:14px;">
            💾 {{ isset($product) ? 'SIMPAN PERUBAHAN' : 'SIMPAN MENU BARU' }}
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('product-img').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('image-preview-wrap');
            wrap.innerHTML = `<img src="${e.target.result}" id="product-preview"
                class="rounded-4 mb-2 mx-auto" style="width:100px;height:100px;object-fit:cover;">`;
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
