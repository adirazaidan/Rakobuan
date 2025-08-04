<div class="card-body">
    <div class="form-group">
        <label for="name">Nama/Nomor Meja</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $diningTable->name ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="location">Lokasi Meja</label>
        <select name="location" id="location" class="form-control" required>
            <option value="">-- Pilih Lokasi --</option>
            <option value="Indoor" {{ old('location', $diningTable->location ?? '') == 'Indoor' ? 'selected' : '' }}>Indoor</option>
            <option value="Outdoor" {{ old('location', $diningTable->location ?? '') == 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
            <option value="Outdoor Atas" {{ old('location', $diningTable->location ?? '') == 'Outdoor Atas' ? 'selected' : '' }}>Outdoor Atas</option>
            <option value="VIP" {{ old('location', $diningTable->location ?? '') == 'VIP' ? 'selected' : '' }}>VIP</option>
            <option value="Takeaway" {{ old('location', $diningTable->location ?? '') == 'Takeaway' ? 'selected' : '' }}>Takeaway</option>
        </select>
    </div>

    <div class="form-group">
        <label for="notes">Catatan</label>
        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $diningTable->notes ?? '') }}</textarea>
    </div>

    @isset($diningTable)
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_locked" id="is_locked" value="1" {{ old('is_locked', $diningTable->is_locked ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_locked">
                Kunci Meja ini (tidak bisa dipesan pelanggan)
            </label>
        </div>
    </div>
    @endisset

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{ isset($diningTable) ? 'Perbarui' : 'Simpan' }}</button>
        <a href="{{ route('admin.dining-tables.index') }}" class="btn btn-secondary">Batal</a>
    </div>
</div>