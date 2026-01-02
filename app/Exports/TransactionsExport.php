<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    public function collection()
    {
        return Transaction::where('organization_id', $this->organizationId)
            ->with('member')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Member Name',
            'Member Email',
            'Type',
            'Amount (RM)',
            'Status',
            'Payment Method',
            'Date'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->member->name ?? '-',
            $transaction->member->email ?? '-',
            ucfirst($transaction->type),
            number_format($transaction->amount, 2),
            ucfirst($transaction->status),
            $transaction->payment_method ?? '-',
            $transaction->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
