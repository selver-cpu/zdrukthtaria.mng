@extends('layouts.app')

@section('title', 'Ticket Layout Editor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> PLC Ticket Layout Editor
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Udhëzime:</strong> Lëviz elementet me mouse (drag & drop) ose vendos pozicionet manualisht. Kliko "Ruaj" për të ruajtur ndryshimet.
                    </div>

                    <div class="row">
                        <!-- Left Panel: Controls -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Cilësimet e Përgjithshme</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label>Emri i Kompanisë</label>
                                        <input type="text" class="form-control" id="company_name" 
                                               value="{{ $config['company_name'] ?? 'ColiDecor' }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Madhësia e Tiketës</label>
                                        <div class="row">
                                            <div class="form-group">
                                                <label>Gjerësia (mm)</label>
                                                <input type="number" class="form-control" id="ticket_width_mm" 
                                                       value="{{ $config['ticket_width_mm'] ?? 100 }}" min="70" max="200">
                                                <small class="text-muted">Rekomanduar: 100mm ose më shumë</small>
                                            </div>

                                            <div class="form-group">
                                                <label>Lartësia (mm)</label>
                                                <input type="number" class="form-control" id="ticket_height_mm" 
                                                       value="{{ $config['ticket_height_mm'] ?? 75 }}" min="60" max="200">
                                                <small class="text-muted">Rekomanduar: 75mm ose më shumë</small>
                                            </div>
                                            
                                            @if(($config['ticket_width_mm'] ?? 100) < 90 || ($config['ticket_height_mm'] ?? 75) < 70)
                                            <div class="alert alert-warning alert-sm">
                                                <i class="fas fa-exclamation-triangle"></i> <strong>Kujdes:</strong> Madhësia është shumë e vogël. Elementet mund të dalin jashtë kornizave!
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Orientimi</label>
                                        <select class="form-control" id="orientation">
                                            <option value="landscape" {{ ($config['orientation'] ?? 'landscape') == 'landscape' ? 'selected' : '' }}>
                                                Horizontal (Landscape)
                                            </option>
                                            <option value="portrait" {{ ($config['orientation'] ?? 'landscape') == 'portrait' ? 'selected' : '' }}>
                                                Vertikal (Portrait)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="show_logo" 
                                               {{ ($config['show_logo'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_logo">
                                            Shfaq Logo
                                        </label>
                                    </div>

                                    <div id="logo_controls">
                                        <div class="form-group mb-2">
                                            <label class="small">Lartësia e Logos (mm)</label>
                                            <input type="number" class="form-control form-control-sm" id="logo_height_mm" 
                                                   value="{{ $config['logo_height_mm'] ?? 6 }}" min="3" max="30" step="0.5">
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label class="small">Orientimi i Logos</label>
                                            <select class="form-control form-control-sm" id="logo_rotation">
                                                <option value="0" {{ ($config['logo_rotation'] ?? 0) == 0 ? 'selected' : '' }}>Horizontal (0°)</option>
                                                <option value="90" {{ ($config['logo_rotation'] ?? 0) == 90 ? 'selected' : '' }}>Vertikal (90°)</option>
                                                <option value="270" {{ ($config['logo_rotation'] ?? 0) == 270 ? 'selected' : '' }}>Vertikal (270°)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <h6 class="text-info"><i class="fas fa-arrows-alt"></i> Pozicioni për Printim</h6>
                                    <small class="text-muted">Lëviz të gjithë layout-in kur printon</small>
                                    
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <label class="small">Offset X (mm)</label>
                                            <input type="number" class="form-control form-control-sm" id="print_offset_x" 
                                                   value="{{ $config['print_offset_x'] ?? 0 }}" min="-50" max="50" step="0.5">
                                            <small class="text-muted">← Majtas / Djathtas →</small>
                                        </div>
                                        <div class="col-6">
                                            <label class="small">Offset Y (mm)</label>
                                            <input type="number" class="form-control form-control-sm" id="print_offset_y" 
                                                   value="{{ $config['print_offset_y'] ?? 0 }}" min="-50" max="50" step="0.5">
                                            <small class="text-muted">↑ Lart / Poshtë ↓</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Elementet e Vizueshme -->
                            <div class="card mb-3">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Fushat e Vizueshme</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $fields = [
                                            'project' => 'Projekti',
                                            'part_name' => 'Emri i Pjesës',
                                            'dimensions' => 'Dimensionet',
                                            'material' => 'Materiali',
                                            'edge_banding' => 'Kantimi',
                                            'date' => 'Data',
                                        ];
                                    @endphp

                                    @foreach($fields as $key => $label)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input field-visible" type="checkbox" 
                                                   id="field_{{ $key }}" data-field="{{ $key }}"
                                                   {{ ($config['fields_visible'][$key] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="field_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Butonat -->
                            <div class="card">
                                <div class="card-body">
                                    <button class="btn btn-success btn-block mb-2" id="saveBtn">
                                        <i class="fas fa-save"></i> Ruaj Ndryshimet
                                    </button>
                                    <button class="btn btn-info btn-block mb-2" id="previewBtn">
                                        <i class="fas fa-eye"></i> Preview me të Dhëna Reale
                                    </button>
                                    <button class="btn btn-warning btn-block mb-2" id="resetBtn">
                                        <i class="fas fa-undo"></i> Kthe në Default
                                    </button>
                                    <a href="{{ route('projektet-dimensions.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-arrow-left"></i> Kthehu
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Interactive Preview -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-mouse-pointer"></i> Interactive Preview - Drag & Drop
                                    </h5>
                                </div>
                                <div class="card-body" style="background: #f5f5f5; overflow: auto;">
                                    <div id="ticketCanvas" style="margin: 20px auto; position: relative; background: white; border: 2px solid #333; cursor: default;">
                                        <!-- Elementet draggable -->
                                        <div id="logo_element" class="draggable-element" data-element="logo" style="position: absolute; left: 6px; top: 6px; cursor: move; border: 1px dashed #007bff; padding: 2px; z-index: 10;">
                                            <img src="{{ asset('img/logo.png') }}" style="height: 18px; display: block;" onerror="this.parentElement.innerHTML='[LOGO]'">
                                        </div>
                                        
                                        <div id="svg_element" class="draggable-element" data-element="svg_diagram" style="position: absolute; left: 15px; top: 30px; cursor: move; border: 1px dashed #28a745; z-index: 10;">
                                            <svg width="105px" height="60px" viewBox="0 0 100 60" style="display: block;">
                                                <rect x="20" y="15" width="60" height="30" fill="white" stroke="#333" stroke-width="2"/>
                                                <rect x="20" y="12" width="60" height="3" fill="#e74c3c"/>
                                                <rect x="20" y="45" width="60" height="3" fill="#e74c3c"/>
                                                <rect x="80" y="15" width="3" height="30" fill="#e74c3c"/>
                                                <text x="50" y="8" text-anchor="middle" font-size="6" fill="#666">600mm</text>
                                                <text x="12" y="32" text-anchor="middle" font-size="6" fill="#666" transform="rotate(-90 12 32)">720mm</text>
                                            </svg>
                                        </div>
                                        
                                        <!-- Fushat individuale draggable -->
                                        <div id="field_project_elem" class="draggable-element text-field" data-element="field_project" data-field="project" style="position: absolute; left: 130px; top: 30px; font-family: Arial; font-size: 9px; z-index: 5; cursor: move; border: 1px dashed #ffc107; padding: 2px;">
                                            <strong>Projekt:</strong> Kuzhina Moderne
                                        </div>
                                        
                                        <div id="field_part_name_elem" class="draggable-element text-field" data-element="field_part_name" data-field="part_name" style="position: absolute; left: 130px; top: 40px; font-family: Arial; font-size: 9px; z-index: 5; cursor: move; border: 1px dashed #ffc107; padding: 2px;">
                                            <strong>Pjesa:</strong> Panel Anësor
                                        </div>
                                        
                                        <div id="field_dimensions_elem" class="draggable-element text-field" data-element="field_dimensions" data-field="dimensions" style="position: absolute; left: 130px; top: 50px; font-family: Arial; font-size: 11px; font-weight: bold; z-index: 5; cursor: move; border: 1px dashed #ffc107; padding: 2px;">
                                            720 × 600 × 18mm
                                        </div>
                                        
                                        <div id="field_material_elem" class="draggable-element text-field" data-element="field_material" data-field="material" style="position: absolute; left: 130px; top: 62px; font-family: Arial; font-size: 9px; z-index: 5; cursor: move; border: 1px dashed #ffc107; padding: 2px;">
                                            <strong>Material:</strong> Melaminë
                                        </div>
                                        
                                        <div id="field_edge_banding_elem" class="draggable-element text-field" data-element="field_edge_banding" data-field="edge_banding" style="position: absolute; left: 130px; top: 72px; font-family: Arial; font-size: 9px; z-index: 5; cursor: move; border: 1px dashed #ffc107; padding: 2px;">
                                            <strong>Kantim:</strong> PVC 0.8mm
                                        </div>
                                        
                                        <div id="field_date_elem" class="draggable-element text-field" data-element="field_date" data-field="date" style="position: absolute; left: 130px; top: 82px; font-family: Arial; font-size: 8px; color: #666; z-index: 5; cursor: move; border: 1px dashed #ffc107; padding: 2px;">
                                            20/10/2025
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-hand-pointer"></i> Kliko dhe tërhiq elementet për të ndryshuar pozicionin
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kontrollet e Teksteve dhe SVG -->
                            <div class="card mt-3">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="fas fa-cog"></i> Kontrollet e Elementeve</h6>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    <!-- SVG Diagram Rotation -->
                                    <div class="border-bottom pb-2 mb-3">
                                        <label class="small font-weight-bold text-success">SVG Diagram (Kantimi):</label>
                                        <div class="input-group input-group-sm mb-1">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-redo"></i></span>
                                            </div>
                                            <select class="form-control" id="svg_diagram_rotation">
                                                <option value="0" {{ ($config['elements']['svg_diagram']['rotation'] ?? 0) == 0 ? 'selected' : '' }}>Horizontal</option>
                                                <option value="90" {{ ($config['elements']['svg_diagram']['rotation'] ?? 0) == 90 ? 'selected' : '' }}>Vertikal (90°)</option>
                                                <option value="270" {{ ($config['elements']['svg_diagram']['rotation'] ?? 0) == 270 ? 'selected' : '' }}>Vertikal (270°)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    @php
                                        $textFields = [
                                            'field_project' => ['label' => 'Projekti', 'default_size' => 9],
                                            'field_part_name' => ['label' => 'Emri i Pjesës', 'default_size' => 9],
                                            'field_dimensions' => ['label' => 'Dimensionet', 'default_size' => 11],
                                            'field_material' => ['label' => 'Materiali', 'default_size' => 9],
                                            'field_edge_banding' => ['label' => 'Kantimi', 'default_size' => 9],
                                            'field_date' => ['label' => 'Data', 'default_size' => 8],
                                        ];
                                    @endphp

                                    @foreach($textFields as $key => $field)
                                        <div class="border-bottom pb-2 mb-3">
                                            <label class="small font-weight-bold text-primary">{{ $field['label'] }}:</label>
                                            
                                            <!-- Font Size -->
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-text-height"></i></span>
                                                </div>
                                                <input type="number" class="form-control text-size-input" 
                                                       id="{{ $key }}_size" 
                                                       data-field="{{ $key }}"
                                                       value="{{ $config['elements'][$key]['font_size'] ?? $field['default_size'] }}" 
                                                       min="6" max="20" step="1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">px</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Rotation -->
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-redo"></i></span>
                                                </div>
                                                <select class="form-control text-rotation-input" 
                                                        id="{{ $key }}_rotation" 
                                                        data-field="{{ $key }}">
                                                    <option value="0" {{ ($config['elements'][$key]['rotation'] ?? 0) == 0 ? 'selected' : '' }}>Horizontal</option>
                                                    <option value="90" {{ ($config['elements'][$key]['rotation'] ?? 0) == 90 ? 'selected' : '' }}>Vertikal (90°)</option>
                                                    <option value="270" {{ ($config['elements'][$key]['rotation'] ?? 0) == 270 ? 'selected' : '' }}>Vertikal (270°)</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Bold -->
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input text-bold-input" type="checkbox" 
                                                       id="{{ $key }}_bold" 
                                                       data-field="{{ $key }}"
                                                       {{ ($config['elements'][$key]['bold'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="{{ $key }}_bold">
                                                    <i class="fas fa-bold"></i> Bold
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let config = @json($config);
let isDragging = false;
let currentElement = null;
let offsetX = 0, offsetY = 0;
const SCALE = 3; // 1mm = 3px

$(document).ready(function() {
    initializeCanvas();
    initializeDragAndDrop();
    initializeFieldVisibility();

    // Event listeners
    $('#company_name, #ticket_width_mm, #ticket_height_mm, #orientation, #logo_height_mm').on('input change', function() {
        updateCanvasSize();
    });

    $('#show_logo').on('change', function() {
        $('#logo_controls').toggle(this.checked);
        $('#logo_element').toggle(this.checked);
    });
    
    // Logo rotation control
    $('#logo_rotation').on('change', function() {
        const rotation = parseInt($(this).val());
        updateLogoRotation(rotation);
    });

    $('.field-visible').on('change', function() {
        const field = $(this).data('field');
        const isChecked = $(this).is(':checked');
        $(`.text-field[data-field="${field}"]`).toggle(isChecked);
        console.log('Field visibility changed:', field, isChecked);
    });

    // Position inputs
    $('.position-input').on('input', function() {
        const element = $(this).data('element');
        const axis = $(this).data('axis');
        const value = parseFloat($(this).val());
        updateElementPosition(element, axis, value);
    });

    // Save button
    $('#saveBtn').click(function() {
        console.log('=== SAVE BUTTON CLICKED ===');
        saveConfig();
    });

    // Reset button
    $('#resetBtn').click(function() {
        if (confirm('A jeni të sigurt që dëshironi të ktheni në konfigurimin default?')) {
            resetConfig();
        }
    });

    // Preview button
    $('#previewBtn').click(function() {
        window.open('{{ route('ticket-layout.preview') }}', '_blank');
    });
    
    // Text size controls
    $('.text-size-input').on('input', function() {
        const field = $(this).data('field');
        const size = parseInt($(this).val());
        updateTextStyle(field, 'font_size', size);
    });
    
    // Text rotation controls
    $('.text-rotation-input').on('change', function() {
        const field = $(this).data('field');
        const rotation = parseInt($(this).val());
        updateTextStyle(field, 'rotation', rotation);
    });
    
    // Text bold controls
    $('.text-bold-input').on('change', function() {
        const field = $(this).data('field');
        const bold = $(this).is(':checked');
        updateTextStyle(field, 'bold', bold);
    });
    
    // SVG rotation control
    $('#svg_diagram_rotation').on('change', function() {
        const rotation = parseInt($(this).val());
        updateSVGRotation(rotation);
    });
    
    // Check canvas size on change
    $('#ticket_width_mm, #ticket_height_mm').on('change', function() {
        const width = parseFloat($('#ticket_width_mm').val());
        const height = parseFloat($('#ticket_height_mm').val());
        
        if (width < 90 || height < 70) {
            Swal.fire({
                icon: 'warning',
                title: 'Madhësi e vogël!',
                html: 'Madhësia e ticket-it është shumë e vogël.<br>Rekomandohet: <strong>100mm × 75mm</strong><br><br>Elementet mund të dalin jashtë kornizave!',
                confirmButtonText: 'OK, e kuptova'
            });
        }
    });
});

function initializeCanvas() {
    console.log('Initializing canvas with config:', config);
    updateCanvasSize();
    const width = parseFloat($('#ticket_width_mm').val());
    const height = parseFloat($('#ticket_height_mm').val());

    // Set initial positions
    if (config.elements && config.elements.logo) {
        setElementPosition('logo', config.elements.logo.x || 2, config.elements.logo.y || 2);
    } else {
        setElementPosition('logo', 2, 2);
    }
    
    // Apply logo rotation if saved
    if (config.logo_rotation) {
        const rotation = config.logo_rotation;
        const transform = rotation === 0 ? 'none' : `rotate(${rotation}deg)`;
        $('#logo_element img').css({
            'transform': transform,
            'transform-origin': 'center center'
        });
        console.log('Logo rotation applied:', rotation);
    }
    
    if (config.elements && config.elements.svg_diagram) {
        setElementPosition('svg_diagram', config.elements.svg_diagram.x || 5, config.elements.svg_diagram.y || 10);
        
        // Apply SVG rotation if saved
        if (config.elements.svg_diagram.rotation) {
            const rotation = config.elements.svg_diagram.rotation;
            const transform = rotation === 0 ? 'none' : `rotate(${rotation}deg)`;
            $('#svg_element').css({
                'transform': transform,
                'transform-origin': 'left top'
            });
            console.log('SVG rotation applied:', rotation);
        }
    } else {
        setElementPosition('svg_diagram', 5, 10);
    }
    
    // Initialize individual text fields
    const textFields = ['field_project', 'field_part_name', 'field_dimensions', 'field_material', 'field_edge_banding', 'field_date'];
    const defaultY = [10, 13.3, 16.6, 20.6, 24, 27.3]; // Default Y positions in mm
    
    textFields.forEach((field, index) => {
        if (config.elements && config.elements[field]) {
            setElementPosition(field, config.elements[field].x || 43, config.elements[field].y || defaultY[index]);
            
            // Apply saved styles
            if (config.elements[field].font_size) {
                $(`#${field}_elem`).css('font-size', config.elements[field].font_size + 'px');
            }
            if (config.elements[field].rotation) {
                const rotation = config.elements[field].rotation;
                const transform = rotation === 0 ? 'none' : `rotate(${rotation}deg)`;
                $(`#${field}_elem`).css({
                    'transform': transform,
                    'transform-origin': 'left top'
                });
            }
            if (config.elements[field].bold) {
                $(`#${field}_elem`).css('font-weight', 'bold');
            }
        } else {
            setElementPosition(field, 43, defaultY[index]);
        }
    });
    
    console.log('Canvas initialized:', $('#ticketCanvas').width(), 'x', $('#ticketCanvas').height());
}

function initializeFieldVisibility() {
    // Set initial visibility based on config
    $('.field-visible').each(function() {
        const field = $(this).data('field');
        const isVisible = config.fields_visible && config.fields_visible[field] !== false;
        $(this).prop('checked', isVisible);
        $(`.text-field[data-field="${field}"]`).toggle(isVisible);
    });
    console.log('Field visibility initialized');
}

function initializeDragAndDrop() {
    const draggableElements = $('.draggable-element');
    console.log('Found draggable elements:', draggableElements.length);
    
    draggableElements.each(function() {
        console.log('Attaching mousedown to:', $(this).attr('id'));
        $(this).on('mousedown', function(e) {
            console.log('Mousedown on:', $(this).attr('id'));
            isDragging = true;
            currentElement = $(this);
            const offset = currentElement.offset();
            const canvasOffset = $('#ticketCanvas').offset();
            offsetX = e.pageX - offset.left;
            offsetY = e.pageY - offset.top;
            currentElement.css('opacity', '0.7');
            e.preventDefault();
        });
    });

    $(document).on('mousemove', function(e) {
        if (isDragging && currentElement) {
            const canvasOffset = $('#ticketCanvas').offset();
            let x = e.pageX - canvasOffset.left - offsetX;
            let y = e.pageY - canvasOffset.top - offsetY;

            // Keep within bounds
            const canvasWidth = $('#ticketCanvas').width();
            const canvasHeight = $('#ticketCanvas').height();
            const elemWidth = currentElement.outerWidth();
            const elemHeight = currentElement.outerHeight();

            x = Math.max(0, Math.min(x, canvasWidth - elemWidth));
            y = Math.max(0, Math.min(y, canvasHeight - elemHeight));

            currentElement.css({
                left: x + 'px',
                top: y + 'px'
            });

            // Update input fields
            const elementName = currentElement.data('element');
            const xMm = (x / SCALE).toFixed(1);
            const yMm = (y / SCALE).toFixed(1);
            
            // Update manual inputs if they exist
            const xInput = $(`#${elementName}_x`);
            const yInput = $(`#${elementName}_y`);
            if (xInput.length) xInput.val(xMm);
            if (yInput.length) yInput.val(yMm);

            // Update config
            if (!config.elements) config.elements = {};
            if (!config.elements[elementName]) config.elements[elementName] = {};
            config.elements[elementName].x = parseFloat(xMm);
            config.elements[elementName].y = parseFloat(yMm);
        }
    });

    $(document).on('mouseup', function() {
        if (isDragging && currentElement) {
            currentElement.css('opacity', '1');
            isDragging = false;
            currentElement = null;
        }
    });
}

function setElementPosition(elementName, xMm, yMm) {
    const x = xMm * SCALE;
    const y = yMm * SCALE;
    $(`#${elementName}_element`).css({
        left: x + 'px',
        top: y + 'px'
    });
}

function updateElementPosition(elementName, axis, valueMm) {
    const currentX = parseFloat($(`#${elementName}_x`).val());
    const currentY = parseFloat($(`#${elementName}_y`).val());
    
    if (axis === 'x') {
        setElementPosition(elementName, valueMm, currentY);
        if (!config.elements[elementName]) config.elements[elementName] = {};
        config.elements[elementName].x = valueMm;
    } else {
        setElementPosition(elementName, currentX, valueMm);
        if (!config.elements[elementName]) config.elements[elementName] = {};
        config.elements[elementName].y = valueMm;
    }
}

function updateCanvasSize() {
    let width = parseFloat($('#ticket_width_mm').val());
    let height = parseFloat($('#ticket_height_mm').val());
    const orientation = $('#orientation').val();
    
    // Swap dimensions for portrait
    if (orientation === 'portrait') {
        [width, height] = [height, width];
    }
    
    $('#ticketCanvas').css({
        width: (width * SCALE) + 'px',
        height: (height * SCALE) + 'px'
    });
    
    console.log('Canvas size updated:', width + 'x' + height + 'mm', '(' + orientation + ')');
}

function updatePreview() {
    const width = $('#ticket_width_mm').val();
    const height = $('#ticket_height_mm').val();
    const showLogo = $('#show_logo').is(':checked');
    const logoHeight = $('#logo_height_mm').val();
    const companyName = $('#company_name').val();

    // Gjenero preview HTML
    let html = `
        <div style="width: ${width * 3}px; height: ${height * 3}px; border: 2px solid #333; padding: 10px; background: white; font-family: Arial, sans-serif; position: relative;">
            ${showLogo ? `
                <div style="position: absolute; top: 5px; right: 5px;">
                    <img src="{{ asset('img/logo.png') }}" style="height: ${logoHeight * 3}px;" alt="Logo">
                </div>
            ` : ''}
            
            <div style="margin-top: ${showLogo ? logoHeight * 3 + 10 : 10}px;">
                <!-- SVG Diagram -->
                <div style="margin-bottom: 10px;">
                    <svg width="${35 * 3}px" height="${20 * 3}px" viewBox="0 0 100 60" style="border: 1px solid #ccc;">
                        <rect x="20" y="15" width="60" height="30" fill="white" stroke="#333" stroke-width="2"/>
                        <rect x="20" y="12" width="60" height="3" fill="#e74c3c"/>
                        <rect x="20" y="45" width="60" height="3" fill="#e74c3c"/>
                        <rect x="80" y="15" width="3" height="30" fill="#e74c3c"/>
                        <text x="50" y="8" text-anchor="middle" font-size="6" fill="#666">600mm</text>
                        <text x="12" y="32" text-anchor="middle" font-size="6" fill="#666" transform="rotate(-90 12 32)">720mm</text>
                    </svg>
                </div>

                <!-- Fushat -->
                ${$('#field_project').is(':checked') ? '<div style="font-size: 9px; margin-bottom: 3px;"><strong>Projekt:</strong> Kuzhina Moderne</div>' : ''}
                ${$('#field_part_name').is(':checked') ? '<div style="font-size: 9px; margin-bottom: 3px;"><strong>Pjesa:</strong> Panel Anësor</div>' : ''}
                ${$('#field_dimensions').is(':checked') ? '<div style="font-size: 11px; margin-bottom: 3px; font-weight: bold;">720 × 600 × 18mm</div>' : ''}
                ${$('#field_material').is(':checked') ? '<div style="font-size: 9px; margin-bottom: 3px;"><strong>Material:</strong> Melaminë e Bardhë</div>' : ''}
                ${$('#field_edge_banding').is(':checked') ? '<div style="font-size: 9px; margin-bottom: 3px;"><strong>Kantim:</strong> PVC 0.8mm (P, Pr, D)</div>' : ''}
                ${$('#field_date').is(':checked') ? '<div style="font-size: 8px; color: #666;">${new Date().toLocaleDateString()}</div>' : ''}
            </div>

            <div style="position: absolute; bottom: 5px; left: 10px; font-size: 7px; color: #999;">
                ${companyName} | ID: 123
            </div>
        </div>
    `;

    $('#ticketPreview').html(html);
}

function saveConfig() {
    const data = {
        company_name: $('#company_name').val(),
        ticket_width_mm: parseFloat($('#ticket_width_mm').val()),
        ticket_height_mm: parseFloat($('#ticket_height_mm').val()),
        orientation: $('#orientation').val(),
        show_logo: $('#show_logo').is(':checked'),
        logo_height_mm: parseFloat($('#logo_height_mm').val()),
        logo_rotation: parseInt($('#logo_rotation').val()) || 0,
        print_offset_x: parseFloat($('#print_offset_x').val()) || 0,
        print_offset_y: parseFloat($('#print_offset_y').val()) || 0,
        elements: config.elements, // Keep existing element positions
        fields_visible: {
            project: $('#field_project').is(':checked'),
            part_name: $('#field_part_name').is(':checked'),
            dimensions: $('#field_dimensions').is(':checked'),
            material: $('#field_material').is(':checked'),
            edge_banding: $('#field_edge_banding').is(':checked'),
            date: $('#field_date').is(':checked'),
        }
    };

    console.log('=== SAVING CONFIG ===');
    console.log('Data to save:', JSON.stringify(data, null, 2));
    console.log('URL:', '{{ route('ticket-layout.update') }}');
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route('ticket-layout.update') }}',
        method: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            console.log('=== SAVE SUCCESS ===');
            console.log('Response:', response);
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: response.message,
                    timer: 2000
                });
            }
        },
        error: function(xhr) {
            console.error('=== SAVE ERROR ===');
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
            console.error('Full XHR:', xhr);
            
            let errorMsg = 'Ndodhi një gabim gjatë ruajtjes.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMsg = xhr.responseText.substring(0, 200);
            }
            Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: errorMsg,
            });
        }
    });
}

function resetConfig() {
    $.ajax({
        url: '{{ route('ticket-layout.reset') }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: response.message,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: 'Ndodhi një gabim.',
            });
        }
    });
}

function updateTextStyle(field, property, value) {
    const element = $(`#${field}_elem`);
    
    if (!element.length) {
        console.warn('Element not found:', field);
        return;
    }
    
    // Update config
    if (!config.elements) config.elements = {};
    if (!config.elements[field]) config.elements[field] = {};
    config.elements[field][property] = value;
    
    // Apply styles
    if (property === 'font_size') {
        element.css('font-size', value + 'px');
    } else if (property === 'rotation') {
        const transform = value === 0 ? 'none' : `rotate(${value}deg)`;
        element.css({
            'transform': transform,
            'transform-origin': 'left top'
        });
    } else if (property === 'bold') {
        element.css('font-weight', value ? 'bold' : 'normal');
    }
    
    console.log('Text style updated:', field, property, value);
}

function updateSVGRotation(rotation) {
    const element = $('#svg_element');
    
    if (!element.length) {
        console.warn('SVG element not found');
        return;
    }
    
    // Update config
    if (!config.elements) config.elements = {};
    if (!config.elements.svg_diagram) config.elements.svg_diagram = {};
    config.elements.svg_diagram.rotation = rotation;
    
    // Apply rotation
    const transform = rotation === 0 ? 'none' : `rotate(${rotation}deg)`;
    element.css({
        'transform': transform,
        'transform-origin': 'left top'
    });
    
    console.log('SVG rotation updated:', rotation);
}

function updateLogoRotation(rotation) {
    const element = $('#logo_element img');
    
    if (!element.length) {
        console.warn('Logo element not found');
        return;
    }
    
    // Update config
    if (!config.logo_rotation) config.logo_rotation = 0;
    config.logo_rotation = rotation;
    
    // Apply rotation
    const transform = rotation === 0 ? 'none' : `rotate(${rotation}deg)`;
    element.css({
        'transform': transform,
        'transform-origin': 'center center'
    });
    
    console.log('Logo rotation updated:', rotation);
}
</script>
@endpush
@endsection
