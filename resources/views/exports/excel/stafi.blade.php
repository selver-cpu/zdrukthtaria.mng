<table>
    <thead>
        <tr>
            <th><strong>Emri</strong></th>
            <th><strong>Email</strong></th>
            <th><strong>Roli</strong></th>
            <th><strong>Projektet e Caktuara</strong></th>
            <th><strong>Projektet e Përfunduara</strong></th>
            <th><strong>Përqindja e Suksesit (%)</strong></th>
            <th><strong>Data e Krijimit</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($mjeshtrat as $mjesher)
        <tr>
            <td>{{ $mjesher->name }}</td>
            <td>{{ $mjesher->email }}</td>
            <td>Mjeshtër</td>
            <td>{{ $mjesher->projekte_si_mjesher_count ?? 0 }}</td>
            <td>{{ $mjesher->projekte_perfunduar_count ?? 0 }}</td>
            <td>
                @if(($mjesher->projekte_si_mjesher_count ?? 0) > 0)
                    {{ number_format((($mjesher->projekte_perfunduar_count ?? 0) / ($mjesher->projekte_si_mjesher_count ?? 1)) * 100, 1) }}
                @else
                    0
                @endif
            </td>
            <td>{{ $mjesher->created_at ? $mjesher->created_at->format('d/m/Y') : 'N/A' }}</td>
        </tr>
        @endforeach
        @foreach($montuesit as $montues)
        <tr>
            <td>{{ $montues->name }}</td>
            <td>{{ $montues->email }}</td>
            <td>Montues</td>
            <td>{{ $montues->projekte_si_montues_count ?? 0 }}</td>
            <td>{{ $montues->projekte_perfunduar_count ?? 0 }}</td>
            <td>
                @if(($montues->projekte_si_montues_count ?? 0) > 0)
                    {{ number_format((($montues->projekte_perfunduar_count ?? 0) / ($montues->projekte_si_montues_count ?? 1)) * 100, 1) }}
                @else
                    0
                @endif
            </td>
            <td>{{ $montues->created_at ? $montues->created_at->format('d/m/Y') : 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
