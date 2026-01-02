<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminMembersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Member::with('organization')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Organization', 'Car Brand', 'Car Model', 'Car Plate', 'Status', 'Synced', 'Joined Date'];
    }

    public function map($member): array
    {
        return [
            $member->name,
            $member->email,
            $member->phone ?? '',
            $member->organization->name,
            $member->car_brand ?? '',
            $member->car_model ?? '',
            $member->car_plate ?? '',
            ucfirst($member->status),
            $member->synced_to_accounting ? 'Yes' : 'No',
            $member->created_at->format('Y-m-d')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
