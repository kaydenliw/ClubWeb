<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    public function collection()
    {
        return Member::where('organization_id', $this->organizationId)
            ->withCount('charges')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Car Brand',
            'Car Model',
            'Car Plate',
            'Status',
            'Charges Count',
            'Joined Date'
        ];
    }

    public function map($member): array
    {
        return [
            $member->name,
            $member->email,
            $member->phone ?? '',
            $member->car_brand ?? '',
            $member->car_model ?? '',
            $member->car_plate ?? '',
            ucfirst($member->status),
            $member->charges_count,
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
