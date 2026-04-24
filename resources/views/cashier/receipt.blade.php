<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f3f4f6;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
        }
        .receipt {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .total-section {
            border-top: 2px dashed #ccc;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #6b7280;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 2rem;
            padding: 0.75rem;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-family: sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>KasirPro</h1>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">Jl. Contoh Kasir No. 123</p>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">{{ date('d M Y H:i', strtotime($order->created_at)) }}</p>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">Order: {{ $order->order_number }}</p>
        </div>

        <div class="items">
            @foreach($order->items as $item)
            <div class="item">
                <span style="flex: 2;">{{ $item->menu->name }}</span>
                <span style="flex: 1; text-align: center;">x{{ $item->quantity }}</span>
                <span style="flex: 1; text-align: right;">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            @if(session('cash_amount'))
            <div class="item" style="margin-top: 0.5rem;">
                <span>Cash</span>
                <span>Rp {{ number_format(session('cash_amount'), 0, ',', '.') }}</span>
            </div>
            <div class="item">
                <span>Change (Kembalian)</span>
                <span>Rp {{ number_format(session('change_amount'), 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Terima kasih atas kunjungan Anda!</p>
        </div>

        <a href="{{ route('cashier.index') }}" class="btn-back no-print">Kembali ke Kasir</a>
    </div>

    <script>
        // Otomatis memicu print saat halaman dibuka
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
