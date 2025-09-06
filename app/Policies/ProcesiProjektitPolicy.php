<?php

namespace App\Policies;

use App\Models\ProcesiProjektit;
use App\Models\Projektet;
use App\Models\User;

class ProcesiProjektitPolicy
{
    /**
     * Determine whether the user can view the process list.
     */
    public function viewList(User $user, Projektet $projekt): bool
    {
        // Vetëm administratorët, menaxherët dhe stafi i caktuar në projekt mund ta shohin
        return $user->rol->emri_rolit === 'administrator' ||
               $user->rol->emri_rolit === 'menaxher' ||
               $projekt->mjeshtri_caktuar_id === $user->perdorues_id ||
               $projekt->montuesi_caktuar_id === $user->perdorues_id;
    }

    /**
     * Determine whether the user can view a specific process.
     */
    public function view(User $user, ProcesiProjektit $proces): bool
    {
        // Përdor të njëjtat rregulla si për listën
        return $this->viewList($user, $proces->projekt);
    }

    /**
     * Determine whether the user can create processes.
     */
    public function create(User $user, Projektet $projekt): bool
    {
        // Vetëm administratorët, menaxherët dhe mjeshtrat e caktuar mund të shtojnë procese
        return $user->rol->emri_rolit === 'administrator' ||
               $user->rol->emri_rolit === 'menaxher' ||
               $projekt->mjeshtri_caktuar_id === $user->perdorues_id;
    }
}
