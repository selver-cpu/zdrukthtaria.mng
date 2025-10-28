# 🎫 PLC Ticket Layout Editor - Udhëzues i Shpejtë

## ✅ Çfarë u Implementua

Një sistem i plotë për **editimin e layout-it të tiketave PLC** që është i ngjashëm me aplikacionin Python `zdrkthtari-MNG`, por i adaptuar për Laravel.

---

## 🎯 Karakteristikat

### 1. **Layout Editor i Konfigurueshëm**
- ✅ Ndrysho madhësinë e tiketës (width × height në mm)
- ✅ Zgjidh orientimin (Landscape/Portrait)
- ✅ Aktivizo/Çaktivizo logo
- ✅ Kontrollo cilat fusha shfaqen
- ✅ Live preview në kohë reale

### 2. **Ruajtja e Konfigurimit**
- ✅ Konfig ruhet në `storage/app/ticket_layout_config.json`
- ✅ Nuk preket databaza
- ✅ Mund të kthehet në default

### 3. **Qasja e Kufizuar**
- ✅ Vetëm **Administrator** (rol_id=1) dhe **Menaxher** (rol_id=2) mund të editojnë
- ✅ Të tjerët nuk e shohin butonin

---

## 🚀 Si të Përdoret

### Hapi 1: Hap Editor-in

1. Shko te **Dimensionet** (`/dimensionet`)
2. Në header, kliko butonin **"Ticket Layout"** (blu)
3. Do të hapesh faqja e editor-it

### Hapi 2: Edito Layout-in

**Cilësimet e Përgjithshme:**
- **Emri i Kompanisë:** Emri që shfaqet në tiketë
- **Madhësia e Tiketës:** Gjerësia dhe lartësia në mm (default: 100×75mm)
- **Orientimi:** Landscape (horizontal) ose Portrait (vertikal)
- **Shfaq Logo:** Aktivizo/çaktivizo logon
- **Lartësia e Logos:** Sa e madhe të jetë logoja (3-20mm)

**Fushat e Vizueshme:**
- ☑️ Projekti
- ☑️ Emri i Pjesës
- ☑️ Dimensionet
- ☑️ Materiali
- ☑️ Kantimi
- ☑️ Data

### Hapi 3: Ruaj Ndryshimet

1. Kliko **"Ruaj Ndryshimet"** (buton jeshil)
2. Do të shfaqet mesazh suksesi
3. Ndryshimet aplikohen menjëherë në të gjitha tiketa

---

## 📐 Struktura e Layout-it

### Layout Aktual (100mm × 75mm):

```
┌─────────────────────────────────────────────┐
│                                    [LOGO]   │
│                                             │
│  ┌──────────────┐                           │
│  │   SVG        │  Projekti: Kuzhina        │
│  │   Diagram    │  Pjesa: Panel Anësor      │
│  │   Kantim     │  Dimensionet: 720×600×18  │
│  └──────────────┘  Materiali: Melaminë      │
│                    Kantimi: PVC 0.8mm       │
│                    Data: 20/10/2025         │
│                                             │
│  ColiDecor | ID: 123                        │
└─────────────────────────────────────────────┘
```

### Elementet:
1. **Logo** - Top-right (opsionale)
2. **SVG Diagram** - Vizualizon kantimin
3. **Tekstet** - Informacioni i pjesës
4. **Footer** - Kompania dhe ID

---

## 🔧 Files të Krijuara

```
carpentry-app/
├── app/Http/Controllers/
│   └── TicketLayoutController.php          # Controller për editor
├── resources/views/ticket-layout/
│   └── editor.blade.php                    # View për editor
├── routes/web.php                          # Routes (shtuar)
└── storage/app/
    └── ticket_layout_config.json           # Config file (krijohet automatikisht)
```

---

## 🎨 Integrimi me Tiketa Ekzistuese

Tiketa aktuale në `projektet-dimensions/ticket.blade.php` mund të lexojë konfigurimin:

```php
// Në controller
$config = Storage::disk('local')->exists('ticket_layout_config.json')
    ? json_decode(Storage::disk('local')->get('ticket_layout_config.json'), true)
    : $defaultConfig;

return view('projektet-dimensions.ticket', compact('dimension', 'config'));
```

---

## 🔐 Siguria

### Kontrolli i Qasjes:

```php
// Në TicketLayoutController
if (!in_array(auth()->user()->rol_id, [1, 2])) {
    return redirect()->route('dashboard')
        ->with('error', 'Vetëm administratori dhe menaxheri...');
}
```

### Kush Ka Qasje:
- ✅ **Administrator** (rol_id=1) - Qasje e plotë + Reset
- ✅ **Menaxher** (rol_id=2) - Qasje e plotë
- ❌ **Disajnere** (rol_id=5) - Pa qasje
- ❌ **Mjeshtër** (rol_id=3) - Pa qasje
- ❌ **Montues** (rol_id=4) - Pa qasje

---

## 📊 Krahasimi me Aplikacionin Python

| Feature | Python App | Laravel App | Status |
|---------|-----------|-------------|--------|
| Layout Editor | ✅ | ✅ | Implementuar |
| Live Preview | ✅ | ✅ | Implementuar |
| JSON Config | ✅ | ✅ | Implementuar |
| Drag & Drop | ✅ | ⏳ | Për në të ardhmen |
| Element Rotation | ✅ | ⏳ | Për në të ardhmen |
| Logo Upload | ✅ | ⏳ | Për në të ardhmen |
| Multiple Templates | ❌ | ⏳ | Për në të ardhmen |

---

## 🚀 Hapat e Ardhshëm (Opsionale)

### Faza 2 - Përmirësime:
1. **Drag & Drop Positioning** - Lëviz elementet me mouse
2. **Element Rotation** - Rrotullim i teksteve
3. **Font Size Control** - Kontrollo madhësinë e fontit për çdo element
4. **Logo Upload** - Ngarko logo direkt nga editor

### Faza 3 - Advanced:
1. **Multiple Templates** - Ruaj template të ndryshme
2. **Template Library** - Galeri me template të gatshme
3. **Export/Import Config** - Shpërndaj konfigurime
4. **Preview me Dimension ID** - Testo me të dhëna reale

---

## 🐛 Troubleshooting

### Problem: Butoni nuk shfaqet
**Zgjidhja:** Kontrollo që je i loguar si Admin ose Menaxher (rol_id 1 ose 2)

### Problem: Ndryshimet nuk ruhen
**Zgjidhja:** 
```bash
# Kontrollo permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

### Problem: Config file nuk krijohet
**Zgjidhja:**
```bash
# Krijo manualisht
touch storage/app/ticket_layout_config.json
sudo chown www-data:www-data storage/app/ticket_layout_config.json
```

---

## 📝 Routes të Reja

```php
// Ticket Layout Editor
GET  /ticket-layout              → Editor page
POST /ticket-layout/update       → Ruaj konfig
POST /ticket-layout/reset        → Reset në default
GET  /ticket-layout/config       → Merr konfig (AJAX)
GET  /ticket-layout/preview      → Preview me të dhëna reale
```

---

## ✅ Përfundim

Sistemi është **gati për përdorim**! 

**Avantazhet:**
- ✅ Nuk preket databaza
- ✅ Konfig i lehtë për backup
- ✅ Live preview
- ✅ Qasje e kufizuar
- ✅ I ngjashëm me aplikacionin Python

**Përdorimi:**
1. Shko te Dimensionet
2. Kliko "Ticket Layout"
3. Edito dhe ruaj
4. Gëzuar! 🎉

---

*Zhvilluar: 20 Tetor 2025*  
*Version: 1.0*  
*Bazuar në: zdrkthtari-MNG Python App*
