<?php

namespace App\Http\Controllers;

use App\Models\ProjektetDimensions;
use App\Models\Projektet;
use App\Models\Materialet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjektetDimensionsController extends Controller
{
    public function __construct()
    {
        // Middleware is handled in routes file
    }
    
    /**
     * Kontrollon nëse përdoruesi ka të drejtë të menaxhojë dimensionet
     */
    protected function checkDimensionManagementAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Ju lutem kyçuni për të vazhduar.');
        }
        
        // Vetëm Admin (1), Menaxher (2) dhe Disajnere (5) mund të shtojnë/modifikojnë dimensione
        if (!in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('projektet-dimensions.index')
                ->with('error', 'Vetëm administratori, menaxheri dhe disajnere mund të menaxhojnë dimensionet.');
        }
        
        return null;
    }
    
    /**
     * Filtron dimensionet bazuar në rolin e përdoruesit
     */
    protected function getFilteredDimensions($query)
    {
        $user = auth()->user();
        
        // Admin, Menaxher dhe Disajnere shohin të gjitha dimensionet
        if (in_array($user->rol_id, [1, 2, 5])) {
            return $query;
        }
        
        // Mjeshtër dhe Montues shohin vetëm dimensionet e projekteve të tyre
        return $query->whereHas('projekt', function($q) use ($user) {
            $q->where('mjeshtri_caktuar_id', $user->perdorues_id)
              ->orWhere('montuesi_caktuar_id', $user->perdorues_id);
        });
    }

    /**
     * Shfaq listën e dimensioneve
     */
    public function index(Request $request)
    {
        // Kontrollo nëse përdoruesi është i loguar
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $query = ProjektetDimensions::with(['projekt', 'materiali', 'krijues']);
        
        // Filtro dimensionet bazuar në rolin e përdoruesit
        $query = $this->getFilteredDimensions($query);

        // Filtri sipas projektit
        if ($request->filled('projekt_id')) {
            $query->where('projekt_id', $request->projekt_id);
        }

        // Filtri sipas statusit
        if ($request->filled('statusi')) {
            $query->where('statusi_prodhimit', $request->statusi);
        }

        // Filtri sipas materialit
        if ($request->filled('materiali_id')) {
            $query->where('materiali_id', $request->materiali_id);
        }

        // Kërkimi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('emri_pjeses', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('projekt', function($proj) use ($search) {
                      $proj->where('emri_projektit', 'like', "%{$search}%");
                  });
            });
        }

        $dimensions = $query->latest()->paginate(15);
        $projektet = Projektet::orderBy('emri_projektit')->get();
        $materialet = Materialet::orderBy('emri_materialit')->get();

        return view('projektet-dimensions.index', compact('dimensions', 'projektet', 'materialet'));
    }

    /**
     * Shfaq formën për krijimin e dimensionit të ri
     */
    public function create()
    {
        // Kontrollo qasjen për menaxhimin e dimensioneve
        if ($redirect = $this->checkDimensionManagementAccess()) {
            return $redirect;
        }

        $projektet = Projektet::orderBy('emri_projektit')->get();
        $materialet = Materialet::orderBy('emri_materialit')->get();
        $users = User::orderBy('emri')->get();

        return view('projektet-dimensions.create', compact('projektet', 'materialet', 'users'));
    }

    /**
     * Ruaj dimensionin e ri
     */
    public function store(Request $request)
    {
        // Kontrollo qasjen për menaxhimin e dimensioneve
        if ($redirect = $this->checkDimensionManagementAccess()) {
            return $redirect;
        }

        $validated = $request->validate([
            'projekt_id' => 'required|exists:projektet,projekt_id',
            'emri_pjeses' => 'required|string|max:255',
            'gjatesia' => 'required|numeric|min:0',
            'gjeresia' => 'required|numeric|min:0',
            'trashesia' => 'required|numeric|min:0',
            'njesi_matese' => 'required|in:mm,cm,m',
            'sasia' => 'required|integer|min:1',
            'materiali_id' => 'nullable|exists:materialet,material_id',
            'materiali_personal' => 'nullable|string|max:255',
            'kantim_needed' => 'boolean',
            'kantim_type' => 'nullable|in:PVC,ABS,Wood Veneer,Aluminum',
            'kantim_thickness' => 'nullable|numeric|min:0',
            'kantim_front' => 'boolean',
            'kantim_back' => 'boolean',
            'kantim_left' => 'boolean',
            'kantim_right' => 'boolean',
            'kantim_corners' => 'required|in:square,rounded',
            'pershkrimi' => 'nullable|string',
        ]);

        // Kontrollo nëse është zgjedhur materiali personal ose nga lista
        if (!$request->filled('materiali_id') && !$request->filled('materiali_personal')) {
            return back()->withInput()->withErrors(['material' => 'Duhet të zgjidhni një material nga lista ose të shtoni një material personal.']);
        }

        // Kontrollo stokun nëse është material nga lista
        if ($request->filled('materiali_id')) {
            $materiali = Materialet::find($request->materiali_id);
            $dimension = new ProjektetDimensions($validated);
            $sasiaNevojitur = $dimension->sasiaMaterialitNevojitur();

            if (!$materiali->kaStokTeMjaftueshem($sasiaNevojitur)) {
                return back()->withInput()->withErrors([
                    'materiali_id' => "Stoku i materialit '{$materiali->emri_materialit}' është i pamjaftueshëm. Keni nevojë për {$sasiaNevojitur}m³, por keni vetëm {$materiali->sasiaEDisponueshme()}m³ të disponueshëm."
                ]);
            }
        }

        $validated['krijues_id'] = Auth::id();

        DB::beginTransaction();
        try {
            $dimension = ProjektetDimensions::create($validated);

            DB::commit();

            return redirect()->route('projektet-dimensions.show', $dimension)
                           ->with('success', 'Dimensioni u krijua me sukses!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Ndodhi një gabim gjatë ruajtjes së dimensionit: ' . $e->getMessage()]);
        }
    }

    /**
     * Shfaq dimensionin specifik
     */
    public function show(ProjektetDimensions $dimension)
    {
        // Kontrollo nëse përdoruesi është i loguar
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $dimension->load(['projekt', 'materiali', 'krijues']);

        return view('projektet-dimensions.show', compact('dimension'));
    }

    /**
     * Shfaq formën për editimin
     */
    public function edit(ProjektetDimensions $dimension)
    {
        // Kontrollo qasjen për menaxhimin e dimensioneve
        if ($redirect = $this->checkDimensionManagementAccess()) {
            return $redirect;
        }

        $projektet = Projektet::orderBy('emri_projektit')->get();
        $materialet = Materialet::orderBy('emri_materialit')->get();
        $users = User::orderBy('emri')->get();

        return view('projektet-dimensions.edit', compact('dimension', 'projektet', 'materialet', 'users'));
    }

    /**
     * Përditëso dimensionin
     */
    public function update(Request $request, ProjektetDimensions $dimension)
    {
        // Kontrollo qasjen për menaxhimin e dimensioneve
        if ($redirect = $this->checkDimensionManagementAccess()) {
            return $redirect;
        }

        $validated = $request->validate([
            'projekt_id' => 'required|exists:projektet,projekt_id',
            'emri_pjeses' => 'required|string|max:255',
            'gjatesia' => 'required|numeric|min:0',
            'gjeresia' => 'required|numeric|min:0',
            'trashesia' => 'required|numeric|min:0',
            'njesi_matese' => 'required|in:mm,cm,m',
            'sasia' => 'required|integer|min:1',
            'materiali_id' => 'nullable|exists:materialet,material_id',
            'materiali_personal' => 'nullable|string|max:255',
            'kantim_needed' => 'boolean',
            'kantim_type' => 'nullable|in:PVC,ABS,Wood Veneer,Aluminum',
            'kantim_thickness' => 'nullable|numeric|min:0',
            'kantim_front' => 'boolean',
            'kantim_back' => 'boolean',
            'kantim_left' => 'boolean',
            'kantim_right' => 'boolean',
            'kantim_corners' => 'required|in:square,rounded',
            'statusi_prodhimit' => 'required|in:pending,cutting,edge_banding,completed',
            'workstation_current' => 'nullable|string|max:50',
            'pershkrimi' => 'nullable|string',
        ]);

        // Kontrollo nëse është zgjedhur materiali personal ose nga lista
        if (!$request->filled('materiali_id') && !$request->filled('materiali_personal')) {
            return back()->withInput()->withErrors(['material' => 'Duhet të zgjidhni një material nga lista ose të shtoni një material personal.']);
        }

        // Kontrollo stokun nëse është material nga lista dhe ka ndryshuar
        if ($request->filled('materiali_id') && $request->materiali_id != $dimension->materiali_id) {
            $materiali = Materialet::find($request->materiali_id);
            $dimensionTemp = new ProjektetDimensions($validated);
            $sasiaNevojitur = $dimensionTemp->sasiaMaterialitNevojitur();

            if (!$materiali->kaStokTeMjaftueshem($sasiaNevojitur)) {
                return back()->withInput()->withErrors([
                    'materiali_id' => "Stoku i materialit '{$materiali->emri_materialit}' është i pamjaftueshëm."
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $dimension->update($validated);

            DB::commit();

            return redirect()->route('projektet-dimensions.show', $dimension)
                           ->with('success', 'Dimensioni u përditësua me sukses!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Ndodhi një gabim gjatë përditësimit: ' . $e->getMessage()]);
        }
    }

    /**
     * Fshi dimensionin
     */
    public function destroy(ProjektetDimensions $dimension)
    {
        // Kontrollo qasjen për menaxhimin e dimensioneve
        if ($redirect = $this->checkDimensionManagementAccess()) {
            return $redirect;
        }

        DB::beginTransaction();
        try {
            $dimension->delete();

            DB::commit();

            return redirect()->route('projektet-dimensions.index')
                           ->with('success', 'Dimensioni u fshi me sukses!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Ndodhi një gabim gjatë fshirjes: ' . $e->getMessage()]);
        }
    }

    /**
     * Print PLC ticket
     */
    public function printTicket(ProjektetDimensions $dimension)
    {
        // Kontrollo nëse përdoruesi është i loguar
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $dimension->load(['projekt.klient', 'materiali']);

        // Shenjo si e printuar (safe, pa ndaluar view në rast gabimi)
        try {
            if (!$dimension->plc_ticket_printed) {
                $dimension->update(['plc_ticket_printed' => true]);
            }
        } catch (\Throwable $e) {
            // Silent fail - nuk pengon shfaqjen e view
        }

        $path = storage_path('app/ticket_layout_config.json');
        if (file_exists($path)) {
            $json = file_get_contents($path);
            $config = json_decode($json, true);
        } else {
            $config = [
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
                    'logo' => ['x' => 2, 'y' => 2, 'width' => 20, 'height' => 6, 'visible' => true],
                    'svg_diagram' => ['x' => 5, 'y' => 10, 'width' => 35, 'height' => 20, 'visible' => true],
                    'field_project' => ['x' => 5, 'y' => 32, 'font_size' => 7, 'visible' => true],
                    'field_part_name' => ['x' => 5, 'y' => 37, 'font_size' => 7, 'visible' => true],
                    'field_dimensions' => ['x' => 5, 'y' => 42, 'font_size' => 9, 'visible' => true],
                    'field_material' => ['x' => 5, 'y' => 48, 'font_size' => 7, 'visible' => true],
                    'field_edge_banding' => ['x' => 5, 'y' => 53, 'font_size' => 7, 'visible' => true],
                    'field_date' => ['x' => 5, 'y' => 58, 'font_size' => 6, 'visible' => true],
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

        return view('ticket-layout.preview', compact('config', 'dimension'));
    }

    /**
     * Shfaq raportin e materialeve të nevojshme
     */
    public function materialReport(Request $request)
    {
        // Kontrollo nëse përdoruesi është i loguar
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $query = ProjektetDimensions::with(['projekt', 'materiali'])
                                   ->whereNotNull('materiali_id');

        if ($request->filled('projekt_id')) {
            $query->where('projekt_id', $request->projekt_id);
        }

        $dimensions = $query->get();
        $projektet = Projektet::orderBy('emri_projektit')->get();

        // Grupi sipas materialit
        $materialUsage = $dimensions->groupBy('materiali.emri_materialit');

        $report = [];
        foreach ($materialUsage as $materialName => $dims) {
            $totalVolume = $dims->sum(function($dim) {
                return $dim->sasiaMaterialitNevojitur();
            });

            $report[] = [
                'material' => $materialName,
                'total_volume' => $totalVolume,
                'dimensions' => $dims
            ];
        }

        return view('projektet-dimensions.material-report', compact('report', 'projektet'));
    }

    /**
     * Eksporton dimensionet në format XML për makinën OSI 2007
     */
    public function exportXML(Request $request)
    {
        // Kontrollo qasjen për eksportim
        if ($redirect = $this->checkDimensionManagementAccess()) {
            return $redirect;
        }

        $query = ProjektetDimensions::with(['projekt', 'materiali']);
        
        // Filtro dimensionet bazuar në rolin e përdoruesit
        $query = $this->getFilteredDimensions($query);

        // Filtri sipas projektit nëse është specifikuar
        if ($request->filled('projekt_id')) {
            $query->where('projekt_id', $request->projekt_id);
        }

        // Filtri sipas statusit (nëse është specifikuar dhe nuk është "të gjitha")
        if ($request->filled('statusi') && $request->statusi !== 'te_gjitha') {
            $query->where('statusi_prodhimit', $request->statusi);
        }
        
        $dimensions = $query->orderBy('projekt_id')
                           ->orderBy('emri_pjeses')
                           ->get();

        if ($dimensions->isEmpty()) {
            return redirect()->back()->with('error', 'Nuk ka dimensione për eksport me kriteret e zgjedhura.');
        }

        // Grupo dimensionet sipas projektit
        $dimensionsByProject = $dimensions->groupBy('projekt_id');

        // Krijo XML-in
        $xml = $this->generateOSI2007XML($dimensionsByProject);

        // Krijo emrin e file-it
        $filename = 'OSI2007_Dimensionet_' . date('Y-m-d_H-i-s') . '.xml';

        // Përdor Laravel's download response për kompatibilitet maksimal
        return response()->streamDownload(function () use ($xml) {
            echo $xml;
        }, $filename, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Length' => strlen($xml)
        ]);
    }

    /**
     * Gjeneron XML në formatin e makinës OSI 2007
     */
    private function generateOSI2007XML($dimensionsByProject)
    {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<Single>\n";
        $xml .= "<WorkList ID='1' Code='CarpentryApp' Released='" . date('Y-m-d H:i:s') . "' Due='" . date('Y-m-d H:i:s') . "' MaterialID='0'>\n";

        $jobId = 101; // Fillo nga 101 si në origjinal
        $exeOrder = 1;
        $globalBoardId = 1;
        $globalPieceId = 0; // Fillo nga 0 si në origjinal

        foreach ($dimensionsByProject as $projektId => $dimensions) {
            $projekt = $dimensions->first()->projekt;
            
            // Grupo sipas materialit dhe trashësisë
            $materialGroups = $dimensions->groupBy(function($dim) {
                return $dim->materiali->emri_materialit . '_' . $dim->trashesia;
            });

            foreach ($materialGroups as $materialKey => $materialDimensions) {
                $firstDim = $materialDimensions->first();
                $materiali = $firstDim->materiali;
                $trashesia = $firstDim->trashesia;

                // Konverto në mm nëse është në cm ose m
                $thickness = $this->convertToMM($trashesia, $firstDim->njesi_matese);

                // Llogarit madhësinë e bordit (optimizim i thjeshtë)
                $maxLength = $materialDimensions->max(function($dim) {
                    return $this->convertToMM($dim->gjatesia, $dim->njesi_matese);
                });
                $maxWidth = $materialDimensions->max(function($dim) {
                    return $this->convertToMM($dim->gjeresia, $dim->njesi_matese);
                });

                // Sigurohu që bordi është mjaftueshëm i madh
                $boardLength = max($maxLength + 100, 2800); // minimum 2800mm
                $boardWidth = max($maxWidth + 100, 2070);   // minimum 2070mm

                // Krijo Job Code më të thjeshtë si në origjinal
                $safeProjectName = preg_replace('/[^A-Za-z0-9_]/', '_', $projekt->emri_projektit);
                $jobCode = $safeProjectName . '_' . $jobId;

                $xml .= "\t\t<Job ID='" . $jobId . "' Code='" . $jobCode . "' Type='PROGRAM' ToWork='9999' QBoards='1' ExeOrder='" . $exeOrder . "' Worked='0' Finished='" . date('Y-m-d H:i:s') . "' Deadline='" . date('Y-m-d H:i:s') . "'>\n";
                
                // Board definition me ID unik
                $xml .= "\t\t<Board id='" . $globalBoardId . "' Code='" . $materiali->emri_materialit . "' L='" . number_format($boardLength, 2, '.', '') . "' W='" . number_format($boardWidth, 2, '.', '') . "' Thickness='" . number_format($thickness, 2, '.', '') . "' MatNo='" . $materiali->material_id . "' MatCode='" . $materiali->emri_materialit . "' Grain='1'/>\n";

                // Pieces me ID unik
                $pieceN = 1;
                foreach ($materialDimensions as $dimension) {
                    $length = $this->convertToMM($dimension->gjatesia, $dimension->njesi_matese);
                    $width = $this->convertToMM($dimension->gjeresia, $dimension->njesi_matese);
                    $quantity = $dimension->sasia;

                    $xml .= "\t\t<Piece N='" . $pieceN . "' id='0' L='" . number_format($length, 2, '.', '') . "' W='" . number_format($width, 2, '.', '') . "' Q='" . $quantity . "' QPatt='" . $quantity . "'/>\n";
                    $pieceN++;
                }

                $globalBoardId++;

                // Program (cutting instructions) me optimizim
                $xml .= "\t\t<Program >\n"; // Hapësirë pas Program si në origjinal
                $cutId = 1;
                $totalCuts = count($materialDimensions) * 2;
                
                foreach ($materialDimensions as $index => $dimension) {
                    $length = $this->convertToMM($dimension->gjatesia, $dimension->njesi_matese);
                    $width = $this->convertToMM($dimension->gjeresia, $dimension->njesi_matese);
                    $quantity = $dimension->sasia;

                    // Cut për gjerësi
                    $xml .= "\t\t\t<Cut id='" . $cutId . "' Code='4' L='" . number_format($width, 2, '.', '') . "' Rep='" . $quantity . "'/>\n";
                    $cutId++;
                    
                    // Cut për gjatësi (me Chk në fund)
                    $xml .= "\t\t\t<Cut id='" . $cutId . "' Code='5' L='" . number_format($length, 2, '.', '') . "' Rep='" . $quantity . "'";
                    if ($cutId == $totalCuts) {
                        // Checksum në cut-in e fundit
                        $checksum = $this->generateChecksum($materialDimensions, $jobId);
                        $xml .= " Chk='" . $checksum . "'";
                    }
                    $xml .= "/>\n";
                    $cutId++;
                }
                $xml .= "\t\t</Program>\n";
                $xml .= "\t\t</Job>\n";

                $jobId++;
                $exeOrder++;
            }
        }

        $xml .= "\n</WorkList>\n";
        $xml .= "\n</Single>\n";

        return $xml;
    }

    /**
     * Konverton dimensionet në mm
     */
    private function convertToMM($value, $unit)
    {
        switch (strtolower($unit)) {
            case 'cm':
                return $value * 10;
            case 'm':
                return $value * 1000;
            case 'mm':
            default:
                return $value;
        }
    }

    /**
     * Gjeneron checksum për validim në makinë
     */
    private function generateChecksum($dimensions, $jobId)
    {
        $total = 0;
        foreach ($dimensions as $dimension) {
            $length = $this->convertToMM($dimension->gjatesia, $dimension->njesi_matese);
            $width = $this->convertToMM($dimension->gjeresia, $dimension->njesi_matese);
            $total += ($length + $width) * $dimension->sasia;
        }
        
        // Kombinon totalin me job ID për checksum unik
        return abs(crc32($total . '_' . $jobId)) % 99999 + 1000;
    }

    /**
     * API për kontrollin e stokut
     */
    public function checkStock(Request $request)
    {
        $materiali = Materialet::find($request->materiali_id);

        if (!$materiali) {
            return response()->json(['error' => 'Materiali nuk u gjet']);
        }

        $gjatesia = $request->gjatesia ?? 0;    // mm
        $gjeresia = $request->gjeresia ?? 0;    // mm
        $sasia = $request->sasia ?? 1;          // copë
        $trashesia = $request->trashesia ?? 0;  // mm

        // mm² -> m²
        $siperfaqja_m2 = ($gjatesia * $gjeresia * max(1, (int)$sasia)) / 1000000; 

        // Llogarit sipas njësisë së materialit
        $unit = strtolower($materiali->njesia_matese);
        switch ($unit) {
            case 'm²':
            case 'm2':
                $sasiaNevojitur = $siperfaqja_m2; // m²
                break;
            case 'm³':
            case 'm3':
                $trashesi_m = $trashesia / 1000; // mm -> m
                $sasiaNevojitur = $siperfaqja_m2 * $trashesi_m; // m³
                break;
            case 'copë':
            case 'cope':
            case 'pcs':
                $sasiaNevojitur = (float) $sasia; // copë
                break;
            default:
                $sasiaNevojitur = $siperfaqja_m2; // default m²
        }

        return response()->json([
            'stok_disponueshem' => $materiali->sasiaEDisponueshme(),
            'sasia_nevojitur' => $sasiaNevojitur,
            'ka_stok' => $materiali->kaStokTeMjaftueshem($sasiaNevojitur),
            'alert_stoku' => $materiali->eshteStokIUlet()
        ]);
    }
}
