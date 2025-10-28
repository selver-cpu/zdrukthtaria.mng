<?php

// Funksion ndihmës për të kontrolluar lejet
if (!function_exists('can')) {
    /**
     * Kontrollon nëse përdoruesi aktual ka leje për një veprim të caktuar në një modul
     *
     * @param string $module Moduli (p.sh. 'projektet', 'klientet', etj.)
     * @param string $action Veprimi (p.sh. 'view', 'create', 'edit', 'delete')
     * @return bool
     */
    function can($module, $action)
    {
        $user = auth()->user();
        return $user ? $user->hasPermission($module, $action) : false;
    }
}

// Funksion ndihmës për të kontrolluar nëse përdoruesi ka ndonjë nga lejet e specifikuara
if (!function_exists('canany')) {
    /**
     * Kontrollon nëse përdoruesi aktual ka ndonjë nga lejet e specifikuara
     *
     * @param array $permissions Array me leje në formatin [['module' => 'action'], ...]
     * @return bool
     */
    function canany(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (isset($permission['module']) && isset($permission['action'])) {
                if (can($permission['module'], $permission['action'])) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

// Funksion ndihmës për të kontrolluar nëse përdoruesi ka një rol të caktuar
if (!function_exists('hasRole')) {
    /**
     * Kontrollon nëse përdoruesi aktual ka një rol të caktuar
     *
     * @param string|array $roleName Emri i rolit ose array me emra rolesh
     * @return bool
     */
    function hasRole($roleName)
    {
        $user = auth()->user();
        return $user ? $user->hasRole($roleName) : false;
    }
}

// Regjistro direktivat Blade për kontrollin e qasjes - do të regjistrohen në një service provider
// për të shmangur problemet me inicializimin e aplikacionit
