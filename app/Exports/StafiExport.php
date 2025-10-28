<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StafiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $mjeshtrat;
    protected $montuesit;
    protected $data;

    public function __construct($mjeshtrat, $montuesit = null)
    {
        $this->mjeshtrat = $mjeshtrat;
        $this->montuesit = $montuesit ?? $mjeshtrat;
        $this->data = collect($this->mjeshtrat)->merge($this->montuesit);
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Emri i Plotë',
            'Roli',
            'Email',
            'Telefon',
            'Numri i Projekteve',
            'Projekte të Përfunduara',
            'Data e Regjistrimit'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->emri . ' ' . $user->mbiemri,
            $user->rol->emri_rolit ?? 'N/A',
            $user->email,
            $user->telefon ?? 'N/A',
            $user->projekteSiMjesher_count ?? $user->projekteSiMontues_count ?? 0,
            $user->projekte_perfunduar ?? 0,
            $user->created_at
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
