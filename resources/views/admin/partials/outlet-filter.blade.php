<div class="card mb-4">
    <div class="card-body">
        <form action="{{ url()->current() }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label for="outlet_id">Filter Berdasarkan Outlet</label>
                        <select name="outlet_id" id="outlet_id" class="form-control">
                            <option value="">Tampilkan Semua Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ $selectedOutletId == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>