@extends('layouts.print')

@section('title', 'PLC Ticket')

@section('content')
<style>
@media print {
  @page { size: 100mm 75mm; margin: 0; }
  html, body { width: 100mm; height: 75mm; margin: 0; padding: 0; }
  body * { visibility: hidden !important; }
  .ticket-card { visibility: visible !important; }
  .ticket-card * { visibility: visible !important; }
  .ticket-card { 
    width: 96mm; height: 71mm; 
    border: 0; 
    position: absolute; 
    left: 2mm; top: 2mm; 
    padding: 2mm;
    font-family: Arial, sans-serif;
    -webkit-print-color-adjust: exact;
    color-adjust: exact;
  }
  .d-print-none { display: none !important; }
}

.ticket-card {
  max-width: 400px;
  margin: 20px auto;
  border: 0;
  padding: 15px;
}

.ticket-row {
  display: flex;
  margin-bottom: 3mm;
}

.svg-col {
  width: 35mm;
  margin-right: 3mm;
}

.text-col {
  flex: 1;
}

.field {
  margin-bottom: 1mm;
  font-size: 8px;
}

.field-label {
  color: #666;
  font-size: 7px;
}

.field-value {
  font-weight: bold;
  font-size: 9px;
}

.logo {
  text-align: right;
  margin-bottom: 2mm;
}

.logo img {
  height: 4mm;
}

.svg-container {
  width: 35mm;
  height: 20mm;
  border: none;
  background: #f9f9f9;
}

/* Compact size for desktop */
.ticket-size-compact .ticket-card {
  max-width: 300px;
}
.ticket-size-compact .field {
  margin-bottom: 0.5mm;
}
.ticket-size-compact .field-label {
  font-size: 6px;
}
.ticket-size-compact .field-value {
  font-size: 8px;
}
.ticket-size-compact .svg-col {
  width: 30mm;
}
.ticket-size-compact .svg-container {
  width: 30mm;
  height: 18mm;
}

/* Full size for desktop (default) */
.ticket-size-full .ticket-card {
  max-width: 400px;
}

@media print {
  .field { margin-bottom: 0.5mm; }
  .field-label { display: none !important; }
  .field-value { font-size: 9px; font-weight: bold; }
  svg { -webkit-print-color-adjust: exact; color-adjust: exact; }
  .edge-banding { -webkit-print-color-adjust: exact; color-adjust: exact; }
}
</style>

@php($ticket = $dimension->tekstiPerPLC())

<div class="ticket-card">
  <div class="d-print-none" style="text-align: center; margin-bottom: 10px;">
    <div class="btn-group" role="group" style="margin-right: 10px;">
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setTicketSize('compact')">
        <i class="fas fa-compress"></i> Compact
      </button>
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setTicketSize('full')">
        <i class="fas fa-expand"></i> Full
      </button>
    </div>
    <a href="{{ route('projektet-dimensions.show', $dimension) }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left"></i> Kthehu
    </a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
      <i class="fas fa-print"></i> Printo
    </button>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <!-- Ana e majtë: SVG dhe tekstet poshtë tij -->
    <div style="width: 60%;">
      <!-- SVG -->
      <div style="margin-bottom: 2mm;">
        <svg width="25mm" height="15mm" viewBox="0 0 100 60" style="border: 1px solid #ccc;">
          <!-- Pjesa kryesore -->
          <rect x="20" y="15" width="60" height="30" 
                fill="white" stroke="#333" stroke-width="2"/>
          
          <!-- Kantimet (vetëm nëse ka kantim) -->
          @if($dimension->kantim_front || $dimension->kantim_back || $dimension->kantim_left || $dimension->kantim_right)
            @if($dimension->kantim_front)
              <rect x="20" y="12" width="60" height="3" fill="#e74c3c"/>
            @endif
            @if($dimension->kantim_back)
              <rect x="20" y="45" width="60" height="3" fill="#e74c3c"/>
            @endif
            @if($dimension->kantim_left)
              <rect x="17" y="15" width="3" height="30" fill="#e74c3c"/>
            @endif
            @if($dimension->kantim_right)
              <rect x="80" y="15" width="3" height="30" fill="#e74c3c"/>
            @endif
          @else
            <!-- Pa kantim - tekst në qendër -->
            <text x="50" y="35" text-anchor="middle" font-size="8" fill="#999">Pa kantim</text>
          @endif
          
          <!-- Dimensionet brenda SVG -->
          <text x="50" y="8" text-anchor="middle" font-size="6" fill="#666">{{ $dimension->gjeresia }}mm</text>
          <text x="12" y="32" text-anchor="middle" font-size="6" fill="#666" transform="rotate(-90 12 32)">{{ $dimension->gjatesia }}mm</text>
        </svg>
      </div>
      
      <!-- Logo poshtë SVG -->
      <div style="text-align: center; margin-bottom: 1mm;">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" style="height: 3mm;">
      </div>
      
      <!-- Tekstet poshtë logos -->
      <div style="font-size: 7px;">
        <div class="field" style="margin-bottom: 0.5mm;">
          <div class="field-label">Klienti</div>
          <div class="field-value">{{ $ticket['project'] }}</div>
        </div>
        
        <div class="field" style="margin-bottom: 0.5mm;">
          <div class="field-label">Pjesa</div>
          <div class="field-value">{{ $ticket['part'] }}</div>
        </div>
        
        <div class="field" style="margin-bottom: 0.5mm;">
          <div class="field-label">Dimensionet</div>
          <div class="field-value">{{ $ticket['dimensions'] }}</div>
        </div>
        
        <div class="field" style="margin-bottom: 0.5mm;">
          <div class="field-label">Materiali</div>
          <div class="field-value">{{ $ticket['material'] }}</div>
        </div>
        
        <div class="field" style="margin-bottom: 0.5mm;">
          <div class="field-label">Kantimi</div>
          <div class="field-value">{{ $ticket['edge_banding'] }}</div>
        </div>
        
        <div class="field">
          <div class="field-label">Data</div>
          <div class="field-value">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
      </div>
    </div>

    <!-- Ana e djathtë: Hapësirë -->
    <div style="width: 35%;">
    </div>
  </div>
</div>

@push('scripts')
<script>
function setTicketSize(size) {
    // Remove existing size classes
    document.body.classList.remove('ticket-size-compact', 'ticket-size-full');

    // Add the selected size class
    document.body.classList.add('ticket-size-' + size);

    // Update button states
    const buttons = document.querySelectorAll('[onclick*="setTicketSize"]');
    buttons.forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
    });

    // Highlight the active button
    const activeBtn = document.querySelector('[onclick="setTicketSize(\'' + size + '\')"]');
    if (activeBtn) {
        activeBtn.classList.remove('btn-outline-secondary');
        activeBtn.classList.add('btn-primary');
    }

    // Store preference in localStorage
    localStorage.setItem('ticketSize', size);
}

// Load saved preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedSize = localStorage.getItem('ticketSize') || 'compact';
    setTicketSize(savedSize);

    // Force compact on print to ensure single, smaller ticket
    window.addEventListener('beforeprint', function() {
        setTicketSize('compact');
    });
    window.addEventListener('afterprint', function() {
        const sz = localStorage.getItem('ticketSize') || 'compact';
        setTicketSize(sz);
    });
});
</script>
@endpush

@endsection
