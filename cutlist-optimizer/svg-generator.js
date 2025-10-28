#!/usr/bin/env node

/**
 * SVG Generator për Cutlist Optimizer
 * Gjeneron diagrame profesionale me logo, barcode, grid, etj.
 */

class SVGGenerator {
    constructor(options = {}) {
        this.scale = options.scale || 0.2; // 1mm = 0.2px
        this.padding = options.padding || 50;
        this.showGrid = options.showGrid !== false;
        this.showLogo = options.showLogo !== false;
        this.showBarcode = options.showBarcode !== false;
        this.showLabels = options.showLabels !== false;
        this.colors = [
            '#3498db', '#2ecc71', '#f39c12', '#e74c3c', 
            '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
        ];
    }

    /**
     * Gjenero SVG për një tabakë
     */
    generateSheet(sheet, sheetNumber, totalSheets) {
        const width = sheet.width * this.scale + this.padding * 2;
        const height = sheet.height * this.scale + this.padding * 2;

        let svg = `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" 
     xmlns:xlink="http://www.w3.org/1999/xlink"
     width="${width}" 
     height="${height + 150}" 
     viewBox="0 0 ${width} ${height + 150}">
    
    <!-- Definitions -->
    <defs>
        <style>
            .sheet-border { fill: #ecf0f1; stroke: #2c3e50; stroke-width: 2; }
            .piece { stroke: #2c3e50; stroke-width: 1.5; opacity: 0.9; }
            .piece:hover { opacity: 1; stroke-width: 2; }
            .piece-label { font-family: Arial, sans-serif; font-size: 12px; fill: #2c3e50; font-weight: bold; }
            .piece-dimensions { font-family: Arial, sans-serif; font-size: 10px; fill: #34495e; }
            .grid-line { stroke: #bdc3c7; stroke-width: 0.5; opacity: 0.3; }
            .header-text { font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; fill: #2c3e50; }
            .info-text { font-family: Arial, sans-serif; font-size: 12px; fill: #7f8c8d; }
            .edge-banding { stroke-width: 3; opacity: 0.8; }
        </style>
        
        <!-- Arrow marker for grain direction -->
        <marker id="arrow" markerWidth="10" markerHeight="10" refX="5" refY="3" orient="auto" markerUnits="strokeWidth">
            <path d="M0,0 L0,6 L9,3 z" fill="#e74c3c" />
        </marker>
    </defs>

    <!-- Header -->
    <g id="header">
        ${this.generateHeader(sheet, sheetNumber, totalSheets)}
    </g>

    <!-- Main content group -->
    <g transform="translate(${this.padding}, ${this.padding + 100})">
        
        <!-- Sheet background -->
        <rect class="sheet-border" 
              x="0" y="0" 
              width="${sheet.width * this.scale}" 
              height="${sheet.height * this.scale}" 
              rx="5" />
        
        ${this.showGrid ? this.generateGrid(sheet) : ''}
        
        <!-- Pieces -->
        ${this.generatePieces(sheet)}
        
        <!-- Sheet dimensions -->
        ${this.generateSheetDimensions(sheet)}
    </g>
    
    <!-- Footer -->
    <g id="footer" transform="translate(0, ${height + 100})">
        ${this.generateFooter(sheet)}
    </g>
</svg>`;

        return svg;
    }

    /**
     * Gjenero header me logo dhe info
     */
    generateHeader(sheet, sheetNumber, totalSheets) {
        const centerX = (sheet.width * this.scale + this.padding * 2) / 2;
        
        return `
        <!-- Logo (placeholder) -->
        ${this.showLogo ? `
        <rect x="20" y="20" width="60" height="60" fill="#3498db" rx="5" />
        <text x="50" y="55" text-anchor="middle" fill="white" font-size="20" font-weight="bold">CL</text>
        ` : ''}
        
        <!-- Title -->
        <text x="${centerX}" y="40" text-anchor="middle" class="header-text">
            CUTLIST OPTIMIZER - Tabaka ${sheetNumber}/${totalSheets}
        </text>
        
        <!-- Sheet info -->
        <text x="${centerX}" y="65" text-anchor="middle" class="info-text">
            Madhësia: ${sheet.width}mm × ${sheet.height}mm | Pjesë: ${sheet.pieces.length}
        </text>
        `;
    }

    /**
     * Gjenero grid lines
     */
    generateGrid(sheet) {
        let grid = '';
        const gridSize = 100; // 100mm grid
        
        // Vertical lines
        for (let x = 0; x <= sheet.width; x += gridSize) {
            const xPos = x * this.scale;
            grid += `<line class="grid-line" x1="${xPos}" y1="0" x2="${xPos}" y2="${sheet.height * this.scale}" />`;
        }
        
        // Horizontal lines
        for (let y = 0; y <= sheet.height; y += gridSize) {
            const yPos = y * this.scale;
            grid += `<line class="grid-line" x1="0" y1="${yPos}" x2="${sheet.width * this.scale}" y2="${yPos}" />`;
        }
        
        return `<g id="grid">${grid}</g>`;
    }

    /**
     * Gjenero pjesët
     */
    generatePieces(sheet) {
        let pieces = '';
        
        sheet.pieces.forEach((piece, index) => {
            const x = piece.x * this.scale;
            const y = piece.y * this.scale;
            const w = piece.width * this.scale;
            const h = piece.height * this.scale;
            const color = this.colors[index % this.colors.length];
            const pieceNumber = index + 1;
            
            pieces += `
            <g id="piece-${pieceNumber}" class="piece-group">
                <!-- Piece rectangle -->
                <rect class="piece" 
                      x="${x}" y="${y}" 
                      width="${w}" height="${h}" 
                      fill="${color}" 
                      data-piece-id="${piece.id}" />
                
                <!-- Edge banding indicators -->
                ${this.generateEdgeBanding(piece, x, y, w, h)}
                
                <!-- Piece number -->
                <text x="${x + w/2}" y="${y + h/2 - 10}" 
                      text-anchor="middle" class="piece-label">
                    #${pieceNumber} ${piece.name}
                </text>
                
                <!-- Dimensions -->
                <text x="${x + w/2}" y="${y + h/2 + 5}" 
                      text-anchor="middle" class="piece-dimensions">
                    ${piece.width}×${piece.height}mm
                </text>
                
                ${piece.rotated ? `
                <text x="${x + w/2}" y="${y + h/2 + 18}" 
                      text-anchor="middle" class="piece-dimensions" fill="#e74c3c">
                    ↻ Rotated
                </text>
                ` : ''}
                
                <!-- Barcode -->
                ${this.showBarcode ? this.generateBarcode(piece, x, y + h - 15, w) : ''}
            </g>
            `;
        });
        
        return `<g id="pieces">${pieces}</g>`;
    }

    /**
     * Gjenero edge banding indicators
     */
    generateEdgeBanding(piece, x, y, w, h) {
        if (!piece.edge_banding) return '';
        
        let edges = '';
        const edgeColor = '#e74c3c';
        
        if (piece.edge_banding.front) {
            edges += `<line class="edge-banding" x1="${x}" y1="${y}" x2="${x + w}" y2="${y}" stroke="${edgeColor}" />`;
        }
        if (piece.edge_banding.back) {
            edges += `<line class="edge-banding" x1="${x}" y1="${y + h}" x2="${x + w}" y2="${y + h}" stroke="${edgeColor}" />`;
        }
        if (piece.edge_banding.left) {
            edges += `<line class="edge-banding" x1="${x}" y1="${y}" x2="${x}" y2="${y + h}" stroke="${edgeColor}" />`;
        }
        if (piece.edge_banding.right) {
            edges += `<line class="edge-banding" x1="${x + w}" y1="${y}" x2="${x + w}" y2="${y + h}" stroke="${edgeColor}" />`;
        }
        
        return edges;
    }

    /**
     * Gjenero barcode (simplified)
     */
    generateBarcode(piece, x, y, width) {
        const barcodeWidth = Math.min(width, 80);
        const barcodeX = x + (width - barcodeWidth) / 2;
        
        return `
        <g class="barcode">
            <rect x="${barcodeX}" y="${y}" width="${barcodeWidth}" height="12" fill="white" stroke="#2c3e50" stroke-width="0.5" />
            <text x="${barcodeX + barcodeWidth/2}" y="${y + 9}" 
                  text-anchor="middle" 
                  font-family="monospace" 
                  font-size="8" 
                  fill="#2c3e50">
                ${piece.id}
            </text>
        </g>
        `;
    }

    /**
     * Gjenero sheet dimensions
     */
    generateSheetDimensions(sheet) {
        const w = sheet.width * this.scale;
        const h = sheet.height * this.scale;
        
        return `
        <g class="dimensions">
            <!-- Width dimension -->
            <line x1="0" y1="${h + 20}" x2="${w}" y2="${h + 20}" stroke="#2c3e50" stroke-width="1" marker-end="url(#arrow)" marker-start="url(#arrow)" />
            <text x="${w/2}" y="${h + 35}" text-anchor="middle" class="info-text">${sheet.width}mm</text>
            
            <!-- Height dimension -->
            <line x1="${w + 20}" y1="0" x2="${w + 20}" y2="${h}" stroke="#2c3e50" stroke-width="1" marker-end="url(#arrow)" marker-start="url(#arrow)" />
            <text x="${w + 35}" y="${h/2}" text-anchor="middle" class="info-text" transform="rotate(90, ${w + 35}, ${h/2})">${sheet.height}mm</text>
        </g>
        `;
    }

    /**
     * Gjenero footer
     */
    generateFooter(sheet) {
        const usedArea = sheet.pieces.reduce((sum, p) => sum + (p.width * p.height), 0);
        const totalArea = sheet.width * sheet.height;
        const efficiency = ((usedArea / totalArea) * 100).toFixed(1);
        const waste = totalArea - usedArea;
        
        return `
        <text x="20" y="20" class="info-text">
            Efikasitet: ${efficiency}% | Mbetje: ${waste.toFixed(0)}mm² (${((waste/1000000)*100).toFixed(2)}m²)
        </text>
        <text x="20" y="40" class="info-text" font-size="10">
            Gjeneruar: ${new Date().toLocaleString('sq-AL')} | ColiDecor Carpentry System
        </text>
        `;
    }

    /**
     * Gjenero të gjitha sheets
     */
    generateAll(result) {
        const svgs = [];
        
        result.sheets.forEach((sheet, index) => {
            const svg = this.generateSheet(sheet, index + 1, result.sheets.length);
            svgs.push({
                sheetNumber: index + 1,
                svg: svg,
                filename: `cutlist_sheet_${index + 1}.svg`
            });
        });
        
        return svgs;
    }
}

// CLI Interface
if (require.main === module) {
    const fs = require('fs');
    
    const inputFile = process.argv[2];
    const outputDir = process.argv[3] || './output';
    
    if (!inputFile) {
        console.error('Usage: node svg-generator.js <result.json> [output-dir]');
        process.exit(1);
    }

    const result = JSON.parse(fs.readFileSync(inputFile, 'utf8'));
    const generator = new SVGGenerator({
        showGrid: true,
        showLogo: true,
        showBarcode: true,
        showLabels: true
    });

    const svgs = generator.generateAll(result);
    
    // Create output directory
    if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
    }

    // Save SVGs
    svgs.forEach(item => {
        const filepath = `${outputDir}/${item.filename}`;
        fs.writeFileSync(filepath, item.svg);
        console.log(`Generated: ${filepath}`);
    });

    console.log(`\nTotal sheets generated: ${svgs.length}`);
}

module.exports = SVGGenerator;
