<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;  // <--- This was missing
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Carbon $startDate;
    protected Carbon $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Transaction::with(['user', 'items'])
            ->whereBetween('created_at', [
                $this->startDate->startOfDay(), 
                $this->endDate->endOfDay()
            ])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Jam',
            'Kasir',
            'Customer',
            'Metode Pembayaran',
            'Total (Rp)',
            'Status',
        ];
    }

    public function map($transaction): array
    {
        return [
            '#' . $transaction->id,
            $transaction->created_at->format('d/m/Y'),
            $transaction->created_at->format('H:i'),
            $transaction->user->name ?? 'Unknown',
            $transaction->customer_name ?? '-',
            strtoupper($transaction->payment_method),
            $transaction->total_amount,
            $transaction->status ?? 'paid',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}