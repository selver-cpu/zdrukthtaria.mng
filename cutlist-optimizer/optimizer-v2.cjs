#!/usr/bin/env node

/**
 * Cutlist Optimizer v2 - Maximal Rectangles Algorithm
 * Më optimal se Guillotine Cut
 */

class CutlistOptimizerV2 {
    constructor(stockWidth, stockHeight, sawKerf = 4) {
        this.stockWidth = stockWidth;
        this.stockHeight = stockHeight;
        this.sawKerf = sawKerf;
        this.sheets = [];
    }

    optimize(pieces) {
        // Sort by longest side first (better for packing)
        const sortedPieces = [...pieces].sort((a, b) => {
            const maxA = Math.max(a.width, a.height);
            const maxB = Math.max(b.width, b.height);
            if (maxA !== maxB) return maxB - maxA;
            // If same max, sort by area
            return (b.width * b.height) - (a.width * a.height);
        });

        for (const piece of sortedPieces) {
            let placed = false;

            // Try existing sheets first
            for (const sheet of this.sheets) {
                if (this.placePiece(sheet, piece)) {
                    placed = true;
                    break;
                }
            }

            // Create new sheet if needed
            if (!placed) {
                const newSheet = this.createNewSheet();
                if (this.placePiece(newSheet, piece)) {
                    this.sheets.push(newSheet);
                }
            }
        }

        return this.generateReport();
    }

    createNewSheet() {
        return {
            id: this.sheets.length + 1,
            width: this.stockWidth,
            height: this.stockHeight,
            pieces: [],
            freeRects: [{
                x: 0,
                y: 0,
                width: this.stockWidth,
                height: this.stockHeight
            }]
        };
    }

    placePiece(sheet, piece) {
        const orientations = [
            { w: piece.width, h: piece.height, rotated: false },
            { w: piece.height, h: piece.width, rotated: true }
        ];

        let bestScore = -1;
        let bestRect = null;
        let bestOrientation = null;

        // Try all free rectangles and orientations
        for (const rect of sheet.freeRects) {
            for (const orient of orientations) {
                if (rect.width >= orient.w && rect.height >= orient.h) {
                    // Guillotine constraint: prefer cuts that align with edges
                    const alignedX = (rect.x === 0 || rect.x + orient.w === this.stockWidth);
                    const alignedY = (rect.y === 0 || rect.y + orient.h === this.stockHeight);
                    const alignBonus = (alignedX ? 10000 : 0) + (alignedY ? 10000 : 0);
                    
                    // Score: Bottom-Left + Best Fit + Alignment
                    const wasteX = rect.width - orient.w;
                    const wasteY = rect.height - orient.h;
                    const waste = wasteX + wasteY;
                    
                    // Lower score is better
                    const score = rect.y * 100000 + rect.x * 1000 + waste - alignBonus;
                    
                    if (bestScore === -1 || score < bestScore) {
                        bestScore = score;
                        bestRect = rect;
                        bestOrientation = orient;
                    }
                }
            }
        }

        if (!bestRect) return false;

        // Place the piece
        const placedPiece = {
            ...piece,
            x: bestRect.x,
            y: bestRect.y,
            width: bestOrientation.w,
            height: bestOrientation.h,
            rotated: bestOrientation.rotated
        };
        sheet.pieces.push(placedPiece);

        // Update free rectangles using Maximal Rectangles
        this.updateFreeRects(sheet, bestRect, bestOrientation.w, bestOrientation.h);

        return true;
    }

    updateFreeRects(sheet, usedRect, pieceW, pieceH) {
        const placedPiece = {
            x: usedRect.x,
            y: usedRect.y,
            width: pieceW,
            height: pieceH
        };

        // Remove or split all rectangles that intersect with the placed piece
        const newRects = [];
        
        for (const rect of sheet.freeRects) {
            if (this.intersects(rect, placedPiece, this.sawKerf)) {
                // Split this rectangle
                const splits = this.splitRect(rect, placedPiece, this.sawKerf);
                newRects.push(...splits);
            } else {
                // Keep this rectangle
                newRects.push(rect);
            }
        }

        sheet.freeRects = newRects.filter(r => r.width > 0 && r.height > 0);
        
        // Remove rectangles that are contained in others
        this.pruneRects(sheet);
    }

    intersects(rect, piece, kerf) {
        return !(rect.x >= piece.x + piece.width + kerf ||
                 rect.x + rect.width <= piece.x ||
                 rect.y >= piece.y + piece.height + kerf ||
                 rect.y + rect.height <= piece.y);
    }

    splitRect(rect, piece, kerf) {
        const splits = [];
        const pieceRight = piece.x + piece.width + kerf;
        const pieceBottom = piece.y + piece.height + kerf;

        // Left split
        if (rect.x < piece.x) {
            splits.push({
                x: rect.x,
                y: rect.y,
                width: piece.x - rect.x,
                height: rect.height
            });
        }

        // Right split
        if (rect.x + rect.width > pieceRight) {
            splits.push({
                x: pieceRight,
                y: rect.y,
                width: rect.x + rect.width - pieceRight,
                height: rect.height
            });
        }

        // Top split
        if (rect.y < piece.y) {
            splits.push({
                x: rect.x,
                y: rect.y,
                width: rect.width,
                height: piece.y - rect.y
            });
        }

        // Bottom split
        if (rect.y + rect.height > pieceBottom) {
            splits.push({
                x: rect.x,
                y: pieceBottom,
                width: rect.width,
                height: rect.y + rect.height - pieceBottom
            });
        }

        return splits;
    }

    pruneRects(sheet) {
        for (let i = sheet.freeRects.length - 1; i >= 0; i--) {
            for (let j = sheet.freeRects.length - 1; j >= 0; j--) {
                if (i !== j && this.isContained(sheet.freeRects[i], sheet.freeRects[j])) {
                    sheet.freeRects.splice(i, 1);
                    break;
                }
            }
        }
    }

    isContained(rectA, rectB) {
        return rectA.x >= rectB.x &&
               rectA.y >= rectB.y &&
               rectA.x + rectA.width <= rectB.x + rectB.width &&
               rectA.y + rectA.height <= rectB.y + rectB.height;
    }

    generateReport() {
        const totalSheets = this.sheets.length;
        let totalPieces = 0;
        let usedArea = 0;
        const sheetArea = this.stockWidth * this.stockHeight;

        for (const sheet of this.sheets) {
            totalPieces += sheet.pieces.length;
            for (const piece of sheet.pieces) {
                usedArea += piece.width * piece.height;
            }
        }

        const totalArea = totalSheets * sheetArea;
        const wasteArea = totalArea - usedArea;
        const efficiency = totalArea > 0 ? ((usedArea / totalArea) * 100).toFixed(2) : '0.00';

        return {
            sheets: this.sheets.map(sheet => ({
                ...sheet,
                freeRectangles: sheet.freeRects
            })),
            summary: {
                totalSheets,
                totalPieces,
                efficiency,
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
    const inputFile = process.argv[2];

    if (!inputFile) {
        console.log('Usage: node optimizer-v2.js <input.json>');
        process.exit(1);
    }

    const input = JSON.parse(fs.readFileSync(inputFile, 'utf8'));
    const optimizer = new CutlistOptimizerV2(
        input.stockWidth,
        input.stockHeight,
        input.sawKerf
    );

    const result = optimizer.optimize(input.pieces);
    console.log(JSON.stringify(result, null, 2));
}

module.exports = CutlistOptimizerV2;
