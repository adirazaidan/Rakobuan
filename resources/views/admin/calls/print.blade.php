<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Panggilan #{{ $call->call_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body onload="window.print();">
    <div class="receipt">
        <div class="header">
            <h1>Warung Tekko Ramenten10 Lelabuan</h1>
            <p>Jalan S. Parman No. 2, Benua Melayu Darat,Pontianak</p>
            <p>Telp: 0811-826-008</p>
            <p>===============================</p>
            <p>DETAIL PANGGILAN</p>
        </div>

        <div class="details">
            <p><strong>No. Panggilan:</strong> <span>#{{ $call->call_number }}</span></p>
            <p><strong>Meja:</strong> <span>{{ $call->table_number }}</span></p>
            <p><strong>Pelanggan:</strong> <span>{{ $call->customer_name }}</span></p>
            <p><strong>Waktu Masuk:</strong> <span>{{ $call->created_at->format('d/m/Y H:i') }}</span></p>
            <p><strong>Catatan:</strong> <span>{{ $call->notes ?: '-' }}</span></p>
        </div>

        <div class="footer">
            <p>TERIMA KASIH</p>
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