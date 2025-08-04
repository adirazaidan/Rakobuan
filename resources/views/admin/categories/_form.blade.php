<div class="card-body">
<div class="form-group">
    <label for="outlet_id">Pilih Outlet</label>
    <select name="outlet_id" id="outlet_id" class="form-control" required>
        <option value="" disabled selected>-- Pilih Outlet --</option>
        @foreach ($outlets as $outlet)
            <option value="{{ $outlet->id }}" 
            {{-- Cek jika ada data lama (saat validasi error) atau jika sedang mode edit --}}
            {{ old('outlet_id', $category->outlet_id ?? '') == $outlet->id ? 'selected' : '' }}>
                {{ $outlet->name }}
            </option>
        @endforeach
    </select>
    @error('outlet_id') <div class="form-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="name">Nama Kategori</label>
    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
    @error('name') <div class="form-error">{{ $message }}</div> @enderror
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Perbarui' : 'Simpan' }}</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Batal</a>
</div>
</div>