<?php

namespace App\Exports;

use App\Models\ProjektetDimensions;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProjektetDimensionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /** @var \Illuminate\Support\Collection */
    protected $dimensions;

    public function __construct($dimensions)
    {
        $this->dimensions = $dimensions;
    }

    public function collection()
    {
        return $this->dimensions;
    }

    public function headings(): array
    {
        return [
            'Projekti',
            'Emri i pjesës',
            'Gjatësia (mm)',
            'Gjerësia (mm)',
            'Trashësia (mm)',
            'Njësia Matëse',
            'Sasia',
            'Materiali',
            'Kërkohet Kantim',
            'Lloji i Kantimit',
            'Trashësia e Kantimit (mm)',
            'Qoshet',
            'Përpara',
            'Pas',
            'Majtas',
            'Djathtas',
            'Statusi i Prodhimit',
            'Workstation',
            'Përshkrimi',
        ];
    }

    public function map($dim): array
    {
        $materialName = $dim->materiali ? $dim->materiali->emri_materialit : ($dim->materiali_personal ?: '-');
        $bool = fn($v) => $v ? 'Po' : 'Jo';

        return [
            optional($dim->projekt)->emri_projektit,
            $dim->emri_pjeses,
            (string)$dim->gjatesia,
            (string)$dim->gjeresia,
            (string)$dim->trashesia,
            $dim->njesi_matese,
            (int)$dim->sasia,
            $materialName,
            $bool($dim->kantim_needed),
            $dim->kantim_type,
            $dim->kantim_thickness,
            $dim->kantim_corners,
            $bool($dim->kantim_front),
            $bool($dim->kantim_back),
            $bool($dim->kantim_left),
            $bool($dim->kantim_right),
            $dim->statusi_prodhimit,
            $dim->workstation_current,
            $dim->pershkrimi,
        ];
    }
}
