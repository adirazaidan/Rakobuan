<div class="card-body">
<div class="form-group">
    <label for="name">Nama Outlet</label>
    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $outlet->name ?? '') }}" required>
    @error('name') <div class="form-error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="image">Gambar Outlet</label>
    <input type="file" id="image" name="image" class="form-control" onchange="previewImage(event)">
    @isset($outlet)
        @if($outlet->image)
            <img id="img-preview" src="{{ Storage::url('outlets/' . $outlet->image) }}" alt="Preview" class="img-preview">
        @else
            <img id="img-preview" src="" alt="Preview" class="img-preview display-none">
        @endif
    @else
        <img id="img-preview" src="" alt="Preview" class="img-preview display-none">
    @endisset
    @error('image') <div class="form-error">{{ $message }}</div> @enderror
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ isset($outlet) ? 'Perbarui' : 'Simpan' }}</button>
    <a href="{{ route('admin.outlets.index') }}" class="btn btn-secondary">Batal</a>
</div>
</div>