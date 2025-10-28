<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjektetDimensions extends Model
{
    use HasFactory;

    protected $table = 'projektet_dimensions';

    protected $fillable = [
        'projekt_id',
        'emri_pjeses',
        'gjatesia',
        'gjeresia',
        'trashesia',
        'njesi_matese',
        'sasia',
        'materiali_id',
        'materiali_personal',
        'kantim_needed',
        'kantim_type',
        'kantim_thickness',
        'kantim_front',
        'kantim_back',
        'kantim_left',
        'kantim_right',
        'kantim_corners',
        'barcode',
        'qr_code',
        'statusi_prodhimit',
        'workstation_current',
        'plc_ticket_printed',
        'pershkrimi',
        'krijues_id',
    ];

    protected $casts = [
        'gjatesia' => 'decimal:2',
        'gjeresia' => 'decimal:2',
        'trashesia' => 'decimal:2',
        'kantim_thickness' => 'decimal:2',
        'kantim_needed' => 'boolean',
        'kantim_front' => 'boolean',
        'kantim_back' => 'boolean',
        'kantim_left' => 'boolean',
        'kantim_right' => 'boolean',
        'plc_ticket_printed' => 'boolean',
    ];

    /**
     * Marrëdhëniet me tabelat e tjera
     */
    public function projekt()
    {
        return $this->belongsTo(Projektet::class, 'projekt_id');
    }

    public function materiali()
    {
        return $this->belongsTo(Materialet::class, 'materiali_id', 'material_id');
    }

    public function krijues()
    {
        return $this->belongsTo(User::class, 'krijues_id');
    }

    /**
     * Llogarit sipërfaqen totale të pjesës
     */
    public function siperfaqjaTotale()
    {
        return $this->gjatesia * $this->gjeresia * $this->sasia;
    }

    /**
     * Llogarit sasinë e materialit të nevojshëm
     */
    public function sasiaMaterialitNevojitur()
    {
        // Konverto në metra katrorë
        $siperfaqja = $this->siperfaqjaTotale() / 1000000; // mm² në m²
        $material = $this->materiali ?: Materialet::find($this->materiali_id);

        if (!$material) {
            // Nëse materiali është personal, supozo sasi sipas m² si default
            return $siperfaqja;
        }

        switch (strtolower($material->njesia_matese)) {
            case 'm²':
            case 'm2':
                return $siperfaqja; // m²
            case 'm³':
            case 'm3':
                $trashesi_m = $this->trashesia / 1000; // mm në m
                return $siperfaqja * $trashesi_m; // m³
            case 'copë':
            case 'cope':
            case 'pcs':
                return (float) $this->sasia; // copë
            default:
                // Default: përdor m²
                return $siperfaqja;
        }
    }

    /**
     * Kontrollon nëse ka stok të mjaftueshëm
     */
    public function kaStokTeMjaftueshem()
    {
        if (!$this->materiali) {
            return true; // Material personal, nuk kontrollohet stoku
        }

        return $this->materiali->kaStokTeMjaftueshem($this->sasiaMaterialitNevojitur());
    }

    /**
     * Rezervon materialin për këtë pjesë
     */
    public function rezervoMaterialin()
    {
        if ($this->materiali && $this->sasiaMaterialitNevojitur() > 0) {
            $this->materiali->rezervoSasi($this->sasiaMaterialitNevojitur());
        }
    }

    /**
     * Liron rezervimin e materialit
     */
    public function liroRezervimin()
    {
        if ($this->materiali && $this->sasiaMaterialitNevojitur() > 0) {
            $this->materiali->liroRezervimin($this->sasiaMaterialitNevojitur());
        }
    }

    /**
     * Përditëson stokun pas përdorimit
     */
    public function perditesoStokun()
    {
        if ($this->materiali && $this->sasiaMaterialitNevojitur() > 0) {
            $this->materiali->perditesoStokun($this->sasiaMaterialitNevojitur());
        }
    }

    /**
     * Gjeneron barcode për pjesën
     */
    public function gjeneroBarcode()
    {
        // Gjenero barcode unik pa varësi nga ID-ja (eviton ruajtje gjatë creating)
        try {
            $rand = random_int(1000, 9999);
        } catch (\Exception $e) {
            $rand = mt_rand(1000, 9999);
        }
        $this->barcode = 'DIM' . time() . $rand;
    }

    /**
     * Merr anët e kantimit si string
     */
    public function anetEKantimit()
    {
        $anet = [];
        if ($this->kantim_front) $anet[] = 'Përpara';
        if ($this->kantim_left) $anet[] = 'Majtas';
        if ($this->kantim_right) $anet[] = 'Djathtas';
        if ($this->kantim_back) $anet[] = 'Pas'; // Pas në fund sepse përdoret më pak

        return implode(', ', $anet);
    }

    /**
     * Merr tekstin për PLC ticket
     */
    public function tekstiPerPLC()
    {
        $material = $this->materiali ? $this->materiali->emri_materialit : $this->materiali_personal;
        $kantim = $this->anetEKantimit();

        return [
            'project' => $this->projekt?->klient?->emri_klientit ?? 'Klienti',
            'part' => $this->emri_pjeses,
            'barcode' => $this->barcode,
            'dimensions' => $this->gjatesia . 'x' . $this->gjeresia . 'x' . $this->trashesia . $this->njesi_matese,
            'material' => $material,
            'edge_banding' => $kantim ?: 'Pa kantim',
            'quantity' => $this->sasia,
            'workstation' => $this->workstation_current ?: 'WORK-01'
        ];
    }

    /**
     * Event listeners
     */
    protected static function booted()
    {
        static::creating(function ($dimension) {
            if (empty($dimension->barcode)) {
                $dimension->gjeneroBarcode();
            }
        });

        static::created(function ($dimension) {
            $dimension->rezervoMaterialin();
        });

        static::updating(function ($dimension) {
            // Liro rezervimin e vjetër
            if ($dimension->isDirty('materiali_id') || $dimension->isDirty('gjatesia') || $dimension->isDirty('gjeresia') || $dimension->isDirty('sasia')) {
                $sasiaVjeter = $dimension->getOriginal('gjatesia') * $dimension->getOriginal('gjeresia') * $dimension->getOriginal('sasia') / 1000000 * $dimension->getOriginal('trashesia');
                $materialVjeter = $dimension->materiali;

                if ($materialVjeter && $sasiaVjeter > 0) {
                    $materialVjeter->liroRezervimin($sasiaVjeter);
                }
            }
        });

        static::updated(function ($dimension) {
            $dimension->rezervoMaterialin();
        });

        static::deleted(function ($dimension) {
            if ($dimension->materiali && $dimension->sasiaMaterialitNevojitur() > 0) {
                $dimension->materiali->liroRezervimin($dimension->sasiaMaterialitNevojitur());
            }
        });
    }
}
