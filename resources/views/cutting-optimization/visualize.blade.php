<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plani i Prerjes - {{ $projekt->emri_projektit }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header {
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .header .info {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .visualization-area {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .canvas-container {
            background: #ecf0f1;
            border-radius: 8px;
            padding: 20px;
            min-height: 600px;
        }
        
        .pieces-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .piece-card {
            background: white;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .piece-card h4 {
            margin-bottom: 8px;
            color: #2c3e50;
        }
        
        .piece-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 13px;
            color: #555;
        }
        
        .piece-info span {
            display: block;
        }
        
        .piece-info .label {
            color: #999;
            font-size: 11px;
        }
        
        .piece-info .value {
            font-weight: bold;
            color: #333;
        }
        
        .summary {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .summary h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        
        .summary-item .label {
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #1976d2;
        }
        
        .actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📐 Plani i Prerjes - Cutting Plan</h1>
            <div class="info">
                <strong>Projekti:</strong> {{ $projekt->emri_projektit }}<br>
                <strong>Klienti:</strong> {{ $projekt->klient->emri_klientit ?? 'N/A' }}<br>
                <strong>Data:</strong> {{ date('d.m.Y H:i') }}
            </div>
        </div>
        
        <div class="visualization-area">
            <div class="canvas-container">
                <h3 style="margin-bottom: 15px; color: #2c3e50;">Vizualizimi i Copëve</h3>
                <svg id="cuttingCanvas" width="100%" height="550" style="background: white; border-radius: 4px;"></svg>
            </div>
            
            <div class="pieces-list">
                <h3 style="margin-bottom: 15px; color: #2c3e50;">Lista e Copëve</h3>
                @foreach($visualization as $index => $piece)
                    <div class="piece-card" style="border-left-color: {{ $piece['color'] }}">
                        <h4>{{ $piece['label'] }}</h4>
                        <p style="font-size: 12px; color: #666; margin-bottom: 8px;">{{ $piece['material'] }}</p>
                        <div class="piece-info">
                            <div>
                                <span class="label">Gjatësia</span>
                                <span class="value">{{ $piece['length'] }} mm</span>
                            </div>
                            <div>
                                <span class="label">Gjerësia</span>
                                <span class="value">{{ $piece['width'] }} mm</span>
                            </div>
                            <div>
                                <span class="label">Trashësia</span>
                                <span class="value">{{ $piece['thickness'] }} mm</span>
                            </div>
                            <div>
                                <span class="label">Sasia</span>
                                <span class="value">x{{ $piece['quantity'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="summary">
            <h3>📊 Përmbledhje</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Copë Totale</div>
                    <div class="value">{{ count($visualization) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Sasi Totale</div>
                    <div class="value">{{ array_sum(array_column($visualization, 'quantity')) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Sipërfaqja Totale</div>
                    <div class="value">{{ number_format(array_sum(array_column($visualization, 'area')), 2) }} m²</div>
                </div>
                <div class="summary-item">
                    <div class="label">Materiale të Ndryshme</div>
                    <div class="value">{{ count(array_unique(array_column($visualization, 'material'))) }}</div>
                </div>
            </div>
        </div>
        
        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">🖨️ Printo</button>
            <a href="{{ route('cutting-optimization.export', $projekt->projekt_id) }}" class="btn btn-primary">💾 Shkarko XML</a>
            <a href="{{ route('cutting-optimization.index', ['projekt_id' => $projekt->projekt_id]) }}" class="btn btn-secondary">← Kthehu</a>
        </div>
    </div>
    
    <script>
        // Simple SVG visualization
        const pieces = @json($visualization);
        const svg = document.getElementById('cuttingCanvas');
        const svgWidth = svg.clientWidth;
        const svgHeight = 550;
        
        // Calculate scale to fit all pieces
        const maxLength = Math.max(...pieces.map(p => p.length));
        const maxWidth = Math.max(...pieces.map(p => p.width));
        const scale = Math.min((svgWidth - 40) / maxLength, (svgHeight - 40) / maxWidth);
        
        let currentY = 20;
        let currentX = 20;
        let rowHeight = 0;
        
        pieces.forEach((piece, index) => {
            const width = piece.length * scale;
            const height = piece.width * scale;
            
            // Check if we need a new row
            if (currentX + width > svgWidth - 20) {
                currentX = 20;
                currentY += rowHeight + 10;
                rowHeight = 0;
            }
            
            // Draw rectangle
            const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            rect.setAttribute('x', currentX);
            rect.setAttribute('y', currentY);
            rect.setAttribute('width', width);
            rect.setAttribute('height', height);
            rect.setAttribute('fill', piece.color);
            rect.setAttribute('stroke', '#333');
            rect.setAttribute('stroke-width', '2');
            rect.setAttribute('opacity', '0.8');
            svg.appendChild(rect);
            
            // Add label
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', currentX + width / 2);
            text.setAttribute('y', currentY + height / 2);
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('dominant-baseline', 'middle');
            text.setAttribute('fill', 'white');
            text.setAttribute('font-size', '12');
            text.setAttribute('font-weight', 'bold');
            text.textContent = `${piece.length}x${piece.width}`;
            svg.appendChild(text);
            
            // Add quantity badge if > 1
            if (piece.quantity > 1) {
                const badge = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                badge.setAttribute('cx', currentX + width - 10);
                badge.setAttribute('cy', currentY + 10);
                badge.setAttribute('r', '12');
                badge.setAttribute('fill', '#e74c3c');
                badge.setAttribute('stroke', 'white');
                badge.setAttribute('stroke-width', '2');
                svg.appendChild(badge);
                
                const badgeText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                badgeText.setAttribute('x', currentX + width - 10);
                badgeText.setAttribute('y', currentY + 10);
                badgeText.setAttribute('text-anchor', 'middle');
                badgeText.setAttribute('dominant-baseline', 'middle');
                badgeText.setAttribute('fill', 'white');
                badgeText.setAttribute('font-size', '10');
                badgeText.setAttribute('font-weight', 'bold');
                badgeText.textContent = `x${piece.quantity}`;
                svg.appendChild(badgeText);
            }
            
            currentX += width + 10;
            rowHeight = Math.max(rowHeight, height);
        });
    </script>
</body>
</html>
