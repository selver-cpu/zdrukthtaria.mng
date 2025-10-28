<?php

namespace App\Policies;

use App\Models\Projektet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjektPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Të gjithë përdoruesit mund të shohin listën e projekteve
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Projektet $projektet): bool
    {
        // Menaxheri mund të shohë të gjitha projektet
        if ($user->rol_id === 2) { // 2 = menaxher
            return true;
        }

        // Mjeshtri dhe montuesi mund të shohin vetëm projektet ku janë caktuar
        return $projektet->mjeshtri_caktuar_id === $user->perdorues_id || 
               $projektet->montuesi_caktuar_id === $user->perdorues_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Administratoret dhe menaxheret mund të krijojnë projekte
        return in_array($user->rol_id, [1, 2]); // 1 = admin, 2 = menaxher
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Projektet $projektet): bool
    {
        // Administratoret dhe menaxherët mund të përditësojnë çdo projekt
        if (in_array($user->rol_id, [1, 2])) { // 1 = admin, 2 = menaxher
            return true;
        }
        
        // Mjeshtri dhe montuesi mund të përditësojnë vetëm projektet ku janë caktuar
        if (in_array($user->rol_id, [3, 4])) { // 3 = mjeshtër, 4 = montues
            return $projektet->mjeshtri_caktuar_id === $user->perdorues_id || 
                   $projektet->montuesi_caktuar_id === $user->perdorues_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Projektet $projektet): bool
    {
        // Vetëm menaxheri mund të fshijë projekte
        return $user->rol_id === 2;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Projektet $projektet): bool
    {
        // Vetëm menaxheri mund të rikthejë projekte të fshira
        return $user->rol_id === 2;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Projektet $projektet): bool
    {
        // Vetëm menaxheri mund të fshijë përfundimisht projekte
        return $user->rol_id === 2;
    }

    /**
     * Përcakto nëse përdoruesi mund të caktojë mjeshtrin ose montuesin.
     */
    public function caktoStafin(User $user): bool
    {
        // Vetëm menaxheri mund të caktojë mjeshtrin dhe montuesin
        return $user->rol_id === 2;
    }

    /**
     * Përcakto nëse përdoruesi mund të ndryshojë statusin e fazës.
     */
    public function ndryshoProjektFaze(User $user, Projektet $projektet): bool
    {
        // Menaxheri mund të ndryshojë çdo fazë
        if ($user->rol_id === 2) {
            return true;
        }

        // Mjeshtri dhe montuesi mund të ndryshojnë fazat vetëm për projektet ku janë caktuar
        return $projektet->mjeshtri_caktuar_id === $user->perdorues_id || 
               $projektet->montuesi_caktuar_id === $user->perdorues_id;
    }
}
