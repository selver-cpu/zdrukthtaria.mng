<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporti i Projekteve</title>
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
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pritje { background-color: #ffeeba; color: #856404; }
        .status-progres { background-color: #b8daff; color: #004085; }
        .status-pauze { background-color: #d6d8db; color: #383d41; }
        .status-perfunduar { background-color: #c3e6cb; color: #155724; }
        .status-anuluar { background-color: #f5c6cb; color: #721c24; }
    </style>
</head>
<body>
    <h1>Raporti i Projekteve</h1>
    
    <div class="summary">
        <h2>Përmbledhje</h2>
        <p>Numri total i projekteve: <strong>{{ $projektet->count() }}</strong></p>
        <p>Vlera totale e projekteve: <strong>{{ number_format($projektet->sum('buxheti'), 2) }} €</strong></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Emri i Projektit</th>
                <th>Klienti</th>
                <th>Buxheti</th>
                <th>Data e fillimit</th>
                <th>Data e përfundimit</th>
                <th>Statusi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projektet as $projekt)
                <tr>
                    <td>{{ $projekt->emri_projektit }}</td>
                    <td>{{ $projekt->klient->emri ?? 'N/A' }} {{ $projekt->klient->mbiemri ?? '' }}</td>
                    <td>{{ number_format($projekt->buxheti, 2) }} €</td>
                    <td>{{ $projekt->data_fillimit_parashikuar ? $projekt->data_fillimit_parashikuar->format('d.m.Y') : 'N/A' }}</td>
                    <td>{{ $projekt->data_perfundimit_parashikuar ? $projekt->data_perfundimit_parashikuar->format('d.m.Y') : 'N/A' }}</td>
                    <td>
                        @if ($projekt->statusi_projektit)
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $projekt->statusi_projektit->emri_statusit)) }}">
                                {{ $projekt->statusi_projektit->emri_statusit }}
                            </span>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Nuk ka projekte për të shfaqur</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #6c757d;">
        <p>Raporti u gjenerua më {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
