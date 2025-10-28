<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjektetExport;
use App\Exports\MaterialetExport;
use App\Exports\StafiExport;
use App\Exports\FinanciarExport;
use App\Exports\ProjektetDimensionsExport;
use App\Models\ProjektetDimensions;
use App\Models\Projektet;
use App\Models\ProjektMateriale;
use App\Models\Materialet;
use App\Models\User;
use App\Models\StatusetProjektit;
use App\Models\Klientet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Intervention\Image\Facades\Image;

class ExportController extends Controller
{
    /**
     * Eksporto raportet në PDF
     */
    public function exportPDF(Request $request)
    {
        $lloji = $request->input('lloji');
        $format = $request->input('format', 'pdf');
        
        switch ($lloji) {
            case 'projektet':
                return $this->exportProjektetPDF($request);
            case 'materialet':
                return $this->exportMaterialetPDF($request);
            case 'stafi':
                return $this->exportStafiPDF($request);
            case 'financiar':
                return $this->exportFinanciarPDF($request);
            default:
                return redirect()->back()->with('error', 'Lloji i raportit nuk është valid.');
        }

        // Mbyll metoda exportPDF
    }

    /**
     * Eksporto Dimensionet në Excel
     */
    private function exportDimensionetExcel(Request $request)
    {
        $query = ProjektetDimensions::with(['projekt', 'materiali']);

        // Apliko filtrat, në sinkron me index-in e modulit
        if ($request->filled('projekt_id')) {
            $query->where('projekt_id', $request->projekt_id);
        }
        if ($request->filled('statusi')) {
            $query->where('statusi_prodhimit', $request->statusi);
        }
        if ($request->filled('materiali_id')) {
            $query->where('materiali_id', $request->materiali_id);
        }
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

        $dimensions = $query->latest()->get();
        $export = new ProjektetDimensionsExport($dimensions);
        return Excel::download($export, 'dimensionet_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Eksporto raportet në Excel
     */
    public function exportExcel(Request $request)
    {
        $lloji = $request->input('lloji');
        
        switch ($lloji) {
            case 'projektet':
                return $this->exportProjektetExcel($request);
            case 'materialet':
                return $this->exportMaterialetExcel($request);
            case 'stafi':
                return $this->exportStafiExcel($request);
            case 'financiar':
                return $this->exportFinanciarExcel($request);
            case 'dimensionet':
                return $this->exportDimensionetExcel($request);
            default:
                return redirect()->back()->with('error', 'Lloji i raportit nuk është valid.');
        }
    }

    /**
     * Eksporto raportet në foto (PNG/JPG)
     */
    public function exportImage(Request $request)
    {
        $lloji = $request->input('lloji', 'projektet');
        $format = $request->input('format', 'png');
        
        // Krijo HTML për raportin
        $html = $this->generateReportHTML($lloji, $request);
        
        // Përdorim një zgjidhje të thjeshtë për të kthyer HTML direkt
        // Shtojmë CSS për të stilizuar raportin për printim
        $html = '<html><head><title>Raport</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #6c757d; }
        </style>
        </head><body>' . $html . '
        <div class="footer">Raporti u gjenerua më ' . now()->format('d.m.Y H:i') . '</div>
        </body></html>';
        
        // Krijo një përgjigje me HTML
        $filename = "raport_{$lloji}_" . date('Y-m-d_H-i-s') . ".html";
        
        // Kthe HTML si përgjigje
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Eksporto raportet në file 3D (OBJ format)
     */
    public function export3D(Request $request)
    {
        $lloji = $request->input('lloji');
        
        // Krijo një file 3D bazuar në të dhënat e raportit
        $objContent = $this->generate3DModel($lloji, $request);
        
        $filename = "raport_3d_{$lloji}_" . date('Y-m-d_H-i-s') . ".obj";
        
        return response($objContent)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function exportProjektetPDF(Request $request)
    {
        $query = $this->getProjektetQuery($request);
        $projektet = $query->get();
        
        $pdf = PDF::loadView('exports.pdf.projektet', compact('projektet'));
        
        return $pdf->download('raport_projektet_' . date('Y-m-d') . '.pdf');
    }
    
    private function exportProjektetExcel(Request $request)
    {
        $query = $this->getProjektetQuery($request);
        $projektet = $query->get();
        
        $export = new ProjektetExport($projektet);
        return Excel::download($export, 'raporti_projekteve_' . now()->format('Y-m-d') . '.xlsx');
    }
    
    private function getProjektetQuery(Request $request)
    {
        $query = Projektet::with(['klient', 'statusi_projektit', 'mjeshtri', 'montuesi']);
        
        // Aplikoj filtrat
        if ($request->filled('data_nga')) {
            $query->where('data_krijimit', '>=', $request->data_nga);
        }
        
        if ($request->filled('data_deri')) {
            $query->where('data_krijimit', '<=', $request->data_deri);
        }
        
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        
        if ($request->filled('klient_id')) {
            $query->where('klient_id', $request->klient_id);
        }
        
        return $query;
    }

    private function exportMaterialetPDF(Request $request)
    {
        $query = ProjektMateriale::with(['material', 'projekt'])
            ->select('material_id', DB::raw('SUM(sasia_perdorur) as total_perdorur'))
            ->groupBy('material_id');
        
        if ($request->filled('data_nga') || $request->filled('data_deri')) {
            $query->whereHas('projekt', function($q) use ($request) {
                if ($request->filled('data_nga')) {
                    $q->where('data_krijimit', '>=', $request->data_nga);
                }
                if ($request->filled('data_deri')) {
                    $q->where('data_krijimit', '<=', $request->data_deri);
                }
            });
        }
        
        $materialetPerdorur = $query->get();
        
        $pdf = PDF::loadView('exports.pdf.materialet', compact('materialetPerdorur'));
        
        return $pdf->download('raport_materialet_' . date('Y-m-d') . '.pdf');
    }
    
    private function exportMaterialetExcel(Request $request)
    {
        $materialet = $this->getMaterialetQuery($request)->get();
        $export = new MaterialetExport($materialet);
        return Excel::download($export, 'raporti_materialeve_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function getMaterialetQuery(Request $request)
    {
        $query = ProjektMateriale::with(['material', 'projekt'])
            ->select('material_id', DB::raw('SUM(sasia_perdorur) as total_perdorur'))
            ->groupBy('material_id');
        
        if ($request->filled('data_nga') || $request->filled('data_deri')) {
            $query->whereHas('projekt', function($q) use ($request) {
                if ($request->filled('data_nga')) {
                    $q->where('data_krijimit', '>=', $request->data_nga);
                }
                if ($request->filled('data_deri')) {
                    $q->where('data_krijimit', '<=', $request->data_deri);
                }
            });
        }
        
        return $query;
    }

    private function exportStafiPDF(Request $request)
    {
        $mjeshtrat = User::whereHas('rol', function($query) {
            $query->where('emri_rolit', 'mjeshtër');
        })->withCount([
            'projekteSiMjesher',
            'projekteSiMjesher as projekte_perfunduar' => function($query) {
                $query->whereHas('status', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }
        ])->get();
        
        $montuesit = User::whereHas('rol', function($query) {
            $query->where('emri_rolit', 'montues');
        })->withCount([
            'projekteSiMontues',
            'projekteSiMontues as projekte_perfunduar' => function($query) {
                $query->whereHas('status', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }
        ])->get();
        
        $pdf = PDF::loadView('exports.pdf.stafi', compact('mjeshtrat', 'montuesit'));
        
        return $pdf->download('raport_stafi_' . date('Y-m-d') . '.pdf');
    }
    
    private function exportStafiExcel(Request $request)
    {
        $mjeshtrat = User::whereHas('rol', function($query) {
            $query->where('emri_rolit', 'mjeshtër');
        })->withCount([
            'projekteSiMjesher',
            'projekteSiMjesher as projekte_perfunduar' => function($query) {
                $query->whereHas('status', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }
        ])->get();
        
        $montuesit = User::whereHas('rol', function($query) {
            $query->where('emri_rolit', 'montues');
        })->withCount([
            'projekteSiMontues',
            'projekteSiMontues as projekte_perfunduar' => function($query) {
                $query->whereHas('status', function($q) {
                    $q->where('emri_statusit', 'Përfunduar');
                });
            }
        ])->get();
        
        $export = new StafiExport($mjeshtrat, $montuesit);
        return Excel::download($export, 'raport_stafi_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function exportFinanciarPDF(Request $request)
    {
        $projektet = $this->getFinanciarQuery($request);
        
        $pdf = PDF::loadView('exports.pdf.financiar', compact('projektet'));
        
        return $pdf->download('raport_financiar_' . date('Y-m-d') . '.pdf');
    }
    
    private function exportFinanciarExcel(Request $request)
    {
        $projektet = $this->getFinanciarQuery($request);
        
        $export = new FinanciarExport($projektet);
        return Excel::download($export, 'raport_financiar_' . now()->format('Y-m-d') . '.xlsx');
    }
    
    private function getFinanciarQuery(Request $request)
    {
        $query = Projektet::with(['klient', 'statusi_projektit']);
        
        if ($request->filled('data_nga')) {
            $query->where('data_krijimit', '>=', $request->data_nga);
        }

        if ($request->filled('data_deri')) {
            $query->where('data_krijimit', '<=', $request->data_deri);
        }

        if ($request->filled('klient_id')) {
            $query->where('klient_id', $request->klient_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        return $query->get();
    }

    private function generateReportHTML($lloji, Request $request)
    {
        // Gjenero HTML për imazhin e raportit
        switch ($lloji) {
            case 'projektet':
                $data = $this->getProjektetData($request);
                return view('exports.html.projektet', $data)->render();
            case 'materialet':
                $data = $this->getMaterialetData($request);
                return view('exports.html.materialet', $data)->render();
            case 'stafi':
                $data = $this->getStafiData($request);
                return view('exports.html.stafi', $data)->render();
            case 'financiar':
                $data = $this->getFinanciarData($request);
                return view('exports.html.financiar', $data)->render();
            default:
                return '<h1>Raport i pavlefshëm</h1>';
        }
    }

    private function generate3DModel($lloji, Request $request)
    {
        // Gjenero një model 3D të thjeshtë në format OBJ bazuar në të dhënat e raportit
        $objContent = "# Raport 3D - {$lloji}\n";
        $objContent .= "# Krijuar më: " . date('Y-m-d H:i:s') . "\n\n";
        
        switch ($lloji) {
            case 'projektet':
                $projektet = Projektet::count();
                $objContent .= $this->generateProjectCubes($projektet);
                break;
            case 'materialet':
                $materialet = Materialet::count();
                $objContent .= $this->generateMaterialSpheres($materialet);
                break;
            case 'stafi':
                $stafi = User::count();
                $objContent .= $this->generateStaffCylinders($stafi);
                break;
            case 'financiar':
                $teArdhurat = Projektet::sum('cmimi_total');
                $objContent .= $this->generateFinancialPyramid($teArdhurat);
                break;
        }
        return $objContent;
    }

    private function generateMaterialSpheres($materialet)
    {
        // Kjo është një metodë dummy për të gjeneruar sfera për materialet
        $objContent = "";
        $radius = 0.5;
        $spacing = 1.5;
        
        for ($i = 0; $i < min($materialet, 100); $i++) {
            $x = ($i % 10) * $spacing;
            $y = floor($i / 10) * $spacing;
            
            $objContent .= "v " . ($x - 5) . " $y 0\n";
            $objContent .= "v " . ($x + 5) . " $y 0\n";
            $objContent .= "v $x " . ($y + 5) . " 0\n";
            
            $baseIndex = $i * 3 + 1;
            $objContent .= "f $baseIndex " . ($baseIndex + 1) . " " . ($baseIndex + 2) . "\n";
        }
        
        return $objContent;
    }
    
    private function generateProjectCubes($projektet)
    {
        // Kjo është një metodë dummy për të gjeneruar kube për projektet
        $objContent = "";
        $size = 0.8;
        $spacing = 1.5;
        
        for ($i = 0; $i < min($projektet, 100); $i++) {
            $x = ($i % 10) * $spacing;
            $y = floor($i / 10) * $spacing;
            
            // Përcakto kulmet e kubit
            $v = [];
            $v[0] = [$x - $size, $y - $size, $size];
            $v[1] = [$x + $size, $y - $size, $size];
            $v[2] = [$x + $size, $y + $size, $size];
            $v[3] = [$x - $size, $y + $size, $size];
            $v[4] = [$x - $size, $y - $size, -$size];
            $v[5] = [$x + $size, $y - $size, -$size];
            $v[6] = [$x + $size, $y + $size, -$size];
            $v[7] = [$x - $size, $y + $size, -$size];
            
            // Shto kulmet në OBJ
            $baseIndex = $i * 8 + 1;
            foreach ($v as $vertex) {
                $objContent .= "v {$vertex[0]} {$vertex[1]} {$vertex[2]}\n";
            }
            
            // Përcakto faqet e kubit
            $faces = [
                [0,1,2,3], [1,5,6,2], [5,4,7,6],
                [4,0,3,7], [3,2,6,7], [4,5,1,0]
            ];
            
            // Shto faqet në OBJ
            foreach ($faces as $face) {
                $objContent .= "f ";
                foreach ($face as $index) {
                    $objContent .= ($baseIndex + $index) . " ";
                }
                $objContent .= "\n";
            }
        }
        
        return $objContent;
    }

    private function generateMaterialSpheresCount($count)
    {
        $obj = "# Sfera për materialet\n";
        for ($i = 0; $i < min($count, 8); $i++) {
            $x = $i * 3;
            // Gjenero një sferë të thjeshtë
            for ($j = 0; $j < 8; $j++) {
                $angle = ($j / 8) * 2 * M_PI;
                $vx = $x + cos($angle);
                $vy = sin($angle);
                $obj .= "v {$vx} {$vy} 0\n";
            }
        }
        return $obj;
    }

    private function generateStaffCylinders($count)
    {
        $obj = "# Cilindra për stafin\n";
        for ($i = 0; $i < min($count, 6); $i++) {
            $x = $i * 2.5;
            for ($j = 0; $j < 6; $j++) {
                $angle = ($j / 6) * 2 * M_PI;
                $vx = $x + cos($angle) * 0.5;
                $vy = sin($angle) * 0.5;
                $obj .= "v {$vx} {$vy} 0\n";
                $obj .= "v {$vx} {$vy} 2\n";
            }
        }
        return $obj;
    }

    private function generateFinancialPyramid($amount)
    {
        $obj = "# Piramidë për të ardhurat\n";
        $height = min($amount / 1000, 10); // Shkallëzo lartësinë
        $obj .= "v 0 0 0\n";
        $obj .= "v 2 0 0\n";
        $obj .= "v 2 2 0\n";
        $obj .= "v 0 2 0\n";
        $obj .= "v 1 1 {$height}\n";
        return $obj;
    }

    // Metodat ndihmëse për të marrë të dhënat
    private function getProjektetData(Request $request)
    {
        $query = Projektet::with(['klient', 'statusi_projektit']);
        // Aplikoj filtrat...
        return ['projektet' => $query->get()];
    }

    private function getMaterialetData(Request $request)
    {
        $materialet = Materialet::withCount('projektet')->get();
        return ['materialet' => $materialet];
    }

    private function getStafiData(Request $request)
    {
        // Get all staff members
        $stafi = User::withCount([
            'projektetMjeshtri as projektet_mjeshtri_count',
            'projektetMontuesi as projektet_montuesi_count'
        ])->get();
        
        return ['stafi' => $stafi];
    }

    private function getFinanciarData(Request $request)
    {
        // Get top projects by budget
        $projektet_top = Projektet::with(['klient', 'statusi_projektit'])
            ->orderBy('buxheti', 'desc')
            ->limit(5)
            ->get();
            
        // Calculate total income from projects
        $te_ardhurat_totale = Projektet::sum('buxheti');
        
        // Calculate total expenses for materials (dummy data if not available)
        $shpenzimet_materiale = DB::table('projekt_materiale')->sum('cmimi') ?? ($te_ardhurat_totale * 0.6);
        
        // Calculate net profit
        $fitimi_neto = $te_ardhurat_totale - $shpenzimet_materiale;
        
        return [
            'projektet_top' => $projektet_top,
            'te_ardhurat_totale' => $te_ardhurat_totale,
            'shpenzimet_materiale' => $shpenzimet_materiale,
            'fitimi_neto' => $fitimi_neto
        ];
    }
}
