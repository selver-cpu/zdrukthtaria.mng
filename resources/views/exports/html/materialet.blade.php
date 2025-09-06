<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporti i Materialeve</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .summary h2 {
            margin-top: 0;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <h1>Raporti i Materialeve</h1>
    
    <div class="summary">
        <h2>Përmbledhje</h2>
        <p>Numri total i materialeve: <strong>{{ $materialet->count() }}</strong></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Emri i Materialit</th>
                <th>Njësia Matëse</th>
                <th>Përshkrimi</th>
                <th>Numri i Projekteve</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($materialet as $material)
                <tr>
                    <td>{{ $material->emri_materialit }}</td>
                    <td>{{ $material->njesia_matese }}</td>
                    <td>{{ $material->pershkrimi ?? 'N/A' }}</td>
                    <td>{{ $material->projektet_count ?? $material->projektet()->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Nuk ka materiale për të shfaqur</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #6c757d;">
        <p>Raporti u gjenerua më {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
