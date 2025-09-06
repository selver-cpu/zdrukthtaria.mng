<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporti i Stafit</title>
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
        .role-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            background-color: #e2e3e5;
            color: #383d41;
        }
        .role-admin { background-color: #cce5ff; color: #004085; }
        .role-mjeshtri { background-color: #d4edda; color: #155724; }
        .role-montuesi { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <h1>Raporti i Stafit</h1>
    
    <div class="summary">
        <h2>Përmbledhje</h2>
        <p>Numri total i stafit: <strong>{{ $stafi->count() }}</strong></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Emri</th>
                <th>Email</th>
                <th>Roli</th>
                <th>Projektet e caktuara</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stafi as $staf)
                <tr>
                    <td>{{ $staf->emri }} {{ $staf->mbiemri }}</td>
                    <td>{{ $staf->email }}</td>
                    <td>
                        @if($staf->roli == 'admin')
                            <span class="role-badge role-admin">Administrator</span>
                        @elseif($staf->roli == 'mjeshtri')
                            <span class="role-badge role-mjeshtri">Mjeshtër</span>
                        @elseif($staf->roli == 'montuesi')
                            <span class="role-badge role-montuesi">Montues</span>
                        @else
                            <span class="role-badge">{{ $staf->roli }}</span>
                        @endif
                    </td>
                    <td>
                        @if($staf->roli == 'mjeshtri')
                            {{ $staf->projektet_mjeshtri_count ?? $staf->projektetMjeshtri()->count() }}
                        @elseif($staf->roli == 'montuesi')
                            {{ $staf->projektet_montuesi_count ?? $staf->projektetMontuesi()->count() }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Nuk ka staf për të shfaqur</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #6c757d;">
        <p>Raporti u gjenerua më {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
