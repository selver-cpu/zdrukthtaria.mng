@extends('layouts.app')

@section('title', 'Ticket Layout Editor')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Preview -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Live Preview</h5>
                </div>
                <div class="card-body" style="background: #f5f5f5; text-align: center; min-height: 500px;">
                    <div class="d-flex justify-content-end mb-2">
                        <label class="mr-2 small mb-0 align-self-center">Zoom</label>
                        <select id="previewZoom" class="form-control form-control-sm" style="width: 100px;">
                            <option value="0.5">50%</option>
                            <option value="0.75">75%</option>
                            <option value="1" selected>100%</option>
                            <option value="1.5">150%</option>
                            <option value="2">200%</option>
                            <option value="3">300%</option>
                        </select>
                    </div>
                    <div id="livePreview" style="display: inline-block; background: white; border: 0; position: relative; margin: 20px auto; cursor: move;">
                        <!-- Will be updated by JS -->
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666;">
                        <small><i class="fas fa-hand-pointer"></i> Kliko dhe tërhiq elementet për të ndryshuar pozicionin</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Cilësimet</h4>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <form id="layoutForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Cilësimet Bazike</h5>
                                
                                <div class="form-group">
                                    <label>Emri i Kompanisë</label>
                                    <input type="text" class="form-control" name="company_name" value="{{ $config['company_name'] ?? 'ColiDecor' }}">
                                </div>

                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="fields_visible[company_name]" id="field_company_name_basic" {{ ($config['fields_visible']['company_name'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="field_company_name_basic">Shfaq emrin e kompanisë si tekst</label>
                                </div>

                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="show_footer" id="show_footer" {{ ($config['show_footer'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_footer">Shfaq footer (ID)</label>
                                </div>

                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="show_field_separators" id="show_field_separators" {{ ($config['show_field_separators'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_field_separators">Shfaq vija ndërmjet fushave</label>
                                </div>

                                <div class="form-group">
                                    <label>Gjerësia (mm)</label>
                                    <input type="number" class="form-control" name="ticket_width_mm" value="{{ $config['ticket_width_mm'] ?? 100 }}" min="40" max="200" step="1">
                                </div>

                                <div class="form-group">
                                    <label>Lartësia (mm)</label>
                                    <input type="number" class="form-control" name="ticket_height_mm" value="{{ $config['ticket_height_mm'] ?? 75 }}" min="40" max="200" step="1">
                                </div>

                                <div class="form-group">
                                    <label>Orientimi</label>
                                    <select class="form-control" name="orientation">
                                        <option value="landscape" {{ ($config['orientation'] ?? 'landscape') == 'landscape' ? 'selected' : '' }}>Horizontal</option>
                                        <option value="portrait" {{ ($config['orientation'] ?? 'landscape') == 'portrait' ? 'selected' : '' }}>Vertikal</option>
                                    </select>
                                </div>

                                <hr>

                                <h5>Logo</h5>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="show_logo" id="show_logo" {{ ($config['show_logo'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_logo">Shfaq Logo</label>
                                </div>

                                <div class="form-group">
                                    <label>Lartësia e Logos (mm)</label>
                                    <input type="number" class="form-control" name="logo_height_mm" value="{{ $config['logo_height_mm'] ?? 6 }}" min="3" max="30" step="0.5">
                                </div>

                                <div class="form-group">
                                    <label>Rotacioni i Logos</label>
                                    <select class="form-control" name="logo_rotation">
                                        <option value="0" {{ ($config['logo_rotation'] ?? 0) == 0 ? 'selected' : '' }}>Horizontal (0°)</option>
                                        <option value="90" {{ ($config['logo_rotation'] ?? 0) == 90 ? 'selected' : '' }}>Vertikal (90°)</option>
                                        <option value="270" {{ ($config['logo_rotation'] ?? 0) == 270 ? 'selected' : '' }}>Vertikal (270°)</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <label class="small">Logo X (mm)</label>
                                        <input type="number" class="form-control form-control-sm" name="logo_x" value="{{ $config['elements']['logo']['x'] ?? 2 }}" min="-100" max="200" step="0.1">
                                    </div>
                                    <div class="col-6">
                                        <label class="small">Logo Y (mm)</label>
                                        <input type="number" class="form-control form-control-sm" name="logo_y" value="{{ $config['elements']['logo']['y'] ?? 2 }}" min="-100" max="200" step="0.1">
                                    </div>
                                </div>

                                <hr>

                                <h5>SVG Diagram (Kantimi)</h5>

                                <div class="form-group">
                                    <label>Rotacioni i Diagramit</label>
                                    <select class="form-control" name="svg_rotation">
                                        <option value="0" {{ ($config['elements']['svg_diagram']['rotation'] ?? 0) == 0 ? 'selected' : '' }}>Horizontal (0°)</option>
                                        <option value="90" {{ ($config['elements']['svg_diagram']['rotation'] ?? 0) == 90 ? 'selected' : '' }}>Vertikal (90°)</option>
                                        <option value="270" {{ ($config['elements']['svg_diagram']['rotation'] ?? 0) == 270 ? 'selected' : '' }}>Vertikal (270°)</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label>Gjerësia SVG (mm)</label>
                                        <input type="number" class="form-control" name="svg_width_mm" value="{{ $config['elements']['svg_diagram']['width_mm'] ?? 35 }}" min="10" max="100" step="1">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>Lartësia SVG (mm)</label>
                                        <input type="number" class="form-control" name="svg_height_mm" value="{{ $config['elements']['svg_diagram']['height_mm'] ?? 20 }}" min="10" max="100" step="1">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label>Trashësia e Vijës së Kantimit</label>
                                        <input type="number" class="form-control" name="edge_thickness" value="{{ $config['elements']['svg_diagram']['edge_thickness'] ?? 4 }}" min="1" max="10" step="1">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>Ngjyra e Kantimit</label>
                                        <input type="color" class="form-control" name="edge_color" value="{{ $config['elements']['svg_diagram']['edge_color'] ?? '#e74c3c' }}">
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="show_outline" id="show_outline" {{ ($config['elements']['svg_diagram']['show_outline'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_outline">Shfaq konturën e pjesës (outline)</label>
                                </div>


                                <div class="form-group">
                                    <label>Pozicioni X (mm)</label>
                                    <input type="number" class="form-control" name="svg_x" value="{{ $config['elements']['svg_diagram']['x'] ?? 10 }}" min="0" step="0.5">
                                </div>

                                <div class="form-group">
                                    <label>Pozicioni Y (mm)</label>
                                    <input type="number" class="form-control" name="svg_y" value="{{ $config['elements']['svg_diagram']['y'] ?? 40 }}" min="0" step="0.5">
                                </div>

                                <hr>

                                <h5>Print Offset</h5>

                                <div class="form-group">
                                    <label>Offset X (mm) - Majtas/Djathtas</label>
                                    <input type="number" class="form-control" name="print_offset_x" value="{{ $config['print_offset_x'] ?? 0 }}" min="-50" max="50" step="0.5">
                                </div>

                                <div class="form-group">
                                    <label>Offset Y (mm) - Lart/Poshtë</label>
                                    <input type="number" class="form-control" name="print_offset_y" value="{{ $config['print_offset_y'] ?? 0 }}" min="-50" max="50" step="0.5">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Fushat e Tekstit</h5>

                                @php
                                    $fields = [
                                        'company_name' => 'Emri i Kompanisë',
                                        'project' => 'Projekti',
                                        'part_name' => 'Emri i Pjesës',
                                        'dimensions' => 'Dimensionet',
                                        'material' => 'Materiali',
                                        'edge_banding' => 'Kantimi',
                                        'date' => 'Data',
                                    ];
                                @endphp

                                @foreach($fields as $key => $label)
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        @if($key !== 'company_name')
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="fields_visible[{{ $key }}]" id="field_{{ $key }}" {{ ($config['fields_visible'][$key] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label font-weight-bold" for="field_{{ $key }}">{{ $label }}</label>
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <span class="font-weight-bold">{{ $label }}</span>
                                                <small class="text-muted d-block">Shfaq/Fshih nga "Cilësimet Bazike"</small>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <div class="form-group mb-2">
                                                <label class="small">Font Size</label>
                                                <input type="number" class="form-control form-control-sm" name="elements[{{ $key }}][font_size]" value="{{ $config['elements']['field_'.$key]['font_size'] ?? ($config['elements'][$key]['font_size'] ?? 9) }}" min="6" max="20" step="1">
                                            </div>
                                            <div class="col-6">
                                                <label class="small">Rotacioni</label>
                                                <select class="form-control form-control-sm" name="elements[{{ $key }}][rotation]">
                                                    @php $rotVal = $config['elements']['field_'.$key]['rotation'] ?? ($config['elements'][$key]['rotation'] ?? 0); @endphp
                                                    <option value="0" {{ ($rotVal) == 0 ? 'selected' : '' }}>0°</option>
                                                    <option value="90" {{ ($rotVal) == 90 ? 'selected' : '' }}>90°</option>
                                                    <option value="270" {{ ($rotVal) == 270 ? 'selected' : '' }}>270°</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <label class="small">X (mm)</label>
                                                <input type="number" class="form-control form-control-sm" name="elements[{{ $key }}][x]" value="{{ $config['elements']['field_'.$key]['x'] ?? ($config['elements'][$key]['x'] ?? 10) }}" min="0" step="0.5">
                                            </div>
                                            <div class="col-6">
                                                <label class="small">Y (mm)</label>
                                                <input type="number" class="form-control form-control-sm" name="elements[{{ $key }}][y]" value="{{ $config['elements']['field_'.$key]['y'] ?? ($config['elements'][$key]['y'] ?? 20) }}" min="0" step="0.5">
                                            </div>
                                        </div>

                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="elements[{{ $key }}][bold]" id="bold_{{ $key }}" {{ ($config['elements']['field_'.$key]['bold'] ?? ($config['elements'][$key]['bold'] ?? false)) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="bold_{{ $key }}">Bold</label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <div class="text-center d-flex flex-wrap gap-2 justify-content-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Ruaj Ndryshimet
                            </button>
                            <button type="button" class="btn btn-warning btn-lg" onclick="window.open('{{ route('ticket-layout.preview') }}', '_blank')">
                                <i class="fas fa-print"></i> Preview & Printo
                            </button>
                            <a class="btn btn-outline-secondary btn-lg" href="{{ route('ticket-layout.export-lprint') }}" target="_blank">
                                <i class="fas fa-file-export"></i> Export LPrint
                            </a>
                            <button type="button" class="btn btn-secondary btn-lg" onclick="resetConfig()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_SCALE = 3; // 1mm = 3px at 100%
let ZOOM = 1;        // UI zoom factor (0.5x .. 3x)
let SCALE = BASE_SCALE * ZOOM;
let draggedElement = null;
let offsetX = 0, offsetY = 0;

function updatePreview() {
    SCALE = BASE_SCALE * ZOOM; // refresh scale from zoom
    const formData = new FormData(document.getElementById('layoutForm'));
    
    let width = parseFloat(formData.get('ticket_width_mm'));
    let height = parseFloat(formData.get('ticket_height_mm'));
    const orientation = formData.get('orientation');
    
    // Swap for portrait
    if (orientation === 'portrait') {
        [width, height] = [height, width];
    }
    
    const preview = document.getElementById('livePreview');
    preview.style.width = (width * SCALE) + 'px';
    preview.style.height = (height * SCALE) + 'px';
    
    let html = '';
    
    // Logo (draggable)
    if (formData.get('show_logo')) {
        const logoHeight = parseFloat(formData.get('logo_height_mm')) || 6;
        const logoRotation = parseInt(formData.get('logo_rotation')) || 0;
        const logoTransform = logoRotation !== 0 ? `transform: rotate(${logoRotation}deg); transform-origin: center center;` : '';
        const logoXmm = parseFloat(formData.get('logo_x')) || 2;
        const logoYmm = parseFloat(formData.get('logo_y')) || 2;
        const logoX = logoXmm * SCALE;
        const logoY = logoYmm * SCALE;
        html += `<div id="logo-elem" data-x-mm="${logoXmm}" data-y-mm="${logoYmm}" style="position: absolute; left: ${logoX}px; top: ${logoY}px; ${logoTransform} cursor: move; padding: 2px; border: 1px dashed #6c757d;">
                    <img src="{{ asset('img/logo.png') }}" style="height: ${logoHeight * SCALE}px;" draggable="false" onerror="this.style.display='none'">
                 </div>`;
    }
    
    // SVG Diagram (Kantimi)
    const svgXmm = parseFloat(formData.get('svg_x')) || 10;
    const svgYmm = parseFloat(formData.get('svg_y')) || 40;
    const svgX = svgXmm * SCALE;
    const svgY = svgYmm * SCALE;
    const svgRotation = parseInt(formData.get('svg_rotation')) || 0;
    const svgTransform = svgRotation !== 0 ? `transform: rotate(${svgRotation}deg); transform-origin: left top;` : '';
    const svgWmm = parseFloat(formData.get('svg_width_mm')) || 35;
    const svgHmm = parseFloat(formData.get('svg_height_mm')) || 20;
    const edgeT = parseFloat(formData.get('edge_thickness')) || 4;
    const edgeColor = formData.get('edge_color') || '#e74c3c';
    const showOutline = formData.get('show_outline') === 'on';
    
    // Center box in viewBox with margin for edges and labels
    // Box size auto-calculated from viewBox
    const margin = edgeT + 8;
    const centerX = 50;
    const centerY = 35;
    const boxW = 100 - 2*margin;
    const boxH = 60 - 2*margin;
    const boxX = centerX - boxW/2;
    const boxY = centerY - boxH/2;
    
    html += `<div id="svg-elem" data-x-mm="${svgXmm}" data-y-mm="${svgYmm}" style="position: absolute; top: ${svgY}px; left: ${svgX}px; ${svgTransform} cursor: move; padding: 2px; border: 1px dashed #007bff;">
                <svg width="${svgWmm * SCALE}px" height="${svgHmm * SCALE}px" viewBox="-15 0 115 75" preserveAspectRatio="none" style="border: none; pointer-events: none;">
                    ${showOutline ? `<rect x="${boxX}" y="${boxY}" width="${boxW}" height="${boxH}" fill="#f9f9f9" stroke="#ddd" stroke-width="0.5"/>` : ''}
                    <rect x="${boxX}" y="${boxY - edgeT}" width="${boxW}" height="${edgeT}" fill="${edgeColor}" rx="0.5"/>
                    <rect x="${boxX}" y="${boxY + boxH}" width="${boxW}" height="${edgeT}" fill="${edgeColor}" rx="0.5"/>
                    <rect x="${boxX - edgeT}" y="${boxY}" width="${edgeT}" height="${boxH}" fill="${edgeColor}" rx="0.5"/>
                    <rect x="${boxX + boxW}" y="${boxY}" width="${edgeT}" height="${boxH}" fill="${edgeColor}" rx="0.5"/>
                    <text x="${boxX + boxW/2}" y="${boxY - edgeT - 4}" text-anchor="middle" font-size="11" font-family="Arial, sans-serif" fill="#000" font-weight="600">W</text>
                    <text x="${boxX - edgeT - 6}" y="${boxY + boxH/2}" text-anchor="middle" font-size="11" font-family="Arial, sans-serif" fill="#000" font-weight="600" transform="rotate(-90 ${boxX - edgeT - 6} ${boxY + boxH/2})">L</text>
                </svg>
             </div>`;
    
    // Text fields
    let y = 20; // px offset for preview-only layouting
    const fields = ['company_name', 'project', 'part_name', 'dimensions', 'material', 'edge_banding', 'date'];
    fields.forEach(field => {
        if (formData.get(`fields_visible[${field}]`)) {
            const fontSize = parseInt(formData.get(`elements[${field}][font_size]`)) || 9;
            const rotation = parseInt(formData.get(`elements[${field}][rotation]`)) || 0;
            const bold = formData.get(`elements[${field}][bold]`) ? 'bold' : 'normal';
            const transform = rotation !== 0 ? `transform: rotate(${rotation}deg); transform-origin: left top;` : '';
            const fieldXmm = parseFloat(formData.get(`elements[${field}][x]`)) || 10;
            const fieldYmm = parseFloat(formData.get(`elements[${field}][y]`)) || (y / SCALE);
            const fieldX = fieldXmm * SCALE;
            const fieldY = fieldYmm * SCALE;

            html += `<div id="field-${field}" data-field="${field}" data-x-mm="${fieldXmm}" data-y-mm="${fieldYmm}" style="position: absolute; top: ${fieldY}px; left: ${fieldX}px; font-size: ${fontSize}px; font-weight: ${bold}; ${transform}; cursor: move; padding: 2px; border: 1px dashed #28a745;">
                        [${field}]
                     </div>`;
            y += fontSize + 5;
        }
    });
    
    preview.innerHTML = html;
    
    // Attach drag listeners
    attachDragListeners();
}

function attachDragListeners() {
    const preview = document.getElementById('livePreview');
    const draggables = preview.querySelectorAll('[data-x-mm][data-y-mm]');
    
    draggables.forEach(elem => {
        elem.addEventListener('mousedown', startDrag);
    });
}

function startDrag(e) {
    draggedElement = e.currentTarget;
    const rect = draggedElement.getBoundingClientRect();
    const previewRect = document.getElementById('livePreview').getBoundingClientRect();
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;
    
    draggedElement.style.opacity = '0.7';
    draggedElement.style.zIndex = '1000';

    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', stopDrag);
    e.preventDefault();
}

function drag(e) {
    if (!draggedElement) return;
    
    const preview = document.getElementById('livePreview');
    const previewRect = preview.getBoundingClientRect();
    
    let x = e.clientX - previewRect.left - offsetX; // px
    let y = e.clientY - previewRect.top - offsetY;  // px
    
    // Keep within relaxed bounds using transformed size (rotation-aware)
    const box = draggedElement.getBoundingClientRect();
    const w = box.width;
    const h = box.height;
    const MARGIN_MM = 30; // allow to move slightly outside canvas
    const marginPx = MARGIN_MM * SCALE;
    x = Math.max(-marginPx, Math.min(x, previewRect.width - w + marginPx));
    y = Math.max(-marginPx, Math.min(y, previewRect.height - h + marginPx));
    
    draggedElement.style.left = x + 'px';
    draggedElement.style.top = y + 'px';
}

function stopDrag() {
    if (!draggedElement) return;
    
    draggedElement.style.opacity = '1';
    draggedElement.style.zIndex = 'auto';
    
    // Save position back to inputs in mm
    const xPx = parseInt(draggedElement.style.left) || 0;
    const yPx = parseInt(draggedElement.style.top) || 0;
    const xMm = Math.round((xPx / SCALE) * 10) / 10;
    const yMm = Math.round((yPx / SCALE) * 10) / 10;
    let field = draggedElement.id.replace('field-', '').replace('svg-elem', 'svg_diagram');
    if (draggedElement.id === 'logo-elem') field = 'logo';

    if (field === 'svg_diagram') {
        const sx = document.querySelector('input[name="svg_x"]');
        const sy = document.querySelector('input[name="svg_y"]');
        if (sx) sx.value = xMm;
        if (sy) sy.value = yMm;
    } else if (field === 'logo') {
        const lx = document.querySelector('input[name="logo_x"]');
        const ly = document.querySelector('input[name="logo_y"]');
        if (lx) lx.value = xMm;
        if (ly) ly.value = yMm;
    } else {
        const xInput = document.querySelector(`input[name="elements[${field}][x]"]`);
        const yInput = document.querySelector(`input[name="elements[${field}][y]"]`);
        if (xInput) xInput.value = xMm;
        if (yInput) yInput.value = yMm;
    }
    
    document.removeEventListener('mousemove', drag);
    document.removeEventListener('mouseup', stopDrag);
    draggedElement = null;
}

// Update preview on load
document.addEventListener('DOMContentLoaded', updatePreview);

// Update preview on any change
document.getElementById('layoutForm').addEventListener('change', updatePreview);
document.getElementById('layoutForm').addEventListener('input', updatePreview);

// Zoom control
document.getElementById('previewZoom').addEventListener('change', function() {
    const v = parseFloat(this.value || '1');
    ZOOM = isNaN(v) ? 1 : Math.max(0.25, Math.min(v, 4));
    updatePreview();
});

document.getElementById('layoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        company_name: formData.get('company_name'),
        ticket_width_mm: parseFloat(formData.get('ticket_width_mm')),
        ticket_height_mm: parseFloat(formData.get('ticket_height_mm')),
        orientation: formData.get('orientation'),
        show_logo: formData.get('show_logo') === 'on' ? 'true' : 'false',
        show_footer: formData.get('show_footer') === 'on' ? 'true' : 'false',
        show_field_separators: formData.get('show_field_separators') === 'on' ? 'true' : 'false',
        logo_height_mm: parseFloat(formData.get('logo_height_mm')),
        logo_rotation: parseInt(formData.get('logo_rotation')),
        print_offset_x: parseFloat(formData.get('print_offset_x')),
        print_offset_y: parseFloat(formData.get('print_offset_y')),
        elements: {},
        fields_visible: {}
    };

    // Collect elements
    const fields = ['company_name', 'project', 'part_name', 'dimensions', 'material', 'edge_banding', 'date'];
    fields.forEach(field => {
        const key = `field_${field}`; // match preview.blade.php keys
        data.elements[key] = {
            font_size: parseInt(formData.get(`elements[${field}][font_size]`)) || 9,
            rotation: parseInt(formData.get(`elements[${field}][rotation]`)) || 0,
            bold: formData.get(`elements[${field}][bold]`) === 'on',
            // Save in mm
            x: parseFloat(formData.get(`elements[${field}][x]`)) || 10,
            y: parseFloat(formData.get(`elements[${field}][y]`)) || 20
        };
        data.fields_visible[field] = formData.get(`fields_visible[${field}]`) === 'on';
    });
    
    // Add SVG rotation and position
    data.elements.svg_diagram = {
        rotation: parseInt(formData.get('svg_rotation')) || 0,
        // Save in mm
        x: parseFloat(formData.get('svg_x')) || 10,
        y: parseFloat(formData.get('svg_y')) || 40,
        width_mm: parseFloat(formData.get('svg_width_mm')) || 35,
        height_mm: parseFloat(formData.get('svg_height_mm')) || 20,
        edge_thickness: parseFloat(formData.get('edge_thickness')) || 4,
        edge_color: formData.get('edge_color') || '#e74c3c',
        show_outline: formData.get('show_outline') === 'on'
    };

    // Add Logo position (mm)
    data.elements.logo = {
        x: parseFloat(formData.get('logo_x')) || 2,
        y: parseFloat(formData.get('logo_y')) || 2
    };

    console.log('Saving:', data);

    fetch('{{ route('ticket-layout.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(response => {
        console.log('Response:', response);
        if (response.success) {
            Swal.fire('Sukses!', response.message, 'success');
            updatePreview();
        } else {
            Swal.fire('Gabim!', response.error || 'Ndodhi një gabim', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        Swal.fire('Gabim!', err.message, 'error');
    });
});

function resetConfig() {
    if (confirm('A jeni të sigurt?')) {
        fetch('{{ route('ticket-layout.reset') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Handle logo visibility toggle
document.getElementById('show_logo').addEventListener('change', updatePreview);
</script>
@endsection
