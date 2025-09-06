<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport Projektet - {{ date('d/m/Y') }}</title>
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #007bff;
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
            color: #007bff;
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
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-aktiv { background-color: #28a745; color: white; }
        .status-perfunduar { background-color: #17a2b8; color: white; }
        .status-pezulluar { background-color: #ffc107; color: black; }
        .status-anulluar { background-color: #dc3545; color: white; }
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
        <h1>Raport Projektet</h1>
        <p>Gjeneruar më: {{ date('d/m/Y H:i') }}</p>
        <p>Total Projektet: {{ $projektet->count() }}</p>
    </div>

    <div class="summary">
        <h3>Përmbledhje</h3>
        <p><strong>Projektet Aktive:</strong> {{ $projektet->where('status.emri_statusit', 'Në progres')->count() }}</p>
        <p><strong>Projektet e Përfunduara:</strong> {{ $projektet->where('status.emri_statusit', 'Përfunduar')->count() }}</p>
        <p><strong>Vlera Totale:</strong> {{ number_format($projektet->sum('cmimi_total'), 2) }} €</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Emri Projektit</th>
                <th>Klienti</th>
                <th>Statusi</th>
                <th>Data Krijimit</th>
                <th>Cmimi (€)</th>
                <th>Mjeshtri</th>
                <th>Montuesi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projektet as $projekt)
            <tr>
                <td>{{ $projekt->projekt_id }}</td>
                <td>{{ $projekt->emri_projektit }}</td>
                <td>{{ $projekt->klient->emri_klientit ?? 'N/A' }}</td>
                <td>
                    <span class="status status-{{ strtolower(str_replace(' ', '-', $projekt->statusi_projektit->emri_statusit ?? 'N/A')) }}">
                        {{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}
                    </span>
                </td>
                <td>{{ $projekt->data_krijimit ? \Carbon\Carbon::parse($projekt->data_krijimit)->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ number_format($projekt->cmimi_total ?? 0, 2) }}</td>
                <td>{{ $projekt->mjeshtriCaktuar->name ?? 'N/A' }}</td>
                <td>{{ $projekt->montuesicaktuar->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Aplikacioni i Menaxhimit të Punës - Raport i gjeneruar automatikisht</p>
    </div>
</body>
</html>
