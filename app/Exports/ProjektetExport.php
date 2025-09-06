<?php

namespace App\Exports;

use App\Models\Projektet;

class ProjektetExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Emri i Projektit',
            'Klienti',
            'Statusi',
            'Mjeshtri',
            'Montuesi',
            'Data e Fillimit',
            'Data e Përfundimit',
            'Data e Krijimit'
        ];
    }

    public function map($projekt): array
    {
        return [
            $projekt->projekt_id,
            $projekt->emri_projektit,
            $projekt->klient->emri_klientit ?? 'N/A',
            $projekt->statusi_projektit->emri_statusit ?? 'N/A',
            $projekt->mjeshtri ? $projekt->mjeshtri->emri . ' ' . $projekt->mjeshtri->mbiemri : 'N/A',
            $projekt->montuesi ? $projekt->montuesi->emri . ' ' . $projekt->montuesi->mbiemri : 'N/A',
            $projekt->data_fillimit_parashikuar,
            $projekt->data_perfundimit_parashikuar,
            $projekt->data_krijimit
        ];
    }
}
