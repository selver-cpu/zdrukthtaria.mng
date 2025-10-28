<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport Financiar - {{ date('d/m/Y') }}</title>
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
            border-bottom: 2px solid #dc3545;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #dc3545;
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
            color: #dc3545;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .summary-item h4 {
            margin: 0 0 5px 0;
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
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
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
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
        <h1>Raport Financiar</h1>
        <p>Gjeneruar më: {{ date('d/m/Y H:i') }}</p>
        <p>Periudha: {{ request('data_nga') ? \Carbon\Carbon::parse(request('data_nga'))->format('d/m/Y') : 'Fillimi' }} - {{ request('data_deri') ? \Carbon\Carbon::parse(request('data_deri'))->format('d/m/Y') : 'Sot' }}</p>
    </div>

    <div class="summary">
        <h3>Përmbledhje Financiare</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Të Ardhurat Totale</h4>
                <div class="value">{{ number_format($teArdhuratTotale, 2) }} €</div>
            </div>
            <div class="summary-item">
                <h4>Shpenzimet Totale</h4>
                <div class="value">{{ number_format($shpenzimet, 2) }} €</div>
            </div>
            <div class="summary-item">
                <h4>Fitimi Neto</h4>
                <div class="value">{{ number_format($teArdhuratTotale - $shpenzimet, 2) }} €</div>
            </div>
        </div>
    </div>

    <h3 style="color: #dc3545; border-bottom: 1px solid #dc3545; padding-bottom: 5px;">Detajet e Projekteve</h3>
    <table>
        <thead>
            <tr>
                <th>Projekti</th>
                <th>Klienti</th>
                <th>Statusi</th>
                <th>Data Krijimit</th>
                <th class="text-right">Të Ardhurat (€)</th>
                <th class="text-right">Shpenzimet (€)</th>
                <th class="text-right">Fitimi (€)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projektet as $projekt)
            @php
                $shpenzimetProjekti = $projekt->projektMateriale->sum(function($pm) {
                    return $pm->sasia * ($pm->material->cmimi_per_njesi ?? 0);
                });
                $fitimiProjekti = ($projekt->cmimi_total ?? 0) - $shpenzimetProjekti;
            @endphp
            <tr>
                <td>{{ $projekt->emri_projektit }}</td>
                <td>{{ $projekt->klient->emri_klientit ?? 'N/A' }}</td>
                <td>{{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}</td>
                <td>{{ $projekt->data_krijimit ? \Carbon\Carbon::parse($projekt->data_krijimit)->format('d/m/Y') : 'N/A' }}</td>
                <td class="text-right">{{ number_format($projekt->cmimi_total ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($shpenzimetProjekti, 2) }}</td>
                <td class="text-right">{{ number_format($fitimiProjekti, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #dc3545; color: white; font-weight: bold;">
                <td colspan="4">TOTALI</td>
                <td class="text-right">{{ number_format($teArdhuratTotale, 2) }} €</td>
                <td class="text-right">{{ number_format($shpenzimet, 2) }} €</td>
                <td class="text-right">{{ number_format($teArdhuratTotale - $shpenzimet, 2) }} €</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Aplikacioni i Menaxhimit të Punës - Raport i gjeneruar automatikisht</p>
    </div>
</body>
</html>
