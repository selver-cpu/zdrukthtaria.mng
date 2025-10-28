<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporti Financiar</title>
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
        .summary {
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e9ecef;
            max-width: 800px;
        }
        .summary h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .financial-card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .financial-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .negative {
            color: #dc3545;
        }
        .chart {
            width: 100%;
            height: 300px;
            margin: 20px 0;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            display: flex;
            align-items: flex-end;
            padding: 10px;
        }
        .bar {
            flex: 1;
            margin: 0 5px;
            background-color: #007bff;
            min-height: 20px;
            position: relative;
        }
        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
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
    </style>
</head>
<body>
    <h1>Raporti Financiar</h1>
    
    <div class="summary">
        <h2>Përmbledhje Financiare</h2>
        
        <div class="financial-card">
            <h3>Të ardhurat totale nga projektet</h3>
            <p class="amount">{{ number_format($te_ardhurat_totale, 2) }} €</p>
        </div>
        
        <div class="financial-card">
            <h3>Shpenzimet totale për materiale</h3>
            <p class="amount negative">{{ number_format($shpenzimet_materiale, 2) }} €</p>
        </div>
        
        <div class="financial-card">
            <h3>Fitimi neto</h3>
            <p class="amount {{ $fitimi_neto < 0 ? 'negative' : '' }}">{{ number_format($fitimi_neto, 2) }} €</p>
        </div>
        
        <h3>Projektet me vlerën më të lartë</h3>
        <table>
            <thead>
                <tr>
                    <th>Emri i Projektit</th>
                    <th>Klienti</th>
                    <th>Buxheti</th>
                    <th>Statusi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projektet_top as $projekt)
                    <tr>
                        <td>{{ $projekt->emri_projektit }}</td>
                        <td>{{ $projekt->klient->emri ?? 'N/A' }} {{ $projekt->klient->mbiemri ?? '' }}</td>
                        <td>{{ number_format($projekt->buxheti, 2) }} €</td>
                        <td>{{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Nuk ka projekte për të shfaqur</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #6c757d;">
        <p>Raporti u gjenerua më {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
