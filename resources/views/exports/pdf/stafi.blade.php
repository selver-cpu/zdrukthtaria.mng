<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport Stafi - {{ date('d/m/Y') }}</title>
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
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #6f42c1;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            color: #6f42c1;
            border-bottom: 1px solid #6f42c1;
            padding-bottom: 5px;
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
            background-color: #6f42c1;
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
        <h1>Raport Performancë Stafi</h1>
        <p>Gjeneruar më: {{ date('d/m/Y H:i') }}</p>
        <p>Total Stafi: {{ $mjeshtrat->count() + $montuesit->count() }}</p>
    </div>

    <div class="section">
        <h3>Mjeshtrat ({{ $mjeshtrat->count() }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Emri</th>
                    <th>Email</th>
                    <th>Projektet e Caktuara</th>
                    <th>Projektet e Përfunduara</th>
                    <th>Përqindja e Suksesit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mjeshtrat as $mjesher)
                <tr>
                    <td>{{ $mjesher->name }}</td>
                    <td>{{ $mjesher->email }}</td>
                    <td>{{ $mjesher->projekte_si_mjesher_count ?? 0 }}</td>
                    <td>{{ $mjesher->projekte_perfunduar_count ?? 0 }}</td>
                    <td>
                        @if(($mjesher->projekte_si_mjesher_count ?? 0) > 0)
                            {{ number_format((($mjesher->projekte_perfunduar_count ?? 0) / ($mjesher->projekte_si_mjesher_count ?? 1)) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Montuesit ({{ $montuesit->count() }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Emri</th>
                    <th>Email</th>
                    <th>Projektet e Caktuara</th>
                    <th>Projektet e Përfunduara</th>
                    <th>Përqindja e Suksesit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($montuesit as $montues)
                <tr>
                    <td>{{ $montues->name }}</td>
                    <td>{{ $montues->email }}</td>
                    <td>{{ $montues->projekte_si_montues_count ?? 0 }}</td>
                    <td>{{ $montues->projekte_perfunduar_count ?? 0 }}</td>
                    <td>
                        @if(($montues->projekte_si_montues_count ?? 0) > 0)
                            {{ number_format((($montues->projekte_perfunduar_count ?? 0) / ($montues->projekte_si_montues_count ?? 1)) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Aplikacioni i Menaxhimit të Punës - Raport i gjeneruar automatikisht</p>
    </div>
</body>
</html>
