<?php

namespace App\Exports;

use App\Models\Organization;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminOrganizationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Organization::withCount('members')->get();
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Address', 'Members Count', 'Status', 'Created Date'];
    }

    public function map($organization): array
    {
        return [
            $organization->name,
            $organization->email,
            $organization->phone ?? '',
            $organization->address ?? '',
            $organization->members_count,
            ucfirst($organization->status),
            $organization->created_at->format('Y-m-d')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
