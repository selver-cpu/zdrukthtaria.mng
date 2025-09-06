<table>
    <thead>
        <tr>
            <th><strong>Emri Materialit</strong></th>
            <th><strong>Njësia Matëse</strong></th>
            <th><strong>Sasia e Përdorur</strong></th>
            <th><strong>Cmimi për Njësi (€)</strong></th>
            <th><strong>Vlera Totale (€)</strong></th>
            <th><strong>Projektet që e Përdorin</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($materialetPerdorur as $material)
        <tr>
            <td>{{ $material->material->emri_materialit ?? 'N/A' }}</td>
            <td>{{ $material->material->njesia_matese ?? 'N/A' }}</td>
            <td>{{ $material->total_perdorur }}</td>
            <td>{{ $material->material->cmimi_per_njesi ?? 0 }}</td>
            <td>{{ $material->total_perdorur * ($material->material->cmimi_per_njesi ?? 0) }}</td>
            <td>{{ $material->projektet_count ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
