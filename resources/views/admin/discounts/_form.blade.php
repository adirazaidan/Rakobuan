<div class="form-group">
    <label for="name">Nama Diskon (Contoh: Diskon Gajian)</label>
    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $discount->name ?? '') }}" required>
</div>

<div class="form-group">
    <label for="product_id">Pilih Menu untuk Diberi Diskon</label>
    <select name="product_id" id="product_id" class="form-control select2" required>
        <option value="" disabled selected>-- Pilih Menu --</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id ?? '') == $product->id ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="percentage">Persentase Diskon (0-100)</label>
    <input type="number" id="percentage" name="percentage" class="form-control" value="{{ old('percentage', $discount->percentage ?? '') }}" required min="0" max="100">
</div>

<div class="form-group">
    <div class="form-check">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $discount->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active">Aktifkan Diskon Ini</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($discount) ? 'Perbarui' : 'Simpan' }}</button>
<a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Batal</a>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: '-- Pilih Menu --',
            allowClear: true
        });
    });
</script>
@endpush