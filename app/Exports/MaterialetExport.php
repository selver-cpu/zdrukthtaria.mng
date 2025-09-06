<?php

namespace App\Exports;

use App\Models\ProjektMateriale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialetExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'ID e Materialit',
            'Emri i Materialit',
            'Sasia e Përdorur',
            'Njësia Matëse',
            'Projekti',
            'Data e Përdorimit'
        ];
    }

    public function map($material): array
    {
        return [
            $material->material_id,
            $material->material->emri_materialit ?? 'N/A',
            $material->sasia_perdorur,
            $material->material->njesia_matese ?? 'N/A',
            $material->projekt->emri_projektit ?? 'N/A',
            $material->created_at
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
