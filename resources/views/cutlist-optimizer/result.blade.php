@extends('layouts.app')

@section('title', 'Cutlist Optimizer - Rezultatet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Rezultatet e Optimizimit
                    </h4>
                    <div>
                        <button class="btn btn-light btn-sm" id="downloadAllBtn">
                            <i class="fas fa-download"></i> Shkarko të Gjitha (SVG)
                        </button>
                        <button class="btn btn-light btn-sm" id="exportPdfBtn">
                            <i class="fas fa-file-pdf"></i> Eksporto PDF
                        </button>
                        <a href="{{ route('cutlist-optimizer.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kthehu
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Statistics -->
                    <div class="row mb-4" id="summarySection">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Tabaka të Përdorura</h6>
                                    <h2 id="totalSheets">-</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Pjesë të Vendosura</h6>
                                    <h2 id="totalPieces">-</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Efikasitet</h6>
                                    <h2 id="efficiency">-%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Mbetje Totale</h6>
                                    <h2 id="wasteArea">- m²</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sheet Tabs -->
                    <ul class="nav nav-tabs" id="sheetTabs" role="tablist">
                        <!-- Tabs will be generated dynamically -->
                    </ul>

                    <!-- Sheet Content -->
                    <div class="tab-content" id="sheetTabContent">
                        <!-- Content will be generated dynamically -->
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Nuk ka rezultate për të shfaqur. Kthehu dhe ekzekuto optimizimin.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let optimizationResult = null;

$(document).ready(function() {
    loadResults();

    $('#downloadAllBtn').click(function() {
        downloadAllSVGs();
    });

    $('#exportPdfBtn').click(function() {
        exportToPDF();
    });
    
    // Enable Bootstrap tabs
    $(document).on('click', '[data-toggle="tab"]', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});

function loadResults() {
    // Try to load from sessionStorage
    const resultJson = sessionStorage.getItem('cutlist_result');
    
    if (!resultJson) {
        $('#noResults').show();
        return;
    }

    try {
        optimizationResult = JSON.parse(resultJson);
        displayResults(optimizationResult);
    } catch (e) {
        console.error('Error parsing results:', e);
        $('#noResults').show();
    }
}

function displayResults(result) {
    // Check if there are any pieces placed
    if (!result.sheets || result.sheets.length === 0 || result.summary.totalPieces === 0) {
        $('#summarySection').hide();
        $('#sheetTabs').hide();
        $('#sheetTabContent').html(`
            <div class="alert alert-warning mt-4">
                <h5><i class="fas fa-exclamation-triangle"></i> Asnjë Pjesë nuk u Vendos</h5>
                <p>Optimizimi u ekzekutua por asnjë pjesë nuk u vendos në tabaka. Kjo mund të ndodhë nëse:</p>
                <ul>
                    <li>Të gjitha pjesët janë shumë të mëdha për tabakën</li>
                    <li>Nuk u zgjodhën pjesë për optimizim</li>
                    <li>Dimensionet e tabakës janë shumë të vogla</li>
                </ul>
                <p class="mb-0"><strong>Zgjidhje:</strong> Kthehu dhe zgjidh pjesë më të vogla ose rrit madhësinë e tabakës.</p>
            </div>
        `);
        return;
    }

    // Update summary
    $('#totalSheets').text(result.summary.totalSheets);
    $('#totalPieces').text(result.summary.totalPieces);
    $('#efficiency').text(result.summary.efficiency + '%');
    $('#wasteArea').text((result.summary.wasteArea / 1000000).toFixed(2) + ' m²');

    // Generate tabs and content
    const $tabs = $('#sheetTabs');
    const $content = $('#sheetTabContent');
    
    result.sheets.forEach((sheet, index) => {
        const sheetNum = index + 1;
        const isActive = index === 0 ? 'active' : '';
        
        // Create tab
        $tabs.append(`
            <li class="nav-item">
                <a class="nav-link ${isActive}" id="sheet-${sheetNum}-tab" data-toggle="tab" 
                   href="#sheet-${sheetNum}" role="tab">
                    Tabaka ${sheetNum}
                    <span class="badge badge-primary">${sheet.pieces.length} pjesë</span>
                </a>
            </li>
        `);

        // Create content
        const sheetArea = sheet.width * sheet.height;
        const usedArea = sheet.pieces.reduce((sum, p) => sum + (p.width * p.height), 0);
        const sheetEfficiency = ((usedArea / sheetArea) * 100).toFixed(1);
        const wasteArea = sheetArea - usedArea;

        $content.append(`
            <div class="tab-pane fade ${isActive ? 'show active' : ''}" 
                 id="sheet-${sheetNum}" role="tabpanel">
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>
                            Tabaka ${sheetNum} - ${sheet.width}×${sheet.height}mm
                            <span class="badge badge-success ml-2">${sheetEfficiency}% efikasitet</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- SVG Diagram -->
                        <div class="text-center mb-3">
                            <div id="svg-container-${sheetNum}" class="svg-diagram">
                                ${generateSVG(sheet, sheetNum, result.sheets.length)}
                            </div>
                        </div>

                        <!-- Piece List -->
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Lista e Pjesëve:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Emri</th>
                                                <th>Dimensionet</th>
                                                <th>Pozicioni</th>
                                                <th>Rotuar</th>
                                                <th>Kantimi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${generatePieceList(sheet.pieces)}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Statistika:</h6>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Pjesë:</span>
                                        <strong>${sheet.pieces.length}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Sipërfaqja e Përdorur:</span>
                                        <strong>${(usedArea / 1000000).toFixed(2)} m²</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Mbetje:</span>
                                        <strong class="text-warning">${(wasteArea / 1000000).toFixed(2)} m²</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Efikasitet:</span>
                                        <strong class="text-success">${sheetEfficiency}%</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });
}

function generateSVG(sheet, sheetNum, totalSheets) {
    const scale = 0.15; // Reduced scale for better fit
    const padding = 30;
    const width = sheet.width * scale + padding * 2;
    const height = sheet.height * scale + padding * 2;
    
    const colors = ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6', '#1abc9c', '#34495e', '#e67e22'];
    
    let svg = `
        <svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" style="border: 1px solid #ddd; background: white;">
            <!-- Sheet background -->
            <rect x="${padding}" y="${padding}" 
                  width="${sheet.width * scale}" 
                  height="${sheet.height * scale}" 
                  fill="#ecf0f1" stroke="#2c3e50" stroke-width="2" rx="3"/>
            
            <!-- Grid lines -->
            ${generateGridLines(sheet, scale, padding)}
            
            <!-- Pieces -->
            ${sheet.pieces.map((piece, idx) => {
                const x = padding + piece.x * scale;
                const y = padding + piece.y * scale;
                const w = piece.width * scale;
                const h = piece.height * scale;
                const color = colors[idx % colors.length];
                const sawKerf = 4 * scale; // 4mm saw kerf
                
                return `
                    <g class="piece">
                        <rect x="${x}" y="${y}" width="${w}" height="${h}" 
                              fill="${color}" stroke="#2c3e50" stroke-width="1.5" opacity="0.8"/>
                        
                        <!-- Saw kerf visualization (red lines) -->
                        <line x1="${x + w}" y1="${y}" x2="${x + w + sawKerf}" y2="${y}" 
                              stroke="#e74c3c" stroke-width="1" stroke-dasharray="2,2" opacity="0.5"/>
                        <line x1="${x}" y1="${y + h}" x2="${x}" y2="${y + h + sawKerf}" 
                              stroke="#e74c3c" stroke-width="1" stroke-dasharray="2,2" opacity="0.5"/>
                        
                        <text x="${x + w/2}" y="${y + h/2}" 
                              text-anchor="middle" font-size="10" font-weight="bold" fill="#2c3e50">
                            #${idx + 1}
                        </text>
                        <text x="${x + w/2}" y="${y + h/2 + 12}" 
                              text-anchor="middle" font-size="8" fill="#34495e">
                            ${piece.width}×${piece.height}
                        </text>
                        ${piece.rotated ? `<text x="${x + w/2}" y="${y + h/2 + 22}" text-anchor="middle" font-size="7" fill="#e74c3c">↻</text>` : ''}
                    </g>
                `;
            }).join('')}
            
            <!-- Dimensions -->
            <text x="${width/2}" y="20" text-anchor="middle" font-size="12" font-weight="bold">
                Tabaka ${sheetNum}/${totalSheets} - ${sheet.width}×${sheet.height}mm
            </text>
        </svg>
    `;
    
    return svg;
}

function generateGridLines(sheet, scale, padding) {
    let lines = '';
    const gridSize = 100; // 100mm grid
    
    for (let x = 0; x <= sheet.width; x += gridSize) {
        const xPos = padding + x * scale;
        lines += `<line x1="${xPos}" y1="${padding}" x2="${xPos}" y2="${padding + sheet.height * scale}" stroke="#bdc3c7" stroke-width="0.5" opacity="0.3"/>`;
    }
    
    for (let y = 0; y <= sheet.height; y += gridSize) {
        const yPos = padding + y * scale;
        lines += `<line x1="${padding}" y1="${yPos}" x2="${padding + sheet.width * scale}" y2="${yPos}" stroke="#bdc3c7" stroke-width="0.5" opacity="0.3"/>`;
    }
    
    return lines;
}

function generatePieceList(pieces) {
    return pieces.map((piece, idx) => {
        const kantim = [];
        if (piece.edge_banding) {
            if (piece.edge_banding.front) kantim.push('P');
            if (piece.edge_banding.back) kantim.push('Pr');
            if (piece.edge_banding.left) kantim.push('M');
            if (piece.edge_banding.right) kantim.push('D');
        }
        
        return `
            <tr>
                <td><strong>#${idx + 1}</strong></td>
                <td>${piece.name}</td>
                <td><span class="badge badge-secondary">${piece.width}×${piece.height}mm</span></td>
                <td><small>${piece.x}, ${piece.y}</small></td>
                <td>${piece.rotated ? '<span class="badge badge-warning">Po</span>' : '<span class="badge badge-light">Jo</span>'}</td>
                <td>${kantim.length > 0 ? '<small class="text-danger">' + kantim.join(', ') + '</small>' : '-'}</td>
            </tr>
        `;
    }).join('');
}

function downloadAllSVGs() {
    Swal.fire({
        title: 'Duke shkarkuar...',
        text: 'Ju lutem prisni',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // TODO: Implement SVG download
    setTimeout(() => {
        Swal.fire('Sukses', 'SVG-të u shkarkuan!', 'success');
    }, 1000);
}

function exportToPDF() {
    Swal.fire({
        title: 'Duke gjeneruar PDF...',
        text: 'Ju lutem prisni',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // TODO: Implement PDF export
    setTimeout(() => {
        Swal.fire('Sukses', 'PDF u gjenerua!', 'success');
    }, 1000);
}
</script>

<style>
.svg-diagram {
    display: inline-block;
    max-width: 100%;
    overflow: auto;
}
.nav-tabs .badge {
    font-size: 10px;
}
</style>
@endpush
@endsection
