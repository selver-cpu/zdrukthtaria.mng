<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class TicketLayoutController extends Controller
{
    protected $configFile = 'ticket_layout_config.json';

    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Shfaq editor-in e layout-it
     */
    public function index()
    {
        // Vetëm Admin dhe Menaxher mund të editojnë layout-in
        if (!in_array(auth()->user()->rol_id, [1, 2])) {
            return redirect()->route('dashboard')
                ->with('error', 'Vetëm administratori dhe menaxheri mund të editojnë layout-in e tiketave.');
        }

        $config = $this->loadConfig();
        
        return view('ticket-layout.editor-simple', compact('config'));
    }

    /**
     * Ngarkon konfigurimin nga storage
     */
    protected function loadConfig()
    {
        $path = storage_path('app/' . $this->configFile);
        
        if (file_exists($path)) {
            $json = file_get_contents($path);
            return json_decode($json, true);
        }

        // Default config nëse nuk ekziston
        return $this->getDefaultConfig();
    }

    /**
     * Konfigurimi default
     */
    protected function getDefaultConfig()
    {
        return [
            'company_name' => config('app.name', 'ColiDecor'),
            'ticket_width_mm' => 100,
            'ticket_height_mm' => 75,
            'orientation' => 'landscape',
            'show_logo' => true,
            'logo_height_mm' => 6,
            'logo_rotation' => 0,
            'print_offset_x' => 0,
            'print_offset_y' => 0,
            'elements' => [
                'logo' => [
                    'x' => 2,
                    'y' => 2,
                    'width' => 20,
                    'height' => 6,
                    'visible' => true,
                ],
                'svg_diagram' => [
                    'x' => 5,
                    'y' => 10,
                    'width' => 35,
                    'height' => 20,
                    'visible' => true,
                ],
                'project' => [
                    'x' => 5,
                    'y' => 32,
                    'font_size' => 7,
                    'visible' => true,
                ],
                'part_name' => [
                    'x' => 5,
                    'y' => 37,
                    'font_size' => 7,
                    'visible' => true,
                ],
                'dimensions' => [
                    'x' => 5,
                    'y' => 42,
                    'font_size' => 9,
                    'visible' => true,
                ],
                'material' => [
                    'x' => 5,
                    'y' => 48,
                    'font_size' => 7,
                    'visible' => true,
                ],
                'edge_banding' => [
                    'x' => 5,
                    'y' => 53,
                    'font_size' => 7,
                    'visible' => true,
                ],
                'date' => [
                    'x' => 5,
                    'y' => 58,
                    'font_size' => 6,
                    'visible' => true,
                ],
            ],
            'fields_visible' => [
                'project' => true,
                'part_name' => true,
                'dimensions' => true,
                'material' => true,
                'edge_banding' => true,
                'date' => true,
            ],
        ];
    }

    /**
     * Ruaj konfigurimin e ri
     */
    public function update(Request $request)
    {
        // Vetëm Admin dhe Menaxher
        if (!in_array(auth()->user()->rol_id, [1, 2])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'company_name' => 'nullable|string|max:255',
                'ticket_width_mm' => 'required|numeric|min:40|max:200',
                'ticket_height_mm' => 'required|numeric|min:40|max:200',
                'orientation' => 'required|in:portrait,landscape',
                'show_logo' => 'sometimes|in:true,false,1,0',
                'show_footer' => 'sometimes|in:true,false,1,0',
                'show_field_separators' => 'sometimes|in:true,false,1,0',
                'logo_height_mm' => 'nullable|numeric|min:3|max:30',
                'logo_rotation' => 'nullable|integer|in:0,90,270',
                'print_offset_x' => 'nullable|numeric|min:-50|max:50',
                'print_offset_y' => 'nullable|numeric|min:-50|max:50',
                'elements' => 'required|array',
                'fields_visible' => 'required|array',
            ]);
            
            // Convert boolean fields
            if (isset($validated['show_logo'])) {
                $validated['show_logo'] = filter_var($validated['show_logo'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['show_logo'] = false;
            }
            
            if (isset($validated['show_footer'])) {
                $validated['show_footer'] = filter_var($validated['show_footer'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['show_footer'] = false;
            }
            
            if (isset($validated['show_field_separators'])) {
                $validated['show_field_separators'] = filter_var($validated['show_field_separators'], FILTER_VALIDATE_BOOLEAN);
            } else {
                $validated['show_field_separators'] = false;
            }

            // Ensure numeric values
            $validated['ticket_width_mm'] = (float)$validated['ticket_width_mm'];
            $validated['ticket_height_mm'] = (float)$validated['ticket_height_mm'];
            $validated['logo_height_mm'] = (float)($validated['logo_height_mm'] ?? 6);
            $validated['logo_rotation'] = (int)($validated['logo_rotation'] ?? 0);
            $validated['print_offset_x'] = (float)($validated['print_offset_x'] ?? 0);
            $validated['print_offset_y'] = (float)($validated['print_offset_y'] ?? 0);

            // Ruaj në storage
            $path = storage_path('app/' . $this->configFile);
            $json = json_encode($validated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            if (file_put_contents($path, $json) === false) {
                throw new \Exception('Could not write to file');
            }

            return response()->json([
                'success' => true,
                'message' => 'Konfigurimi u ruajt me sukses!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Ticket Layout Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merr konfigurimin aktual (për AJAX)
     */
    public function getConfig()
    {
        return response()->json($this->loadConfig());
    }

    /**
     * Reset në default
     */
    public function reset()
    {
        // Vetëm Admin
        if (auth()->user()->rol_id !== 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $defaultConfig = $this->getDefaultConfig();
        
        $path = storage_path('app/' . $this->configFile);
        file_put_contents($path, json_encode($defaultConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->json([
            'success' => true,
            'message' => 'Konfigurimi u kthye në default!',
            'config' => $defaultConfig
        ]);
    }

    /**
     * Preview me të dhëna reale
     */
    public function preview(Request $request)
    {
        $config = $this->loadConfig();
        
        // Merr dimension ID për preview (opsionale)
        $dimensionId = $request->get('dimension_id');
        
        if ($dimensionId) {
            $dimension = \App\Models\ProjektetDimensions::with(['projekt.klient', 'materiali'])
                ->findOrFail($dimensionId);
        } else {
            // Përdor të dhëna sample
            $dimension = $this->getSampleDimension();
        }

        return view('ticket-layout.preview', compact('config', 'dimension'));
    }

    /**
     * Eksporton konfigurimin aktual në formatin LPrint (ZIP me INI dat files)
     */
    public function exportLPrint()
    {
        if (!in_array(auth()->user()->rol_id, [1, 2])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $config = $this->loadConfig();

        $widthMm = (float)($config['ticket_width_mm'] ?? 100);
        $heightMm = (float)($config['ticket_height_mm'] ?? 75);
        $offsetX = (float)($config['print_offset_x'] ?? 0); // mm; + = right
        $offsetY = (float)($config['print_offset_y'] ?? 0); // mm; + = down

        // Convert to hundredths of mm (LPrint style)
        $W100 = (int)round($widthMm * 100);
        $H100 = (int)round($heightMm * 100);

        // Map offsets to margins: positive offset becomes Left/Top margin, negative to Right/Bottom
        $mLeft  = $offsetX > 0 ? (int)round($offsetX * 100) : 0;
        $mRight = $offsetX < 0 ? (int)round(abs($offsetX) * 100) : 0;
        $mTop   = $offsetY > 0 ? (int)round($offsetY * 100) : 0;
        $mBot   = $offsetY < 0 ? (int)round(abs($offsetY) * 100) : 0;

        // Ensure non-negative and not exceeding page
        $mLeft = max(0, min($mLeft, $W100));
        $mRight = max(0, min($mRight, $W100 - $mLeft));
        $mTop = max(0, min($mTop, $H100));
        $mBot = max(0, min($mBot, $H100 - $mTop));

        // PaperSetup.dat uses L (usable width) and W (usable height)
        $L_usable = max(0, $W100 - ($mLeft + $mRight));
        $W_usable = max(0, $H100 - ($mTop + $mBot));

        $paperSetup = "[Page]\n" .
            "L={$L_usable}\n" .
            "W={$W_usable}\n" .
            "MrgTop={$mTop}\n" .
            "MrgLeft={$mLeft}\n" .
            "MrgRight={$mRight}\n" .
            "MrgBottom={$mBot}\n" .
            "DistVert=0\n" .
            "DistOriz=0\n" .
            "MultiPage=0\n" .
            "Copie=1\n" .
            "NumEtOr=1\n" .
            "NumEtVer=1\n" .
            "Type=0\n" .
            "NumPage=0\n" .
            "Q=1\n" .
            "Rect=0\n" .
            "NoSch=0\n";

        $customPaper = "[CustomSheet]\n" .
            "Size=1\n" .
            "[Sheet_000]\n" .
            "H={$H100}\n" .
            "W={$W100}\n" .
            "Name=label\n";

        // Sheet.dat uses L as width and W as height in their sample
        $sheetDat = "[SheetSet]\n" .
            "IsStd=0\n" .
            "Name=label\n" .
            "Pos=0\n" .
            "L={$W100}\n" .
            "W={$H100}\n";

        // Create ZIP in temp
        $zip = new ZipArchive();
        $zipName = 'LPrint_Export_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/' . $zipName);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['success' => false, 'error' => 'Could not create ZIP archive'], 500);
        }

        $zip->addFromString('Dati/PaperSetup.dat', $paperSetup);
        $zip->addFromString('Dati/CustomPaper.dat', $customPaper);
        $zip->addFromString('Dati/Sheet.dat', $sheetDat);
        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Gjeneron të dhëna sample për preview
     */
    protected function getSampleDimension()
    {
        return (object)[
            'id' => 1,
            'emri_pjeses' => 'Panel Anësor',
            'gjatesia' => 720,
            'gjeresia' => 600,
            'trashesia' => 18,
            'sasia' => 2,
            'kantim_front' => true,
            'kantim_back' => true,
            'kantim_left' => false,
            'kantim_right' => true,
            'kantim_type' => 'PVC',
            'kantim_thickness' => '0.8',
            'projekt' => (object)[
                'emri_projektit' => 'Kuzhina Moderne',
                'klient' => (object)[
                    'emri_klientit' => 'Sample Client'
                ]
            ],
            'materiali' => (object)[
                'emri_materialit' => 'Melaminë e Bardhë 18mm'
            ]
        ];
    }
}
