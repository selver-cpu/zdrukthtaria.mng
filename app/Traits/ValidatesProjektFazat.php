<?php

namespace App\Traits;

use App\Models\FazatProjekti;
use App\Models\ProjektFazaPune;
use Illuminate\Support\Collection;

trait ValidatesProjektFazat
{
    /**
     * Kontrollo nëse faza e re është në rendin e duhur.
     *
     * @param int $projektId
     * @param int $fazaId
     * @return bool
     */
    protected function validateFazaOrder(int $projektId, int $fazaId): bool
    {
        // Merr të gjitha fazat e projektit të renditura sipas renditjes
        $projektFazat = ProjektFazaPune::where('projekt_id', $projektId)
            ->join('fazat_projekti', 'projekt_faza_pune.faza_id', '=', 'fazat_projekti.id')
            ->orderBy('fazat_projekti.renditja')
            ->get();

        // Nëse nuk ka faza ekzistuese, çdo fazë është e vlefshme
        if ($projektFazat->isEmpty()) {
            return true;
        }

        // Merr fazën që po shtohet
        $fazaReRenditjes = FazatProjekti::find($fazaId);
        if (!$fazaReRenditjes) {
            return false;
        }

        // Merr fazën e fundit të përfunduar
        $fazaFundit = $projektFazat->last();
        
        // Kontrollo nëse faza e re është në rendin e duhur
        // Faza e re duhet të jetë menjëherë pas fazës së fundit të përfunduar
        return $fazaReRenditjes->renditja === ($fazaFundit->renditja + 1);
    }

    /**
     * Merr fazën e radhës që duhet të kryhet për projektin.
     *
     * @param int $projektId
     * @return FazatProjekti|null
     */
    protected function getFazaRadhes(int $projektId): ?FazatProjekti
    {
        // Merr të gjitha fazat e mundshme të renditura
        $teFazat = FazatProjekti::orderBy('renditja')->get();
        
        // Merr fazat e përfunduara të projektit
        $fazatPerfunduara = ProjektFazaPune::where('projekt_id', $projektId)
            ->pluck('faza_id');

        // Gjej fazën e parë që nuk është përfunduar ende
        return $teFazat->first(function ($faza) use ($fazatPerfunduara) {
            return !$fazatPerfunduara->contains($faza->id);
        });
    }
}
