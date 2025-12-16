<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .period {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
            background: #f5f5f5;
            padding: 8px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #09090b;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .items-cell {
            font-size: 9px;
            max-width: 200px;
        }
        .grand-total {
            margin-top: 10px;
            padding: 10px;
            background: #09090b;
            color: white;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BAKMI JOWO PALUR</h1>
        <p>Laporan Transaksi Harian</p>
    </div>

    <div class="period">
        <strong>Periode:</strong> {{ $startDate }} - {{ $endDate }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 60px;">Invoice</th>
                <th style="width: 80px;">Pelanggan</th>
                <th>Item</th>
                <th class="text-right" style="width: 80px;">Total</th>
                <th style="width: 50px;">Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ strtoupper(substr($transaction->uuid ?? $transaction->id, 0, 8)) }}</td>
                    <td>{{ $transaction->customer_name }}</td>
                    <td class="items-cell">
                        @foreach($transaction->items->where('status', 'active') as $item)
                            {{ $item->menu->name ?? 'Item' }} x{{ $item->quantity }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    <td>{{ strtoupper($transaction->payment_method ?? '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="grand-total">
        GRAND TOTAL: Rp {{ number_format($grandTotal, 0, ',', '.') }}
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }} WIB
    </div>
</body>
</html>
