<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projektet;
use App\Models\Klientet;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        // Search projects
        $projekte = Projektet::where('emri_projektit', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($projekt) {
                return [
                    'title' => $projekt->emri_projektit,
                    'description' => optional($projekt->klient)->emri ?? 'Klient i panjohur',
                    'type' => 'projekt',
                    'url' => route('projektet.show', $projekt)
                ];
            });

        // Search clients
        $kliente = Klientet::where('emri', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($klient) {
                return [
                    'title' => $klient->emri,
                    'description' => 'Klient',
                    'type' => 'klient',
                    'url' => '#' // Adjust this to the appropriate client route if available
                ];
            });

        // Merge and limit results
        $results = $projekte->merge($kliente)->take(10);

        return response()->json($results);
    }
}
