<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RolePermissions
{
    /**
     * Lejet e përcaktuara për secilin rol
     */
    private static $rolePermissions = [
        'administrator' => [
            'dashboard' => ['view', 'edit'],
            'projektet' => ['view', 'create', 'edit', 'delete'],
            'klientet' => ['view', 'create', 'edit', 'delete'],
            'materialet' => ['view', 'create', 'edit', 'delete'],
            'stafi' => ['view', 'create', 'edit', 'delete'],
            'dokumentet' => ['view', 'create', 'edit', 'delete'],
            'raportet' => ['view', 'export'],
            'settings' => ['view', 'edit'],
            'users' => ['view', 'create', 'edit', 'delete'],
        ],
        
        'menaxher' => [
            'dashboard' => ['view'],
            'projektet' => ['view', 'create', 'edit'],
            'klientet' => ['view', 'create', 'edit'],
            'materialet' => ['view', 'create'],
            'stafi' => ['view', 'assign'],
            'dokumentet' => ['view', 'create', 'edit'],
            'raportet' => ['view', 'export'],
            'settings' => ['view'],
        ],
        
        'mjeshtër' => [
            'dashboard' => ['view'],
            'projektet' => ['view', 'update_status'],
            'materialet' => ['view', 'request'],
            'dokumentet' => ['view', 'create'],
            'raportet' => ['view'],
        ],
        
        'montues' => [
            'dashboard' => ['view'],
            'projektet' => ['view', 'update_status'],
            'dokumentet' => ['view'],
        ],
    ];

    /**
     * Kontrollon nëse përdoruesi aktual ka leje për një veprim të caktuar në një modul
     *
     * @param string $module Moduli (p.sh. 'projektet', 'klientet', etj.)
     * @param string $action Veprimi (p.sh. 'view', 'create', 'edit', 'delete')
     * @return bool
     */
    public static function can($module, $action)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $roleName = $user->role ? $user->role->emri_rolit : null;

        if (!$roleName) {
            return false;
        }

        // Administratortë kanë qasje të plotë
        if ($roleName === 'administrator') {
            return true;
        }

        // Kontrollo nëse roli ekziston në lejet e përcaktuara
        if (!isset(self::$rolePermissions[$roleName])) {
            return false;
        }

        // Kontrollo nëse moduli ekziston në lejet e rolit
        if (!isset(self::$rolePermissions[$roleName][$module])) {
            return false;
        }

        // Kontrollo nëse veprimi është i lejuar për këtë modul
        return in_array($action, self::$rolePermissions[$roleName][$module]);
    }

    /**
     * Kontrollon nëse përdoruesi aktual ka ndonjë nga lejet e specifikuara
     *
     * @param array $permissions Array me leje në formatin [['module' => 'action'], ...]
     * @return bool
     */
    public static function canAny(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (isset($permission['module']) && isset($permission['action'])) {
                if (self::can($permission['module'], $permission['action'])) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
