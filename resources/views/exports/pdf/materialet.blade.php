<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport Materialet - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #28a745;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Raport Materialet</h1>
        <p>Gjeneruar më: {{ date('d/m/Y H:i') }}</p>
        <p>Total Materialet e Përdorura: {{ $materialetPerdorur->count() }}</p>
    </div>

    <div class="summary">
        <h3>Përmbledhje</h3>
        <p><strong>Lloje Materialesh:</strong> {{ $materialetPerdorur->count() }}</p>
        <p><strong>Sasia Totale e Përdorur:</strong> {{ $materialetPerdorur->sum('total_perdorur') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Emri Materialit</th>
                <th>Njësia Matëse</th>
                <th>Sasia e Përdorur</th>
                <th>Cmimi për Njësi (€)</th>
                <th>Vlera Totale (€)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materialetPerdorur as $material)
            <tr>
                <td>{{ $material->material->emri_materialit ?? 'N/A' }}</td>
                <td>{{ $material->material->njesia_matese ?? 'N/A' }}</td>
                <td>{{ number_format($material->total_perdorur, 2) }}</td>
                <td>{{ number_format($material->material->cmimi_per_njesi ?? 0, 2) }}</td>
                <td>{{ number_format(($material->total_perdorur * ($material->material->cmimi_per_njesi ?? 0)), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Aplikacioni i Menaxhimit të Punës - Raport i gjeneruar automatikisht</p>
    </div>
</body>
</html>
