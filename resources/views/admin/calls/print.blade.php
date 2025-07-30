<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Panggilan #{{ $call->call_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .receipt {
            width: 300px;
            margin: 0 auto;
            padding: 15px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .details {
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        .details p {
            margin: 2px 0;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
        }
        .details p strong {
            flex-basis: 45%;
            text-align: left;
        }
        .details p span {
            flex-basis: 55%;
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .receipt {
                width: 100%;
                margin: 0;
                padding: 5mm;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
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

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cetak Lagi</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>
</body>
</html>