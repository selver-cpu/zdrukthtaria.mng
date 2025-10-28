#!/usr/bin/env node

/**
 * Cutlist Optimizer për Carpentry Management System
 * Algoritëm: Guillotine Cut me First Fit Decreasing Height (FFDH)
 */

class CutlistOptimizer {
    constructor(stockWidth, stockHeight, sawKerf = 4) {
        this.stockWidth = stockWidth;
        this.stockHeight = stockHeight;
        this.sawKerf = sawKerf;
        this.sheets = [];
    }

    /**
     * Optimizo listën e pjesëve
     */
    optimize(pieces) {
        // Sort pieces by area (largest first) for better packing
        const sortedPieces = pieces.sort((a, b) => {
            const areaA = a.width * a.height;
            const areaB = b.width * b.height;
            // If areas are similar, prefer longer pieces
            if (Math.abs(areaA - areaB) < 1000) {
                return Math.max(b.width, b.height) - Math.max(a.width, a.height);
            }
            return areaB - areaA;
        });

        // Try to place each piece
        for (const piece of sortedPieces) {
            let placed = false;

            // Try to place in existing sheets
            for (const sheet of this.sheets) {
                if (this.placePiece(sheet, piece)) {
                    placed = true;
                    break;
                }
            }

            // If not placed, create new sheet
            if (!placed) {
                const newSheet = {
                    id: this.sheets.length + 1,
                    width: this.stockWidth,
                    height: this.stockHeight,
                    pieces: [],
                    freeRectangles: [{
                        x: 0,
                        y: 0,
                        width: this.stockWidth,
                        height: this.stockHeight
                    }]
                };
                
                if (this.placePiece(newSheet, piece)) {
                    this.sheets.push(newSheet);
                } else {
                    // Piece cannot fit - skip silently or log to result
                    // Don't use console.error as it corrupts JSON output
                }
            }
        }

        return this.generateReport();
    }

    /**
     * Vendos një pjesë në tabakë
     */
    placePiece(sheet, piece) {
        // Try both orientations
        const orientations = [
            { width: piece.width, height: piece.height, rotated: false },
            { width: piece.height, height: piece.width, rotated: true }
        ];

        for (const orientation of orientations) {
            // Find best fitting rectangle
            let bestRect = null;
            let bestScore = Infinity;

            for (const rect of sheet.freeRectangles) {
                if (rect.width >= orientation.width + this.sawKerf && 
                    rect.height >= orientation.height + this.sawKerf) {
                    // Score: Bottom-Left strategy (prefer lower Y, then lower X, then tighter fit)
                    const score = rect.y * 10000 + rect.x * 100 + 
                                  (rect.width - orientation.width) + 
                                  (rect.height - orientation.height);
                    if (score < bestScore) {
                        bestScore = score;
                        bestRect = rect;
                    }
                }
            }

            if (bestRect) {
                // Place piece
                const placedPiece = {
                    ...piece,
                    x: bestRect.x,
                    y: bestRect.y,
                    width: orientation.width,
                    height: orientation.height,
                    rotated: orientation.rotated
                };
                sheet.pieces.push(placedPiece);

                // Update free rectangles (Guillotine split)
                this.splitRectangle(sheet, bestRect, orientation.width, orientation.height);

                return true;
            }
        }

        return false;
    }

    /**
     * Split rectangle after placing a piece
     */
    splitRectangle(sheet, rect, pieceWidth, pieceHeight) {
        // Remove used rectangle
        const index = sheet.freeRectangles.indexOf(rect);
        sheet.freeRectangles.splice(index, 1);

        // Add remaining rectangles
        const rightWidth = rect.width - pieceWidth - this.sawKerf;
        const bottomHeight = rect.height - pieceHeight - this.sawKerf;

        // Right rectangle (full height)
        if (rightWidth > 0) {
            sheet.freeRectangles.push({
                x: rect.x + pieceWidth + this.sawKerf,
                y: rect.y,
                width: rightWidth,
                height: rect.height
            });
        }

        // Bottom rectangle
        if (bottomHeight > 0) {
            sheet.freeRectangles.push({
                x: rect.x,
                y: rect.y + pieceHeight + this.sawKerf,
                width: rect.width,
                height: bottomHeight
            });
        }
    }

    /**
     * Gjenero raport
     */
    generateReport() {
        const totalArea = this.stockWidth * this.stockHeight;
        let usedArea = 0;
        let totalPieces = 0;

        for (const sheet of this.sheets) {
            for (const piece of sheet.pieces) {
                usedArea += piece.width * piece.height;
                totalPieces++;
            }
        }

        const efficiency = (usedArea / (totalArea * this.sheets.length)) * 100;
        const wasteArea = (totalArea * this.sheets.length) - usedArea;

        return {
            sheets: this.sheets,
            summary: {
                totalSheets: this.sheets.length,
                totalPieces: totalPieces,
                efficiency: efficiency.toFixed(2),
                usedArea: usedArea.toFixed(2),
                wasteArea: wasteArea.toFixed(2),
                stockSize: `${this.stockWidth}×${this.stockHeight}mm`
            }
        };
    }
}

// CLI Interface
if (require.main === module) {
    const fs = require('fs');
    
    // Read input from stdin or file
    const inputFile = process.argv[2];
    
    if (!inputFile) {
        console.error('Usage: node optimizer.js <input.json>');
        process.exit(1);
    }

    const input = JSON.parse(fs.readFileSync(inputFile, 'utf8'));
    
    const optimizer = new CutlistOptimizer(
        input.stockWidth || 2800,
        input.stockHeight || 2070,
        input.sawKerf || 4
    );

    const result = optimizer.optimize(input.pieces);
    
    // Output JSON
    console.log(JSON.stringify(result, null, 2));
}

module.exports = CutlistOptimizer;
