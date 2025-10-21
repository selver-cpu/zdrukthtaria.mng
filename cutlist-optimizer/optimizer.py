#!/usr/bin/env python3
"""
Industrial-Grade Cutlist Optimizer
Uses MaxRects algorithm (industry standard)
"""

import sys
import json
from typing import List, Dict, Tuple

class Rectangle:
    """Represents a piece to be cut"""
    def __init__(self, width: int, height: int, rid: str, data: dict):
        self.width = width
        self.height = height
        self.rid = rid
        self.data = data
        self.x = 0
        self.y = 0
        self.rotated = False

class MaxRectsBinPack:
    """
    MaxRects Bin Packing Algorithm
    Industry standard for 2D cutting optimization
    """
    
    def __init__(self, width: int, height: int, saw_kerf: int = 4):
        self.bin_width = width
        self.bin_height = height
        self.saw_kerf = saw_kerf
        self.used_rectangles = []
        self.free_rectangles = [{'x': 0, 'y': 0, 'width': width, 'height': height}]
    
    def insert(self, width: int, height: int, rid: str, data: dict) -> Rectangle:
        """Insert a rectangle using Guillotine-compatible strategy"""
        rect = Rectangle(width, height, rid, data)
        
        # Try both orientations
        best_score = float('inf')
        best_rect = None
        best_rotated = False
        
        for rotated in [False, True]:
            w = height if rotated else width
            h = width if rotated else height
            
            for free_rect in self.free_rectangles:
                if free_rect['width'] >= w and free_rect['height'] >= h:
                    # Guillotine preference: prefer edges (x=0 or y=0)
                    is_at_edge = (free_rect['x'] == 0 or free_rect['y'] == 0)
                    edge_bonus = 0 if is_at_edge else 100000
                    
                    # Bottom-Left strategy for Guillotine
                    position_score = free_rect['y'] * 10000 + free_rect['x'] * 100
                    
                    # Minimize waste
                    leftoverX = free_rect['width'] - w
                    leftoverY = free_rect['height'] - h
                    waste = leftoverX + leftoverY
                    
                    score = edge_bonus + position_score + waste
                    
                    if score < best_score:
                        best_score = score
                        best_rect = free_rect
                        rect.width = w
                        rect.height = h
                        rect.x = free_rect['x']
                        rect.y = free_rect['y']
                        rect.rotated = rotated
        
        if best_rect:
            self.place_rectangle(rect)
            return rect
        
        return None
    
    def place_rectangle(self, rect: Rectangle):
        """Place a rectangle and update free rectangles"""
        num_rects_to_process = len(self.free_rectangles)
        i = 0
        
        while i < num_rects_to_process:
            if self.split_free_node(self.free_rectangles[i], rect):
                self.free_rectangles.pop(i)
                num_rects_to_process -= 1
            else:
                i += 1
        
        self.prune_free_list()
        self.used_rectangles.append(rect)
    
    def split_free_node(self, free_node: dict, used_node: Rectangle) -> bool:
        """Split a free rectangle if it intersects with used rectangle"""
        # Add saw kerf to used rectangle
        used_x = used_node.x
        used_y = used_node.y
        used_width = used_node.width + self.saw_kerf
        used_height = used_node.height + self.saw_kerf
        
        # Check if rectangles intersect
        if (used_x >= free_node['x'] + free_node['width'] or
            used_x + used_width <= free_node['x'] or
            used_y >= free_node['y'] + free_node['height'] or
            used_y + used_height <= free_node['y']):
            return False
        
        # Split the free node
        if used_x < free_node['x'] + free_node['width'] and used_x + used_width > free_node['x']:
            # New node at the top
            if used_y > free_node['y'] and used_y < free_node['y'] + free_node['height']:
                new_node = {
                    'x': free_node['x'],
                    'y': free_node['y'],
                    'width': free_node['width'],
                    'height': used_y - free_node['y']
                }
                self.free_rectangles.append(new_node)
            
            # New node at the bottom
            if used_y + used_height < free_node['y'] + free_node['height']:
                new_node = {
                    'x': free_node['x'],
                    'y': used_y + used_height,
                    'width': free_node['width'],
                    'height': free_node['y'] + free_node['height'] - (used_y + used_height)
                }
                self.free_rectangles.append(new_node)
        
        if used_y < free_node['y'] + free_node['height'] and used_y + used_height > free_node['y']:
            # New node to the left
            if used_x > free_node['x'] and used_x < free_node['x'] + free_node['width']:
                new_node = {
                    'x': free_node['x'],
                    'y': free_node['y'],
                    'width': used_x - free_node['x'],
                    'height': free_node['height']
                }
                self.free_rectangles.append(new_node)
            
            # New node to the right
            if used_x + used_width < free_node['x'] + free_node['width']:
                new_node = {
                    'x': used_x + used_width,
                    'y': free_node['y'],
                    'width': free_node['x'] + free_node['width'] - (used_x + used_width),
                    'height': free_node['height']
                }
                self.free_rectangles.append(new_node)
        
        return True
    
    def prune_free_list(self):
        """Remove free rectangles that are contained in others"""
        i = 0
        while i < len(self.free_rectangles):
            j = i + 1
            while j < len(self.free_rectangles):
                if self.is_contained_in(self.free_rectangles[i], self.free_rectangles[j]):
                    self.free_rectangles.pop(i)
                    i -= 1
                    break
                if self.is_contained_in(self.free_rectangles[j], self.free_rectangles[i]):
                    self.free_rectangles.pop(j)
                    j -= 1
                j += 1
            i += 1
    
    def is_contained_in(self, a: dict, b: dict) -> bool:
        """Check if rectangle a is contained in rectangle b"""
        return (a['x'] >= b['x'] and a['y'] >= b['y'] and
                a['x'] + a['width'] <= b['x'] + b['width'] and
                a['y'] + a['height'] <= b['y'] + b['height'])

class CutlistOptimizer:
    """Main optimizer class"""
    
    def __init__(self, stock_width: int, stock_height: int, saw_kerf: int = 4):
        self.stock_width = stock_width
        self.stock_height = stock_height
        self.saw_kerf = saw_kerf
        self.bins = []
    
    def optimize(self, pieces: List[dict]) -> dict:
        """Optimize piece placement"""
        # Sort pieces by area (largest first)
        sorted_pieces = sorted(pieces, key=lambda p: p['width'] * p['height'], reverse=True)
        
        for piece in sorted_pieces:
            placed = False
            
            # Try to place in existing bins
            for bin_pack in self.bins:
                rect = bin_pack.insert(
                    piece['width'],
                    piece['height'],
                    piece['id'],
                    piece
                )
                if rect:
                    placed = True
                    break
            
            # Create new bin if needed
            if not placed:
                new_bin = MaxRectsBinPack(self.stock_width, self.stock_height, self.saw_kerf)
                rect = new_bin.insert(
                    piece['width'],
                    piece['height'],
                    piece['id'],
                    piece
                )
                if rect:
                    self.bins.append(new_bin)
        
        return self.generate_report()
    
    def generate_report(self) -> dict:
        """Generate optimization report"""
        sheets = []
        total_pieces = 0
        used_area = 0
        
        for idx, bin_pack in enumerate(self.bins):
            pieces = []
            for rect in bin_pack.used_rectangles:
                pieces.append({
                    'id': rect.rid,
                    'name': rect.data.get('name', ''),
                    'width': rect.width,
                    'height': rect.height,
                    'thickness': rect.data.get('thickness', 0),
                    'quantity': rect.data.get('quantity', 1),
                    'material': rect.data.get('material', ''),
                    'edge_banding': rect.data.get('edge_banding', {}),
                    'x': rect.x,
                    'y': rect.y,
                    'rotated': rect.rotated
                })
                used_area += rect.width * rect.height
                total_pieces += 1
            
            sheets.append({
                'id': idx + 1,
                'width': self.stock_width,
                'height': self.stock_height,
                'pieces': pieces,
                'freeRectangles': [
                    {'x': r['x'], 'y': r['y'], 'width': r['width'], 'height': r['height']}
                    for r in bin_pack.free_rectangles
                ]
            })
        
        total_area = len(self.bins) * self.stock_width * self.stock_height
        efficiency = (used_area / total_area * 100) if total_area > 0 else 0
        waste_area = total_area - used_area
        
        return {
            'sheets': sheets,
            'summary': {
                'totalSheets': len(self.bins),
                'totalPieces': total_pieces,
                'efficiency': f'{efficiency:.2f}',
                'usedArea': f'{used_area:.2f}',
                'wasteArea': f'{waste_area:.2f}',
                'stockSize': f'{self.stock_width}×{self.stock_height}mm'
            }
        }

def main():
    """Main entry point"""
    if len(sys.argv) < 2:
        print('Usage: python3 optimizer.py <input.json>')
        sys.exit(1)
    
    # Read input
    with open(sys.argv[1], 'r') as f:
        data = json.load(f)
    
    # Run optimizer
    optimizer = CutlistOptimizer(
        data['stockWidth'],
        data['stockHeight'],
        data.get('sawKerf', 4)
    )
    
    result = optimizer.optimize(data['pieces'])
    
    # Output result
    print(json.dumps(result, indent=2))

if __name__ == '__main__':
    main()
