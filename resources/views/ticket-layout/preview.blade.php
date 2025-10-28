@extends('layouts.print')

@section('title', 'PLC Ticket Preview')

@section('content')
@php
    $Wmm = $config['ticket_width_mm'] ?? 100;
    $Hmm = $config['ticket_height_mm'] ?? 75;
    $orientation = $config['orientation'] ?? 'landscape';
    if ($orientation === 'portrait') {
        [$Wmm, $Hmm] = [$Hmm, $Wmm];
    }
    $Wpx = $Wmm * 3; // SCALE = 3
    $Hpx = $Hmm * 3;
@endphp
<style>
/* Screen styles */
.ticket-card {
  width: {{ $Wpx }}px;
  height: {{ $Hpx }}px;
  margin: 20px auto;
  border: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  padding: 0;
  background: white;
  font-family: Arial, sans-serif;
  position: relative;
}

/* Print styles */
@media print {
  /* Hide everything except ticket */
  body * {
    visibility: hidden !important;
  }
  
  .ticket-card,
  .ticket-card * {
    visibility: visible !important;
  }
  
  /* Remove all screen-only elements */
  .d-print-none,
  .btn,
  .btn-group,
  button,
  a,
  nav,
  header,
  footer,
  .navbar,
  .sidebar {
    display: none !important;
    visibility: hidden !important;
  }
  
  /* Page setup */
  @page { 
    size: {{ $Wmm }}mm {{ $Hmm }}mm; 
    margin: 0; 
  }
  
  html, body { 
    width: {{ $Wmm }}mm; 
    height: {{ $Hmm }}mm; 
    margin: 0 !important; 
    padding: 0 !important;
    background: white !important;
  }
  
  /* Ticket card print styles */
  .ticket-card { 
    width: {{ $Wmm }}mm !important; 
    height: {{ $Hmm }}mm !important; 
    border: 0 !important; 
    outline: none !important;
    box-shadow: none !important;
    position: absolute !important; 
    left: {{ ($config['print_offset_x'] ?? 0) }}mm !important; 
    top: {{ ($config['print_offset_y'] ?? 0) }}mm !important; 
    margin: 0 !important;
    padding: 0 !important;
    font-family: Arial, sans-serif !important;
    background: white !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
  }

  /* Remove any residual borders/outlines/shadows inside ticket */
  .ticket-card, .ticket-card * {
    border: 0 !important;
    outline: none !important;
    box-shadow: none !important;
  }
}
</style>

@php
    // Gjenero tekstin për tiketë
    $projectName = $dimension->projekt->emri_projektit ?? 'N/A';
    $clientName = $dimension->projekt->klient->emri_klientit ?? 'N/A';
    $partName = $dimension->emri_pjeses ?? 'N/A';
    $dimensions = $dimension->gjatesia . ' × ' . $dimension->gjeresia . ' × ' . $dimension->trashesia . 'mm';
    $material = $dimension->materiali->emri_materialit ?? 'N/A';
    
    // Kantimi
    $kantimSides = [];
    if ($dimension->kantim_front) $kantimSides[] = 'P';
    if ($dimension->kantim_back) $kantimSides[] = 'Pr';
    if ($dimension->kantim_left) $kantimSides[] = 'M';
    if ($dimension->kantim_right) $kantimSides[] = 'D';
    $kantimText = !empty($kantimSides) 
        ? ($dimension->kantim_type ?? 'PVC') . ' ' . ($dimension->kantim_thickness ?? '0.8') . 'mm (' . implode(', ', $kantimSides) . ')'
        : 'Pa kantim';
@endphp

<div class="d-print-none" style="text-align: center; margin-bottom: 20px; padding: 20px; background: #f8f9fa;">
    <h3><i class="fas fa-eye"></i> Preview me të Dhëna Reale</h3>
    <p class="text-muted">Madhësia: {{ $Wmm }}mm × {{ $Hmm }}mm</p>
    
    <div class="alert alert-info" style="max-width: 600px; margin: 0 auto 15px;">
        <strong><i class="fas fa-info-circle"></i> Udhëzime për Printim:</strong>
        <ol style="text-align: left; margin-top: 10px;">
            <li>Kliko "Printo"</li>
            <li>Në Printer Settings:
                <ul>
                    <li><strong>Paper Size:</strong> Custom ({{ $Wmm }}mm × {{ $Hmm }}mm)</li>
                    <li><strong>Margins:</strong> None (0mm)</li>
                    <li><strong>Scale:</strong> 100% (mos e ndrysho!)</li>
                </ul>
            </li>
            <li>Nëse nuk del saktë, rregulloje <strong>Print Offset</strong> në Editor</li>
        </ol>
    </div>
    
    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print"></i> Printo
        </button>
        <button onclick="printTest()" class="btn btn-warning">
            <i class="fas fa-ruler"></i> Test Print (me kornizë)
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Mbyll
        </button>
    </div>
    
    <script>
    function printTest() {
        // Add test border for alignment only temporarily
        const card = document.querySelector('.ticket-card');
        const prev = card.style.border;
        card.style.border = '3px dashed red';
        window.print();
        setTimeout(() => {
            card.style.border = '0';
        }, 100);
    }
    </script>
</div>

<div class="ticket-card">
    @php
        $SCALE = 3; // 1mm = 3px
        $logoX = ($config['elements']['logo']['x'] ?? 2) * $SCALE;
        $logoY = ($config['elements']['logo']['y'] ?? 2) * $SCALE;
        $svgX = ($config['elements']['svg_diagram']['x'] ?? 5) * $SCALE;
        $svgY = ($config['elements']['svg_diagram']['y'] ?? 10) * $SCALE;
    @endphp

    @if($config['show_logo'] ?? true)
    @php
        $logoRotation = $config['logo_rotation'] ?? 0;
        $logoTransform = $logoRotation != 0 ? "transform: rotate({$logoRotation}deg); transform-origin: center center;" : '';
    @endphp
    <div style="position: absolute; left: {{ $logoX }}px; top: {{ $logoY }}px;">
        <img src="{{ asset('img/logo.png') }}" alt="{{ $config['company_name'] ?? 'Logo' }}" 
             style="height: {{ ($config['logo_height_mm'] ?? 6) * $SCALE }}px; {{ $logoTransform }}"
             onerror="this.style.display='none'">
    </div>
    @endif

    <!-- SVG Diagram -->
    @php
        $svgRotation = $config['elements']['svg_diagram']['rotation'] ?? 0;
        $svgTransform = $svgRotation != 0 ? "transform: rotate({$svgRotation}deg); transform-origin: left top;" : '';
        $svgWmm = $config['elements']['svg_diagram']['width_mm'] ?? 35;
        $svgHmm = $config['elements']['svg_diagram']['height_mm'] ?? 20;
        $edgeThickness = $config['elements']['svg_diagram']['edge_thickness'] ?? 4; // viewBox units
        $edgeColor = $config['elements']['svg_diagram']['edge_color'] ?? '#e74c3c';
        $showOutline = $config['elements']['svg_diagram']['show_outline'] ?? false;
        
        // Scale viewBox based on SVG size for proportional scaling
        // Add extra space on left for vertical label
        $viewBoxW = 115; // extra 15 for left label
        $viewBoxH = 75;  // extra 5 for top label
    @endphp
    <div style="position: absolute; left: {{ $svgX }}px; top: {{ $svgY }}px; {{ $svgTransform }}">
        <svg width="{{ $svgWmm * 3 }}px" height="{{ $svgHmm * 3 }}px" viewBox="-15 0 {{ $viewBoxW }} {{ $viewBoxH }}" preserveAspectRatio="none" style="border: none;">
            @php
                $edgeFront = (bool)$dimension->kantim_front;
                $edgeBack  = (bool)$dimension->kantim_back;
                $edgeLeft  = (bool)$dimension->kantim_left;
                $edgeRight = (bool)$dimension->kantim_right;
                $boldW = ($edgeFront || $edgeBack) ? 'font-weight: bold;' : '';
                $boldL = ($edgeLeft || $edgeRight) ? 'font-weight: bold;' : '';
                
                // Center box in viewBox with margin for edges and labels
                // Box size scales with SVG size
                $margin = $edgeThickness + 8;
                $centerX = 50;
                $centerY = 35;
                $boxW = 100 - 2*$margin;
                $boxH = 60 - 2*$margin;
                $boxX = $centerX - $boxW/2;
                $boxY = $centerY - $boxH/2;
            @endphp
            
            <!-- Background outline if enabled -->
            @if($showOutline)
                <rect x="{{ $boxX }}" y="{{ $boxY }}" width="{{ $boxW }}" height="{{ $boxH }}" fill="#f9f9f9" stroke="#ddd" stroke-width="0.5"/>
            @endif
            
            <!-- Kantim edges -->
            @if($edgeFront || $edgeBack || $edgeLeft || $edgeRight)
                @if($edgeFront)
                    <rect x="{{ $boxX }}" y="{{ $boxY - $edgeThickness }}" width="{{ $boxW }}" height="{{ $edgeThickness }}" fill="{{ $edgeColor }}" rx="0.5"/>
                @endif
                @if($edgeBack)
                    <rect x="{{ $boxX }}" y="{{ $boxY + $boxH }}" width="{{ $boxW }}" height="{{ $edgeThickness }}" fill="{{ $edgeColor }}" rx="0.5"/>
                @endif
                @if($edgeLeft)
                    <rect x="{{ $boxX - $edgeThickness }}" y="{{ $boxY }}" width="{{ $edgeThickness }}" height="{{ $boxH }}" fill="{{ $edgeColor }}" rx="0.5"/>
                @endif
                @if($edgeRight)
                    <rect x="{{ $boxX + $boxW }}" y="{{ $boxY }}" width="{{ $edgeThickness }}" height="{{ $boxH }}" fill="{{ $edgeColor }}" rx="0.5"/>
                @endif
            @endif
            
            <!-- Dimension labels -->
            @php
                // Auto-rotate: show longest dimension on longest side
                $dimW = $dimension->gjeresia;
                $dimL = $dimension->gjatesia;
                $autoRotate = ($svgWmm > $svgHmm && $dimL > $dimW) || ($svgHmm > $svgWmm && $dimW > $dimL);
                
                if ($autoRotate) {
                    // Swap dimensions
                    [$dimW, $dimL] = [$dimL, $dimW];
                    [$boldW, $boldL] = [$boldL, $boldW];
                }
            @endphp
            <text x="{{ $boxX + $boxW/2 }}" y="{{ $boxY - $edgeThickness - 4 }}" text-anchor="middle" font-size="11" font-family="Arial, sans-serif" fill="#000" font-weight="600" style="{{ $boldW }}">{{ $dimW }}mm</text>
            <text x="{{ $boxX - $edgeThickness - 6 }}" y="{{ $boxY + $boxH/2 }}" text-anchor="middle" font-size="11" font-family="Arial, sans-serif" fill="#000" font-weight="600" transform="rotate(-90 {{ $boxX - $edgeThickness - 6 }} {{ $boxY + $boxH/2 }})" style="{{ $boldL }}">{{ $dimL }}mm</text>
        </svg>
    </div>

    <!-- Fushat individuale me pozicione nga config -->
    @if($config['fields_visible']['company_name'] ?? true)
    @php 
        $pos = $config['elements']['field_company_name'] ?? ($config['elements']['company_name'] ?? ['x' => 43, 'y' => 7]);
        $fontSize = ($pos['font_size'] ?? 9) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? true;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; {{ $transform }} {{ $fontWeight }}">
        {{ $config['company_name'] ?? 'ColiDecor' }}
    </div>
    @endif
    @if($config['fields_visible']['project'] ?? true)
    @php 
        $pos = $config['elements']['field_project'] ?? ($config['elements']['project'] ?? ['x' => 43, 'y' => 10]);
        $fontSize = ($pos['font_size'] ?? 9) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? false;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; {{ $transform }} {{ $fontWeight }}">
        <span style="color: #666; font-weight: 600;">Projekt:</span> {{ $projectName }}
    </div>
    @if($config['show_field_separators'] ?? false)
        @php $sepY = $pos['y'] + (($pos['font_size'] ?? 9) / 3) + 1.5; @endphp
        <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $sepY * $SCALE }}px; width: {{ ($Wmm - $pos['x'] - 2) * $SCALE }}px; height: 1px; background: repeating-linear-gradient(to right, #999 0, #999 3px, transparent 3px, transparent 6px); -webkit-print-color-adjust: exact; print-color-adjust: exact;"></div>
    @endif
    @endif

    @if($config['fields_visible']['part_name'] ?? true)
    @php 
        $pos = $config['elements']['field_part_name'] ?? ($config['elements']['part_name'] ?? ['x' => 43, 'y' => 13.3]);
        $fontSize = ($pos['font_size'] ?? 9) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? false;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; {{ $transform }} {{ $fontWeight }}">
        <span style="color: #666; font-weight: 600;">Pjesa:</span> {{ $partName }}
    </div>
    @if($config['show_field_separators'] ?? false)
        @php $sepY = $pos['y'] + (($pos['font_size'] ?? 9) / 3) + 1.5; @endphp
        <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $sepY * $SCALE }}px; width: {{ ($Wmm - $pos['x'] - 2) * $SCALE }}px; height: 1px; background: repeating-linear-gradient(to right, #999 0, #999 3px, transparent 3px, transparent 6px); -webkit-print-color-adjust: exact; print-color-adjust: exact;"></div>
    @endif
    @endif

    @if($config['fields_visible']['dimensions'] ?? true)
    @php 
        $pos = $config['elements']['field_dimensions'] ?? ($config['elements']['dimensions'] ?? ['x' => 43, 'y' => 16.6]);
        $fontSize = ($pos['font_size'] ?? 11) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? true;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; letter-spacing: 0.3px; {{ $transform }} {{ $fontWeight }}">
        {{ $dimensions }}
    </div>
    @if($config['show_field_separators'] ?? false)
        @php $sepY = $pos['y'] + (($pos['font_size'] ?? 11) / 3) + 1.5; @endphp
        <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $sepY * $SCALE }}px; width: {{ ($Wmm - $pos['x'] - 2) * $SCALE }}px; height: 1px; background: repeating-linear-gradient(to right, #999 0, #999 3px, transparent 3px, transparent 6px); -webkit-print-color-adjust: exact; print-color-adjust: exact;"></div>
    @endif
    @endif

    @if($config['fields_visible']['material'] ?? true)
    @php 
        $pos = $config['elements']['field_material'] ?? ($config['elements']['material'] ?? ['x' => 43, 'y' => 20.6]);
        $fontSize = ($pos['font_size'] ?? 9) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? false;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; {{ $transform }} {{ $fontWeight }}">
        <span style="color: #666; font-weight: 600;">Material:</span> {{ $material }}
    </div>
    @if($config['show_field_separators'] ?? false)
        @php $sepY = $pos['y'] + (($pos['font_size'] ?? 9) / 3) + 1.5; @endphp
        <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $sepY * $SCALE }}px; width: {{ ($Wmm - $pos['x'] - 2) * $SCALE }}px; height: 1px; background: repeating-linear-gradient(to right, #999 0, #999 3px, transparent 3px, transparent 6px); -webkit-print-color-adjust: exact; print-color-adjust: exact;"></div>
    @endif
    @endif

    @if($config['fields_visible']['edge_banding'] ?? true)
    @php 
        $pos = $config['elements']['field_edge_banding'] ?? ($config['elements']['edge_banding'] ?? ['x' => 43, 'y' => 24]);
        $fontSize = ($pos['font_size'] ?? 9) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? false;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; {{ $transform }} {{ $fontWeight }}">
        <span style="color: #666; font-weight: 600;">Kantim:</span> {{ $kantimText }}
    </div>
    @if($config['show_field_separators'] ?? false)
        @php $sepY = $pos['y'] + (($pos['font_size'] ?? 9) / 3) + 1.5; @endphp
        <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $sepY * $SCALE }}px; width: {{ ($Wmm - $pos['x'] - 2) * $SCALE }}px; height: 1px; background: repeating-linear-gradient(to right, #999 0, #999 3px, transparent 3px, transparent 6px); -webkit-print-color-adjust: exact; print-color-adjust: exact;"></div>
    @endif
    @endif

    @if($config['fields_visible']['date'] ?? true)
    @php 
        $pos = $config['elements']['field_date'] ?? ($config['elements']['date'] ?? ['x' => 43, 'y' => 27.3]);
        $fontSize = ($pos['font_size'] ?? 8) . 'px';
        $rotation = $pos['rotation'] ?? 0;
        $bold = $pos['bold'] ?? false;
        $transform = $rotation != 0 ? "transform: rotate({$rotation}deg); transform-origin: left top;" : '';
        $fontWeight = $bold ? 'font-weight: bold;' : '';
    @endphp
    <div style="position: absolute; left: {{ $pos['x'] * $SCALE }}px; top: {{ $pos['y'] * $SCALE }}px; font-size: {{ $fontSize }}; font-family: Arial, sans-serif; color: #777; {{ $transform }} {{ $fontWeight }}">
        {{ now()->format('d/m/Y H:i') }}
    </div>
    @endif

    <!-- Footer -->
    @if($config['show_footer'] ?? false)
    <div style="position: absolute; bottom: 5px; left: 10px; font-size: 7px; color: #999;">
        {{ $config['company_name'] ?? 'ColiDecor' }} | ID: {{ $dimension->id }}
    </div>
    @endif
</div>

@endsection
