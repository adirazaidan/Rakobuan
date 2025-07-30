<div class="status-card" data-order-session-id="{{ $order->session_id }}">
    <div class="receipt-details">
        <div class="receipt-header">
            <h3>Rincian Pesanan #{{ $order->order_number }}</h3>
            <div class="receipt-customer-info">
                <span><strong>Waktu:</strong> {{ $order->created_at->format('d M Y, H:i') }}</span>
                <p class="receipt-status">
                    <strong>Status:</strong> 
                    <span id="order-status-badge-{{ $order->id }}" class="status-badge status-{{ $order->status }}">
                        {{ $order->translated_status }}
                    </span>
                </p>
            </div>
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th class="text-center">No.</th>
                    <th>Item</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="item-name">{{ $item->product->name }}</div>
                            @if($item->notes)
                                <div class="receipt-item-notes">
                                    <i class="fas fa-sticky-note"></i> {{ $item->notes }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total</td>
                    <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>