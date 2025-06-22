@extends('layouts.admin')
@section('title', 'Daftar Panggilan Masuk')

@section('content')
<div class="container">
    <h1>@yield('title')</h1>
    <p>Daftar panggilan dari pelanggan yang perlu ditangani.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-container">
        @forelse ($calls as $call)
            <div class="order-card status-{{ $call->status }}">
                <div class="order-header">
                    <div>
                        <h3>Meja: {{ $call->table_number }}</h3>
                        <span>Pelanggan: {{ $call->customer_name }}</span>
                    </div>
                    <div class="order-status">
                        <strong>{{ ucfirst($call->status) }}</strong>
                        <span>{{ $call->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div class="order-body">
                    <p><strong>Catatan:</strong> {{ $call->notes ?: '-' }}</p>
                </div>
                <div class="order-footer">
                    <div></div> {{-- Div kosong untuk alignment --}}
                    <div class="order-actions">
                        @if ($call->status == 'pending')
                            <form action="{{ route('admin.calls.updateStatus', $call) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="handled">
                                <button type="submit" class="btn btn-primary">Tangani</button>
                            </form>
                        @elseif ($call->status == 'handled')
                             <form action="{{ route('admin.calls.updateStatus', $call) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success">Selesai</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.calls.destroy', $call) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus panggilan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 2rem; text-align:center;">
                <p>Tidak ada panggilan masuk saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection