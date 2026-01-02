<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Transaction::with(['organization', 'member'])->get();
    }

    public function headings(): array
    {
        return ['Transaction ID', 'Organization', 'Member', 'Type', 'Amount', 'Status', 'Payment Method', 'Synced', 'Date'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_id,
            $transaction->organization->name,
            $transaction->member->name,
            ucfirst($transaction->type),
            'RM ' . number_format($transaction->amount, 2),
            ucfirst($transaction->status),
            $transaction->payment_method ? ucfirst(str_replace('_', ' ', $transaction->payment_method)) : '-',
            $transaction->synced_to_accounting ? 'Yes' : 'No',
            $transaction->created_at->format('Y-m-d H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
