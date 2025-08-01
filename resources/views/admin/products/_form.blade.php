<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            <label for="name">Nama Menu</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="stock">Jumlah Stok</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? '0') }}" required>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="" disabled selected>-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->outlet->name }} - {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="image">Gambar Menu</label>
            <input type="file" id="image" name="image" class="form-control">
            @isset($product->image)
                <img src="{{ Storage::url('products/' . $product->image) }}" alt="Preview" class="img-preview" style="margin-top: 10px; max-width: 150px;">
            @endisset
        </div>
        <div class="form-group">
            <label>Opsi</label>
            <div class="form-check">
                <input type="checkbox" name="is_bestseller" id="is_bestseller" value="1" {{ old('is_bestseller', $product->is_bestseller ?? false) ? 'checked' : '' }}>
                <label for="is_bestseller">Terlaris</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', $product->is_available ?? true) ? 'checked' : '' }}>
                <label for="is_available">Tersedia</label>
            </div>
        </div>
    </div>
</div>
<hr>
<button type="submit" class="btn btn-primary">{{ isset($product) ? 'Perbarui Menu' : 'Simpan Menu' }}</button>
<a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Batal</a>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
    });
</script>
@endpush