<table>
    <thead>
        <tr>
            <th><strong>Projekti</strong></th>
            <th><strong>Klienti</strong></th>
            <th><strong>Statusi</strong></th>
            <th><strong>Data Krijimit</strong></th>
            <th><strong>Të Ardhurat (€)</strong></th>
            <th><strong>Shpenzimet (€)</strong></th>
            <th><strong>Fitimi (€)</strong></th>
            <th><strong>Përqindja e Fitimit (%)</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($projektet as $projekt)
        @php
            $shpenzimetProjekti = $projekt->projektMateriale->sum(function($pm) {
                return $pm->sasia * ($pm->material->cmimi_per_njesi ?? 0);
            });
            $fitimiProjekti = ($projekt->cmimi_total ?? 0) - $shpenzimetProjekti;
            $perqindjaFitimit = ($projekt->cmimi_total ?? 0) > 0 ? ($fitimiProjekti / ($projekt->cmimi_total ?? 1)) * 100 : 0;
        @endphp
        <tr>
            <td>{{ $projekt->emri_projektit }}</td>
            <td>{{ $projekt->klient->emri_klientit ?? 'N/A' }}</td>
            <td>{{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}</td>
            <td>{{ $projekt->data_krijimit ? \Carbon\Carbon::parse($projekt->data_krijimit)->format('d/m/Y') : 'N/A' }}</td>
            <td>{{ $projekt->cmimi_total ?? 0 }}</td>
            <td>{{ $shpenzimetProjekti }}</td>
            <td>{{ $fitimiProjekti }}</td>
            <td>{{ number_format($perqindjaFitimit, 1) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
