@php
    $statusClass = '';
    if ($table->is_locked) {
        $statusClass = 'is-locked';
    } elseif ($table->session_id) {
        $statusClass = 'is-occupied';
    }
    $completedOrderForThisSession = $table->latestCompletedOrder && $table->session_id && $table->latestCompletedOrder->session_id === $table->session_id;
@endphp

<div class="table-card {{ $statusClass }}" id="table-card-{{ $table->id }}">
    <div class="table-visual">
        <span class="table-visual-name">{{ $table->name }}</span>
        <div class="table-status-text">
            @if($table->is_locked)
                <span class="status-locked">Terkunci</span>
            @elseif($table->session_id)
                <span class="status-occupied">Diduduki</span>
            @else
                <span class="status-available">Tersedia</span>
            @endif
        </div>
    </div>

    @if($table->activeOrder)
    <div class="table-card-order-details">
        <h5>Pesanan Aktif: #{{ $table->activeOrder->id }}</h5>
        <ul class="order-item-list">
            @foreach($table->activeOrder->orderItems as $item)
            <li class="order-item-row {{ $item->quantity <= $item->quantity_delivered ? 'item-delivered' : '' }}">
                <div class="item-info">
                    <span class="item-quantity">{{ $item->quantity }}x</span>
                    <span class="item-name">{{ $item->product->name }}</span>
                </div>
                <div class="item-delivery-status">
                    <form action="{{ route('admin.order-items.deliver', $item) }}" method="POST" class="deliver-form">
                        @csrf
                        <button type="submit" class="btn-deliver-action" title="Tandai 1 item telah diantar" @if($item->quantity <= $item->quantity_delivered) disabled @endif>
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    <span>{{ $item->quantity_delivered }}/{{ $item->quantity }}</span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($table->activeCalls->isNotEmpty())
    <div class="table-card-call-details">
        <h5>Panggilan Aktif:</h5>
        <ul class="call-item-list">
            @foreach($table->activeCalls as $call)
            <li class="call-item-row">
                <span class="call-note" title="{{ $call->notes }}"><i class="fas fa-comment-dots"></i> {{ Str::limit($call->notes, 20) ?: 'Memanggil pelayan' }}</span>
                <div class="call-actions">
                    {{-- Tombol ini akan menandai panggilan sebagai 'completed' --}}
                    <form action="{{ route('admin.calls.updateStatus', $call) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn-deliver-action" title="Tandai Selesai">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="table-card-details">
        <div class="table-card-info">
            <span class="table-location">{{ $table->location }}</span>
            <p class="table-notes">{{ $table->notes ?: 'Tidak ada catatan' }}</p>
        </div>
        <div class="table-card-actions">
            <a href="{{ route('admin.dining-tables.edit', $table) }}" class="btn-action-edit" title="Edit Meja"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.dining-tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus meja ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action-delete" title="Hapus Meja"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>

    @if($table->session_id)
    <div class="table-card-footer">
        @if($completedOrderForThisSession)
            <button class="btn btn-sm btn-secondary w-100 btn-view-history"
                    data-order='{{ $table->latestCompletedOrder->load('orderItems.product')->toJson() }}'>
                Lihat Riwayat Sesi Ini
            </button>
        @endif
        <form action="{{ route('admin.dining-tables.clearSession', $table) }}" method="POST" onsubmit="return confirm('Yakin ingin membersihkan sesi ini?');" class="clear-session-form">
            @csrf
            <button type="submit" class="btn btn-sm btn-info w-100">Clear Session</button>
        </form>
    </div>
    @endif
</div>