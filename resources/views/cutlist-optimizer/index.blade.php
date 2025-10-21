@extends('layouts.app')

@section('title', 'Cutlist Optimizer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cut"></i> Cutlist Optimizer - Optimizimi i Prerjes
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Udhëzime:</strong> Zgjidh pjesët që dëshiron të optimizosh, vendos madhësinë e tabakës dhe kliko "Optimizo".
                        Sistemi do të gjejë mënyrën më efikase për të prerë pjesët.
                    </div>

                    <!-- Konfigurimi i Tabakës -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-cog"></i> Konfigurimi i Tabakës</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="stock_width">Gjerësia e Tabakës (mm)</label>
                                        <input type="number" class="form-control" id="stock_width" value="2800" min="100">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="stock_height">Lartësia e Tabakës (mm)</label>
                                        <input type="number" class="form-control" id="stock_height" value="2070" min="100">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="saw_kerf">Trashësia e Prerjes (mm) <small class="text-muted">Saw kerf - hapësira që humbet gjatë prerjes</small></label>
                                        <input type="number" class="form-control" id="saw_kerf" value="4" min="0" max="10">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input" id="check_stock" checked>
                                        <label class="form-check-label" for="check_stock">
                                            Kontrollo disponueshmërinë e materialit në stok
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Materiali</label>
                                        <select class="form-control" id="material_filter">
                                            <option value="">Të gjitha materialet</option>
                                            @foreach($materialet as $material)
                                                <option value="{{ $material->material_id }}">{{ $material->emri_materialit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista e Pjesëve -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Zgjidh Pjesët për Optimizim</h5>
                            <div>
                                <button class="btn btn-sm btn-light" id="selectAllBtn">
                                    <i class="fas fa-check-square"></i> Zgjidh të Gjitha
                                </button>
                                <button class="btn btn-sm btn-light" id="deselectAllBtn">
                                    <i class="fas fa-square"></i> Çzgjidh të Gjitha
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($groupedByMaterial->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Nuk ka dimensione të disponueshme për optimizim.
                                </div>
                            @else
                                @foreach($groupedByMaterial as $materialId => $dimensions)
                                    @php
                                        $material = $dimensions->first()->materiali;
                                    @endphp
                                    <div class="material-group mb-4">
                                        <h6 class="text-primary">
                                            <i class="fas fa-layer-group"></i> 
                                            {{ $material->emri_materialit ?? 'Material i Papërcaktuar' }}
                                            <span class="badge badge-info">{{ $dimensions->count() }} pjesë</span>
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="50">
                                                            <input type="checkbox" class="select-material" data-material="{{ $materialId }}">
                                                        </th>
                                                        <th>Projekti</th>
                                                        <th>Emri i Pjesës</th>
                                                        <th>Dimensionet (L×W×T)</th>
                                                        <th>Sasia</th>
                                                        <th>Kantimi</th>
                                                        <th>Sipërfaqja</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($dimensions as $dim)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="dimension-checkbox" 
                                                                       value="{{ $dim->id }}"
                                                                       data-material="{{ $materialId }}"
                                                                       data-area="{{ $dim->gjatesia * $dim->gjeresia }}">
                                                            </td>
                                                            <td>
                                                                <small>{{ $dim->projekt->emri_projektit ?? 'N/A' }}</small>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $dim->emri_pjeses }}</strong>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-secondary">
                                                                    {{ $dim->gjatesia }}×{{ $dim->gjeresia }}×{{ $dim->trashesia }}mm
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-primary">{{ $dim->sasia }}x</span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $kantim = [];
                                                                    if($dim->kantim_front) $kantim[] = 'P';
                                                                    if($dim->kantim_back) $kantim[] = 'Pr';
                                                                    if($dim->kantim_left) $kantim[] = 'M';
                                                                    if($dim->kantim_right) $kantim[] = 'D';
                                                                @endphp
                                                                @if(!empty($kantim))
                                                                    <small class="text-danger">{{ implode(', ', $kantim) }}</small>
                                                                @else
                                                                    <small class="text-muted">-</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <small>{{ number_format(($dim->gjatesia * $dim->gjeresia) / 1000000, 2) }}m²</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Statistika dhe Butoni -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stat-box">
                                                <h6 class="text-muted">Pjesë të Zgjedhura</h6>
                                                <h3 class="text-primary" id="selectedCount">0</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-box">
                                                <h6 class="text-muted">Sipërfaqja Totale</h6>
                                                <h3 class="text-success" id="totalArea">0 m²</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-box">
                                                <h6 class="text-muted">Tabaka të Nevojshme (est.)</h6>
                                                <h3 class="text-warning" id="estimatedSheets">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-success btn-lg" id="optimizeBtn" disabled>
                                        <i class="fas fa-magic"></i> Optimizo Tani
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5 class="mt-3">Duke optimizuar...</h5>
                <p class="text-muted">Ju lutem prisni, po llogaritet layout-i më i mirë.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    updateStatistics();

    // Select/Deselect all
    $('#selectAllBtn').click(function() {
        $('.dimension-checkbox').prop('checked', true);
        updateStatistics();
    });

    $('#deselectAllBtn').click(function() {
        $('.dimension-checkbox').prop('checked', false);
        updateStatistics();
    });

    // Select by material
    $('.select-material').change(function() {
        const materialId = $(this).data('material');
        const isChecked = $(this).is(':checked');
        $(`.dimension-checkbox[data-material="${materialId}"]`).prop('checked', isChecked);
        updateStatistics();
    });

    // Individual checkbox
    $('.dimension-checkbox').change(function() {
        // Check if piece is too large
        const $row = $(this).closest('tr');
        const dimensionText = $row.find('td:eq(3)').text(); // Get dimensions
        const match = dimensionText.match(/(\d+)×(\d+)/);
        
        if (match && $(this).is(':checked')) {
            const pieceWidth = parseInt(match[1]);
            const pieceHeight = parseInt(match[2]);
            const stockWidth = parseInt($('#stock_width').val());
            const stockHeight = parseInt($('#stock_height').val());
            
            // Check if piece fits (even rotated)
            const fitsNormal = pieceWidth <= stockWidth && pieceHeight <= stockHeight;
            const fitsRotated = pieceHeight <= stockWidth && pieceWidth <= stockHeight;
            
            if (!fitsNormal && !fitsRotated) {
                $(this).prop('checked', false);
                Swal.fire({
                    icon: 'warning',
                    title: 'Pjesë Shumë e Madhe',
                    html: `Pjesa me dimensione <strong>${pieceWidth}×${pieceHeight}mm</strong> është më e madhe se tabaka <strong>${stockWidth}×${stockHeight}mm</strong>.<br><br>Ndrysho madhësinë e tabakës ose mos e zgjidh këtë pjesë.`,
                    confirmButtonText: 'OK'
                });
                updateStatistics();
                return;
            }
        }
        
        updateStatistics();
    });

    // Material filter
    $('#material_filter').change(function() {
        const materialId = $(this).val();
        if (materialId) {
            $('.material-group').hide();
            $(`.dimension-checkbox[data-material="${materialId}"]`).closest('.material-group').show();
        } else {
            $('.material-group').show();
        }
    });

    // Optimize button
    $('#optimizeBtn').click(function() {
        const selectedIds = [];
        const materials = new Set();
        
        $('.dimension-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
            materials.add($(this).data('material'));
        });

        console.log('Selected IDs:', selectedIds);
        console.log('Materials:', Array.from(materials));

        if (selectedIds.length === 0) {
            Swal.fire('Gabim', 'Zgjidh të paktën një pjesë për optimizim!', 'error');
            return;
        }

        // Check if multiple materials selected - NOT ALLOWED
        if (materials.size > 1) {
            Swal.fire({
                icon: 'error',
                title: 'Materiale të Ndryshme',
                html: 'Nuk mund të optimizosh pjesë me materiale të ndryshme!<br><br>Zgjidh vetëm pjesë me <strong>një material</strong>.',
                confirmButtonText: 'OK'
            });
            return;
        }

        optimizeCutlist(selectedIds);
    });

    function updateStatistics() {
        const checked = $('.dimension-checkbox:checked');
        const count = checked.length;
        let totalArea = 0;
        const materials = new Set();

        checked.each(function() {
            totalArea += parseFloat($(this).data('area'));
            materials.add($(this).data('material'));
        });

        const stockWidth = parseFloat($('#stock_width').val()) || 2800;
        const stockHeight = parseFloat($('#stock_height').val()) || 2070;
        const stockArea = (stockWidth * stockHeight) / 1000000; // m²
        const estimatedSheets = Math.ceil(totalArea / stockArea);

        $('#selectedCount').text(count);
        $('#totalArea').text(totalArea.toFixed(2) + ' m²');
        $('#estimatedSheets').text(estimatedSheets);

        // Disable button if no selection OR multiple materials
        const shouldDisable = count === 0 || materials.size > 1;
        
        console.log('Update Statistics:', {
            count: count,
            materials: Array.from(materials),
            shouldDisable: shouldDisable
        });
        
        $('#optimizeBtn').prop('disabled', shouldDisable);
        
        // Show warning if multiple materials selected
        if (materials.size > 1) {
            $('#optimizeBtn').attr('title', 'Nuk mund të optimizosh materiale të ndryshme!');
            $('#optimizeBtn').removeClass('btn-success').addClass('btn-secondary');
            console.log('Button disabled - multiple materials');
        } else {
            $('#optimizeBtn').attr('title', 'Kliko për të optimizuar');
            $('#optimizeBtn').removeClass('btn-secondary').addClass('btn-success');
            console.log('Button enabled - single material');
        }
    }

    function optimizeCutlist(dimensionIds) {
        $('#loadingModal').modal('show');

        $.ajax({
            url: '{{ route('cutlist-optimizer.optimize') }}',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                dimension_ids: dimensionIds,
                stock_width: $('#stock_width').val(),
                stock_height: $('#stock_height').val(),
                saw_kerf: $('#saw_kerf').val(),
                check_stock: $('#check_stock').is(':checked') ? 1 : 0
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                if (response.success) {
                    // Show warnings if any
                    if (response.warnings && response.warnings.length > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Paralajmërime',
                            html: response.warnings.join('<br>'),
                            confirmButtonText: 'Shiko Rezultatet'
                        }).then(() => {
                            showResults(response.result);
                        });
                    } else {
                        showResults(response.result);
                    }
                } else {
                    Swal.fire('Gabim', response.error || 'Optimizimi dështoi', 'error');
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                console.error('Optimization error:', xhr);
                
                let errorMsg = 'Ndodhi një gabim gjatë optimizimit.';
                if (xhr.responseJSON) {
                    errorMsg = xhr.responseJSON.error || errorMsg;
                    if (xhr.responseJSON.details) {
                        errorMsg += '<br><br><small>' + xhr.responseJSON.details.join('<br>') + '</small>';
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    html: errorMsg
                });
            }
        });
    }

    function showResults(result) {
        // Store result in sessionStorage
        sessionStorage.setItem('cutlist_result', JSON.stringify(result));
        
        // Open results in new window
        window.open('{{ route('cutlist-optimizer.result', ['id' => 'latest']) }}', '_blank');
    }
});
</script>

<style>
.stat-box {
    text-align: center;
    padding: 15px;
    border-radius: 5px;
    background: #f8f9fa;
}
.stat-box h6 {
    margin-bottom: 5px;
    font-size: 12px;
}
.stat-box h3 {
    margin: 0;
    font-weight: bold;
}
.material-group {
    border-left: 3px solid #3498db;
    padding-left: 15px;
}
</style>
@endpush
@endsection
