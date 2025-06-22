@push('styles')
<style> /* Bisa dipindah ke admin.css */
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
    .form-group input, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
    .form-group .img-preview { margin-top: 1rem; max-width: 200px; display: block; }
    .form-group .form-error { color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; }
</style>
@endpush

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
            <img id="img-preview" src="" alt="Preview" class="img-preview" style="display:none;">
        @endif
    @else
        <img id="img-preview" src="" alt="Preview" class="img-preview" style="display:none;">
    @endisset
    @error('image') <div class="form-error">{{ $message }}</div> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($outlet) ? 'Perbarui' : 'Simpan' }}</button>
<a href="{{ route('admin.outlets.index') }}" class="btn" style="background-color: #6c757d; color:white;">Batal</a>

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        const imageField = document.getElementById("img-preview");

        reader.onload = function(){
            if (reader.readyState == 2) {
                imageField.style.display = 'block';
                imageField.src = reader.result;
            }
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush