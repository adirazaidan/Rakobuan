<option value="" disabled selected>-- Meja yang Tersedia --</option>
@foreach ($tablesByLocation as $location => $tables)
    <optgroup label="{{ $location ?: 'Lainnya' }}">
        @foreach ($tables as $table)
            <option value="{{ $table->id }}">{{ $table->name }}</option>
        @endforeach
    </optgroup>
@endforeach