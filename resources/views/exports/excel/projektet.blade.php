<table>
    <thead>
        <tr>
            <th><strong>ID</strong></th>
            <th><strong>Emri Projektit</strong></th>
            <th><strong>Klienti</strong></th>
            <th><strong>Statusi</strong></th>
            <th><strong>Data Krijimit</strong></th>
            <th><strong>Cmimi (€)</strong></th>
            <th><strong>Mjeshtri</strong></th>
            <th><strong>Montuesi</strong></th>
            <th><strong>Pershkrimi</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($projektet as $projekt)
        <tr>
            <td>{{ $projekt->projekt_id }}</td>
            <td>{{ $projekt->emri_projektit }}</td>
            <td>{{ $projekt->klient->emri_klientit ?? 'N/A' }}</td>
            <td>{{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}</td>
            <td>{{ $projekt->data_krijimit ? \Carbon\Carbon::parse($projekt->data_krijimit)->format('d/m/Y') : 'N/A' }}</td>
            <td>{{ $projekt->cmimi_total ?? 0 }}</td>
            <td>{{ $projekt->mjeshtriCaktuar->name ?? 'N/A' }}</td>
            <td>{{ $projekt->montuesicaktuar->name ?? 'N/A' }}</td>
            <td>{{ $projekt->pershkrimi ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
