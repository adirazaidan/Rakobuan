@php
    $isOccupied = $table->session_id || $table->activeOrders->isNotEmpty();
    $isAbandoned = !$table->session_id && $table->activeOrders->isNotEmpty();

    $statusClass = '';
    if ($table->is_locked) {
        $statusClass = 'is-locked';
    } elseif ($isAbandoned) {
        $statusClass = 'is-abandoned'; 
    } elseif ($isOccupied) {
        $statusClass = 'is-occupied';
    }
@endphp

<div class="table-card {{ $statusClass }}" id="table-card-{{ $table->id }}">
    <div class="table-visual">
        <span class="table-visual-name">{{ $table->name }}</span>
        <div class="table-status-text">
            @if($table->is_locked)
                <span class="status-locked">Terkunci</span>
            @elseif($isAbandoned)
                <span class="status-abandoned"><i class="fas fa-exclamation-triangle"></i> Terabaikan</span>
            @elseif($isOccupied)
                <span class="status-occupied">Diduduki</span>
            @else
                <span class="status-available">Tersedia</span>
            @endif
        </div>
    </div>

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

    @if($table->activeOrders->isNotEmpty())
    <div class="table-card-order-details">
        @foreach($table->activeOrders as $order)
            <div class="order-group">
                <h5>Pesanan Aktif: #{{ $order->order_number }}</h5>
                <ul class="order-item-list">
                    @foreach($order->orderItems as $item)
                    <li class="order-item-row {{ $item->quantity <= $item->quantity_delivered ? 'item-delivered' : 'item-pending' }}"data-created-at="{{ $item->created_at->toIso8601String() }}">
                        <div class="item-info">
                            <div>
                                <span class="item-quantity">{{ $item->quantity }}x</span>
                                <span class="item-name">
                                    {{ $item->product->name }}

                                    @if($item->is_overdue)
                                        <span class="overdue-warning" title="Pesanan ini sudah lebih dari 15 menit!">
                                            <i class="fas fa-clock"></i> Terlambat
                                        </span>
                                    @endif
                                </span>
                            </div>
                            
                            @if($item->notes)
                                <small class="item-note" title="{{ $item->notes }}">
                                    <i class="fas fa-sticky-note"></i> {{ Str::limit($item->notes, 25) }}
                                </small>
                            @endif
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
        @endforeach
    </div>
    @endif

    @if($table->activeCalls->isNotEmpty())
    <div class="table-card-call-details">
        <h5>Panggilan Aktif:</h5>
        <ul class="call-item-list">
            @foreach($table->activeCalls as $call)
            <li class="call-item-row {{ $call->status === 'pending' ? 'item-pending' : '' }}" data-created-at="{{ $call->created_at->toIso8601String() }}">
                <span class="call-note" title="{{ $call->notes }}"><i class="fas fa-comment-dots"></i> {{ Str::limit($call->notes, 20) ?: 'Memanggil pelayan' }}</span>
                @if($call->is_overdue)
                    <span class="overdue-warning" title="Panggilan ini sudah lebih dari 5 menit!">
                        <i class="fas fa-clock"></i> Terlambat
                    </span>
                @endif
                <div class="call-actions">
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
    

    @if($table->session_id)
    <div class="table-card-footer">
        @php
            $completedOrders = $table->completed_orders_for_current_session;
            $sessionCalls = $table->calls_for_current_session;
            $historyData = [
                'orders' => $completedOrders->load('orderItems.product'),
                'calls'  => $sessionCalls
            ];
        @endphp
        @if ($completedOrders->isNotEmpty() || $sessionCalls->isNotEmpty())
            <button class="btn btn-sm btn-secondary w-100 btn-view-history"
                    data-history='{{ json_encode($historyData) }}'
                    data-customer-name="{{ $completedOrders->first()->customer_name ?? $sessionCalls->first()->customer_name ?? '' }}">
                Lihat Riwayat Sesi Ini
            </button>
        @endif
        @if($isOccupied && $table->activeOrders->isEmpty())
            <form action="{{ route('admin.dining-tables.clearSession', $table) }}" method="POST" onsubmit="return confirm('Yakin ingin membersihkan sesi ini?');" class="clear-session-form">
                @csrf
                <button type="submit" class="btn btn-sm btn-info w-100">Bersihkan Meja</button>
            </form>
        @endif
    </div>
    @endif
</div>