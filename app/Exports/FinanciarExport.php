<?php

namespace App\Exports;

use App\Models\Projektet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanciarExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'ID e Projektit',
            'Emri i Projektit',
            'Klienti',
            'Statusi',
            'Te Ardhurat',
            'Shpenzimet',
            'Fitimi',
            'Data e Fillimit',
            'Data e Përfundimit',
            'Data e Krijimit'
        ];
    }

    public function map($projekt): array
    {
        $shpenzimet = $projekt->projektMateriale->sum(function($pm) {
            return $pm->sasia_perdorur * ($pm->material->cmimi_per_njesi ?? 0);
        });
        
        $fitimi = $projekt->cmimi_total - $shpenzimet;

        return [
            $projekt->projekt_id,
            $projekt->emri_projektit,
            $projekt->klient->emri_klientit ?? 'N/A',
            $projekt->statusi_projektit->emri_statusit ?? 'N/A',
            number_format($projekt->cmimi_total, 2) . ' €',
            number_format($shpenzimet, 2) . ' €',
            number_format($fitimi, 2) . ' €',
            $projekt->data_fillimit_parashikuar,
            $projekt->data_perfundimit_parashikuar,
            $projekt->data_krijimit
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:J1' => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ]
            ]
        ];
    }
}
