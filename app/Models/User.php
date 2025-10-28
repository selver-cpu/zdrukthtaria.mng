<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    protected $table = 'perdoruesit';
    protected $primaryKey = 'perdorues_id';

    const CREATED_AT = 'data_krijimit';
    const UPDATED_AT = 'data_perditesimit';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'perdorues_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rol_id',
        'emri',
        'mbiemri',
        'email',
        'fjalekalimi_hash',
        'telefon',
        'adresa',
        'aktiv',
    ];
    
    /**
     * Merr rolin e përdoruesit.
     */
    public function role()
    {
        try {
            return $this->belongsTo(Role::class, 'rol_id', 'rol_id');
        } catch (\Exception $e) {
            try {
                return $this->belongsTo(Rolet::class, 'rol_id', 'rol_id');
            } catch (\Exception $e) {
                return null;
            }
        }
    }
    
    /**
     * Kontrollon nëse përdoruesi ka një rol të caktuar.
     *
     * @param string|array $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        // Check for role_id matching to admin/manager (1, 2)
        if ($roleName === 'administrator' || $roleName === 'admin') {
            // First check by rol_id, as a direct fallback
            if ($this->rol_id == 1) { // Admin role ID
                return true;
            }
        }

        // Check by name through relationship
        $current = $this->role ? strtolower(trim($this->role->emri_rolit)) : null;
        if (!$current) {
            // If we can't determine role name, check by ID for manager (for klientet.create)
            if (is_array($roleName) && in_array('menaxher', $roleName) && $this->rol_id == 2) {
                return true;
            }
            return false;
        }

        $normalize = function ($r) {
            return strtolower(trim($r));
        };

        $matches = function ($target) use ($current, $normalize) {
            $t = $normalize($target);
            if ($current === $t) {
                return true;
            }
            // handle common synonyms
            $synonyms = [
                'administrator' => ['admin'],
                'admin' => ['administrator']
            ];
            if (isset($synonyms[$current]) && in_array($t, $synonyms[$current], true)) {
                return true;
            }
            return false;
        };

        if (is_array($roleName)) {
            foreach ($roleName as $role) {
                if ($matches($role)) {
                    return true;
                }
            }
            return false;
        }

        return $matches($roleName);
    }

    /**
     * Kontrollon nëse përdoruesi ka leje për një veprim të caktuar në një modul
     *
     * @param string $module Moduli (p.sh. 'projektet', 'klientet', etj.)
     * @param string $action Veprimi (p.sh. 'view', 'create', 'edit', 'delete')
     * @return bool
     */
    public function hasPermission($module, $action)
    {
        $roleName = $this->role ? $this->role->emri_rolit : null;

        if (!$roleName) {
            return false;
        }

        // Administratortë kanë qasje të plotë
        if ($roleName === 'administrator') {
            return true;
        }

        // Definoj lejet për secilin rol
        $rolePermissions = [
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

        // Kontrollo nëse roli ekziston në lejet e përcaktuara
        if (!isset($rolePermissions[$roleName])) {
            return false;
        }

        // Kontrollo nëse moduli ekziston në lejet e rolit
        if (!isset($rolePermissions[$roleName][$module])) {
            return false;
        }

        // Kontrollo nëse veprimi është i lejuar për këtë modul
        return in_array($action, $rolePermissions[$roleName][$module]);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'fjalekalimi_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fjalekalimi_hash' => 'hashed',
        ];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->fjalekalimi_hash;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Get the name of the password field for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'fjalekalimi_hash';
    }

    /**
     * Get the name of the remember token field.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null; // We don't use remember tokens
    }

    // Relationships
    public function rol()
    {
        return $this->belongsTo(Rolet::class, 'rol_id', 'rol_id');
    }

    public function projekteSiMjesher()
    {
        return $this->hasMany(Projektet::class, 'mjeshtri_caktuar_id', 'perdorues_id');
    }

    public function projekteSiMontues()
    {
        return $this->hasMany(Projektet::class, 'montuesi_caktuar_id', 'perdorues_id');
    }

    public function proceseProjekti()
    {
        return $this->hasMany(ProcesiProjektit::class, 'perdorues_id', 'perdorues_id');
    }

    public function dokumente()
    {
        return $this->hasMany(DokumentetProjekti::class, 'perdorues_id', 'perdorues_id');
    }

    public function njoftimet()
    {
        return $this->hasMany(Njoftimet::class, 'perdorues_id', 'perdorues_id');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string ...$roles
     * @return bool
     */
    public function roliIs(...$roles)
    {
        if ($this->rol) {
            return in_array($this->rol->emri_rolit, $roles);
        }
        return false;
    }

    public function ditarVeprime()
    {
        return $this->hasMany(DitarVeprimet::class, 'perdorues_id', 'perdorues_id');
    }
}
