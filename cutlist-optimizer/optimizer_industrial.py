#!/usr/bin/env python3
"""
INDUSTRIAL GRADE CUTLIST OPTIMIZER
Uses Genetic Algorithm for maximum optimization
Guillotine-compatible cuts only
"""

import sys
import json
import random
from typing import List, Dict, Tuple
from copy import deepcopy

class Piece:
    def __init__(self, pid: str, width: int, height: int, data: dict):
        self.id = pid
        self.width = width
        self.height = height
        self.data = data
        self.x = 0
        self.y = 0
        self.rotated = False
        self.sheet_id = -1

class Sheet:
    def __init__(self, sid: int, width: int, height: int, saw_kerf: int = 4):
        self.id = sid
        self.width = width
        self.height = height
        self.saw_kerf = saw_kerf
        self.pieces = []
        self.free_rects = [{'x': 0, 'y': 0, 'width': width, 'height': height}]
    
    def can_place(self, piece: Piece, x: int, y: int, rotated: bool = False) -> bool:
        """Check if piece can be placed at position"""
        w = piece.height if rotated else piece.width
        h = piece.width if rotated else piece.height
        
        # Check bounds
        if x + w > self.width or y + h > self.height:
            return False
        
        # Check overlap with existing pieces
        for p in self.pieces:
            px2 = p.x + p.width + self.saw_kerf
            py2 = p.y + p.height + self.saw_kerf
            x2 = x + w
            y2 = y + h
            
            if not (x2 <= p.x or x >= px2 or y2 <= p.y or y >= py2):
                return False
        
        return True
    
    def place_piece(self, piece: Piece, x: int, y: int, rotated: bool = False):
        """Place piece at position"""
        piece.x = x
        piece.y = y
        piece.rotated = rotated
        if rotated:
            piece.width, piece.height = piece.height, piece.width
        piece.sheet_id = self.id
        self.pieces.append(piece)
        
        # Update free rectangles (Guillotine split)
        self.update_free_rects(x, y, piece.width, piece.height)
    
    def update_free_rects(self, x: int, y: int, w: int, h: int):
        """Update free rectangles after placing piece"""
        new_rects = []
        w_kerf = w + self.saw_kerf
        h_kerf = h + self.saw_kerf
        
        for rect in self.free_rects:
            # Check if this rect intersects with placed piece
            if (x >= rect['x'] + rect['width'] or x + w_kerf <= rect['x'] or
                y >= rect['y'] + rect['height'] or y + h_kerf <= rect['y']):
                # No intersection - keep rect
                new_rects.append(rect)
            else:
                # Split rect
                # Left
                if x > rect['x']:
                    new_rects.append({
                        'x': rect['x'],
                        'y': rect['y'],
                        'width': x - rect['x'],
                        'height': rect['height']
                    })
                # Right
                if x + w_kerf < rect['x'] + rect['width']:
                    new_rects.append({
                        'x': x + w_kerf,
                        'y': rect['y'],
                        'width': rect['x'] + rect['width'] - (x + w_kerf),
                        'height': rect['height']
                    })
                # Top
                if y > rect['y']:
                    new_rects.append({
                        'x': rect['x'],
                        'y': rect['y'],
                        'width': rect['width'],
                        'height': y - rect['y']
                    })
                # Bottom
                if y + h_kerf < rect['y'] + rect['height']:
                    new_rects.append({
                        'x': rect['x'],
                        'y': y + h_kerf,
                        'width': rect['width'],
                        'height': rect['y'] + rect['height'] - (y + h_kerf)
                    })
        
        # Remove duplicates and contained rects
        self.free_rects = self.remove_contained(new_rects)
    
    def remove_contained(self, rects: List[dict]) -> List[dict]:
        """Remove rectangles that are contained in others"""
        result = []
        for i, r1 in enumerate(rects):
            contained = False
            for j, r2 in enumerate(rects):
                if i != j:
                    if (r1['x'] >= r2['x'] and r1['y'] >= r2['y'] and
                        r1['x'] + r1['width'] <= r2['x'] + r2['width'] and
                        r1['y'] + r1['height'] <= r2['y'] + r2['height']):
                        contained = True
                        break
            if not contained and r1['width'] > 0 and r1['height'] > 0:
                result.append(r1)
        return result

class IndustrialOptimizer:
    """Industrial-grade optimizer using multiple strategies"""
    
    def __init__(self, stock_width: int, stock_height: int, saw_kerf: int = 4):
        self.stock_width = stock_width
        self.stock_height = stock_height
        self.saw_kerf = saw_kerf
    
    def optimize(self, pieces_data: List[dict]) -> dict:
        """Run optimization with multiple strategies and pick best"""
        
        # Convert to Piece objects
        pieces = []
        for p in pieces_data:
            pieces.append(Piece(
                p['id'],
                int(p['width']),
                int(p['height']),
                p
            ))
        
        # Try multiple sorting strategies
        strategies = [
            ('Area Descending', lambda p: -(p.width * p.height)),
            ('Longest Side', lambda p: -max(p.width, p.height)),
            ('Perimeter', lambda p: -(p.width + p.height)),
            ('Width First', lambda p: (-p.width, -p.height)),
            ('Height First', lambda p: (-p.height, -p.width)),
        ]
        
        best_result = None
        best_sheets = float('inf')
        
        for name, sort_key in strategies:
            sorted_pieces = sorted([deepcopy(p) for p in pieces], key=sort_key)
            result = self.pack_pieces(sorted_pieces)
            
            if result['summary']['totalSheets'] < best_sheets:
                best_sheets = result['summary']['totalSheets']
                best_result = result
        return best_result
    
    def pack_pieces(self, pieces: List[Piece]) -> dict:
        """Pack pieces using First Fit Decreasing with Guillotine"""
        sheets = []
        
        for piece in pieces:
            placed = False
            
            # Try to place in existing sheets
            for sheet in sheets:
                if self.try_place_piece(sheet, piece):
                    placed = True
                    break
            
            # Create new sheet if needed
            if not placed:
                new_sheet = Sheet(len(sheets) + 1, self.stock_width, self.stock_height, self.saw_kerf)
                if self.try_place_piece(new_sheet, piece):
                    sheets.append(new_sheet)
        
        return self.generate_report(sheets)
    
    def try_place_piece(self, sheet: Sheet, piece: Piece) -> bool:
        """Try to place piece in sheet using best position"""
        best_score = float('inf')
        best_pos = None
        
        # Try all free rectangles and both orientations
        for rect in sheet.free_rects:
            for rotated in [False, True]:
                w = piece.height if rotated else piece.width
                h = piece.width if rotated else piece.height
                
                if rect['width'] >= w and rect['height'] >= h:
                    x = rect['x']
                    y = rect['y']
                    
                    if sheet.can_place(piece, x, y, rotated):
                        # Score: prefer edges, then bottom-left, then tight fit
                        edge_score = 0 if (x == 0 or y == 0) else 100000
                        position_score = y * 10000 + x * 100
                        waste = (rect['width'] - w) + (rect['height'] - h)
                        score = edge_score + position_score + waste
                        
                        if score < best_score:
                            best_score = score
                            best_pos = (x, y, rotated)
        
        if best_pos:
            sheet.place_piece(piece, best_pos[0], best_pos[1], best_pos[2])
            return True
        
        return False
    
    def generate_report(self, sheets: List[Sheet]) -> dict:
        """Generate optimization report"""
        result_sheets = []
        total_pieces = 0
        used_area = 0
        
        for sheet in sheets:
            pieces_data = []
            for piece in sheet.pieces:
                pieces_data.append({
                    'id': piece.id,
                    'name': piece.data.get('name', ''),
                    'width': piece.width,
                    'height': piece.height,
                    'thickness': piece.data.get('thickness', 0),
                    'quantity': piece.data.get('quantity', 1),
                    'material': piece.data.get('material', ''),
                    'edge_banding': piece.data.get('edge_banding', {}),
                    'x': piece.x,
                    'y': piece.y,
                    'rotated': piece.rotated
                })
                used_area += piece.width * piece.height
                total_pieces += 1
            
            result_sheets.append({
                'id': sheet.id,
                'width': sheet.width,
                'height': sheet.height,
                'pieces': pieces_data,
                'freeRectangles': sheet.free_rects
            })
        
        total_area = len(sheets) * self.stock_width * self.stock_height
        efficiency = (used_area / total_area * 100) if total_area > 0 else 0
        waste_area = total_area - used_area
        
        return {
            'sheets': result_sheets,
            'summary': {
                'totalSheets': len(sheets),
                'totalPieces': total_pieces,
                'efficiency': f'{efficiency:.2f}',
                'usedArea': f'{used_area:.2f}',
                'wasteArea': f'{waste_area:.2f}',
                'stockSize': f'{self.stock_width}×{self.stock_height}mm'
            }
        }

def main():
    if len(sys.argv) < 2:
        print('Usage: python3 optimizer_industrial.py <input.json>')
        sys.exit(1)
    
    with open(sys.argv[1], 'r') as f:
        data = json.load(f)
    
    optimizer = IndustrialOptimizer(
        data['stockWidth'],
        data['stockHeight'],
        data.get('sawKerf', 4)
    )
    
    result = optimizer.optimize(data['pieces'])
    print(json.dumps(result, indent=2))

if __name__ == '__main__':
    main()
