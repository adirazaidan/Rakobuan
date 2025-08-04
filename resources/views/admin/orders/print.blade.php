<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $order->order_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body onload="window.print();">
    <div class="receipt">
        <div class="header">
            <h1>Warung Tekko Ramenten10 Lelabuan</h1>
            <p>Jalan S. Parman No. 2, Benua Melayu Darat, Pontianak</p>
            <p>Telp: 0811-826-008</p>
            <p>===============================</p>
            <p>STRUK PESANAN</p>
        </div>

        <div class="details">
            <p><strong>No. Pesanan:</strong> <span>#{{ $order->order_number }}</span></p>
            <p><strong>Meja:</strong> <span>{{ $order->table_number }}</span></p>
            <p><strong>Pelanggan:</strong> <span>{{ $order->customer_name }}</span></p>
            <p><strong>Waktu:</strong> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></p>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th class="item-col">Item</th>
                        <th class="qty-col">Qty</th>
                        <th class="price-col">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td class="item-col">{{ $item->product->name }}</td>
                        <td class="qty-col">{{ $item->quantity }}</td>
                        <td class="price-col">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @if($item->notes)
                    <tr>
                        <td colspan="3" class="notes">Catatan: {{ $item->notes }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-item-row">
                        <td colspan="2" class="item-col">Total Item</td>
                        <td class="qty-col">{{ $order->orderItems->sum('quantity') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2" class="item-col">TOTAL</td>
                        <td class="price-col">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            <p>TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
            <p>Dicetak: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="no-print text-center margin-top-20">
        <div class="d-flex justify-content-center align-items-center gap-2">
            <button onclick="window.print()" class="custom-action-btn custom-action-btn-info" title="Cetak Lagi">
                <i class="fas fa-print"></i> <span>Cetak Lagi</span>
            </button>
            <button onclick="window.close()" class="custom-action-btn custom-action-btn-secondary" title="Tutup">
                <i class="fas fa-times"></i> <span>Tutup</span>
            </button>
        </div>
    </div>
</body>
</html>