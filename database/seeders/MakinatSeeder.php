<?php

namespace Database\Seeders;

use App\Models\Makina;
use App\Models\MirembajtjePlan;
use Illuminate\Database\Seeder;

class MakinatSeeder extends Seeder
{
    public function run(): void
    {
        $makinat = [
            [
                'emri' => 'CNC Router Kryesor',
                'lloji' => 'cnc',
                'marka' => 'Homag',
                'modeli' => 'Centateq N-500',
                'lokacioni' => 'Hala A',
                'kritikaliteti' => 'A',
                'ore_pune' => 1200,
                'planet' => [
                    [
                        'titulli' => 'Pastrim ditor & kontroll vizual',
                        'frekuenca' => 'dite',
                        'intervali' => 1,
                        'checklist' => "Pastro tavolinën dhe udhëzuesit nga pluhuri\nKontrollo mbrojtësit e sigurisë\nTesto butonat E-Stop\nKontrollo vakuumin / thithjen\nRaporto zhurma ose dridhje të pazakonta",
                    ],
                    [
                        'titulli' => 'Lubrifikim & kontrolle javore',
                        'frekuenca' => 'jave',
                        'intervali' => 1,
                        'checklist' => "Lubrifiko aksët X/Y/Z sipas manualit\nKontrollo rripat për tension dhe konsum\nKontrollo filtrat e pompës së vakuumit\nInspekto kabllot dhe konektorët",
                    ],
                    [
                        'titulli' => 'Servisim mujor spindle',
                        'frekuenca' => 'muaj',
                        'intervali' => 1,
                        'checklist' => "Inspekto spindle për nxehtësi dhe zhurmë\nKontrollo kolletat dhe mbajtësit e veglave\nVerifiko nivelin e lubrifikantit\nPastro filtrin e kabinetit elektrik",
                    ],
                    [
                        'titulli' => 'Servisim sipas orëve (500h)',
                        'frekuenca' => 'ore',
                        'intervali' => 500,
                        'checklist' => "Ndrysho filtrat kryesorë\nKontrollo rreshtimin e aksëve\nInspekto kushineta dhe greasing\nRegjistro orët aktuale të punës",
                    ],
                ],
            ],
            [
                'emri' => 'Sharrë Paneli',
                'lloji' => 'panel_saw',
                'marka' => 'Altendorf',
                'modeli' => 'F45',
                'lokacioni' => 'Hala A',
                'kritikaliteti' => 'A',
                'ore_pune' => 800,
                'planet' => [
                    [
                        'titulli' => 'Pastrim & kontroll tehe',
                        'frekuenca' => 'dite',
                        'intervali' => 1,
                        'checklist' => "Pastro tavolinën dhe udhëzuesit\nKontrollo tehën kryesore dhe scoring\nVerifiko thithjen e pluhurit\nKontrollo mbrojtësit",
                    ],
                    [
                        'titulli' => 'Kontroll javore rripa & alinimi',
                        'frekuenca' => 'jave',
                        'intervali' => 1,
                        'checklist' => "Kontrollo rripat e motorit\nVerifiko këndin 90°\nInspekto karrocën e lëvizshme\nLubrifiko pikat e specifikuara",
                    ],
                ],
            ],
            [
                'emri' => 'Kantues (Edge Bander)',
                'lloji' => 'edge_bander',
                'marka' => 'Biesse',
                'modeli' => 'Stream A',
                'lokacioni' => 'Hala B',
                'kritikaliteti' => 'A',
                'ore_pune' => 950,
                'planet' => [
                    [
                        'titulli' => 'Pastrim ngjitësi & rrotulla',
                        'frekuenca' => 'dite',
                        'intervali' => 1,
                        'checklist' => "Pastro potin e ngjitësit nga mbetjet\nPastro rrotullat dhe zinxhirin\nKontrollo temperaturën e potit\nBëj test edge në skrap",
                    ],
                    [
                        'titulli' => 'Kontroll mujore tehe trim',
                        'frekuenca' => 'muaj',
                        'intervali' => 1,
                        'checklist' => "Inspekto tehet top/bottom trim\nKontrollo tensionin e shiritit transportues\nKalibro termostatin e ngjitësit\nKontrollo tubacionet pneumatike për rrjedhje",
                    ],
                    [
                        'titulli' => 'Servisim tremujor kalibrimi',
                        'frekuenca' => 'tremujor',
                        'intervali' => 1,
                        'checklist' => "Ri-zero end-trim dhe flush-trim\nVerifiko këndet 90°/45°\nInspekto valvulat pneumatike\nKontrollo filtrat e ajrit",
                    ],
                ],
            ],
            [
                'emri' => 'Aspirim Pluhuri Qendror',
                'lloji' => 'dust_collector',
                'marka' => 'Nederman',
                'modeli' => 'FlexFilter',
                'lokacioni' => 'Jashtë / Teknik',
                'kritikaliteti' => 'A',
                'ore_pune' => 2000,
                'planet' => [
                    [
                        'titulli' => 'Zbrazja e kontejnerëve',
                        'frekuenca' => 'dite',
                        'intervali' => 1,
                        'checklist' => "Kontrollo nivelin e kontejnerëve\nZbraz nëse është mbi 70%\nKontrollo portat blast gates\nDëgjo motorin për zhurma",
                    ],
                    [
                        'titulli' => 'Kontroll filtre & rripa',
                        'frekuenca' => 'jave',
                        'intervali' => 1,
                        'checklist' => "Inspekto filtrat / qeset\nKontrollo rripat e ventilatorit\nVerifiko tubat fleksibël për vrima\nKontrollo manometrin e presionit",
                    ],
                    [
                        'titulli' => 'Inspektim mujor zjarr-risk',
                        'frekuenca' => 'muaj',
                        'intervali' => 1,
                        'checklist' => "Kontrollo spark detection (nëse ka)\nPastro impeller nga depozitimet\nInspekto lidhjet elektrike\nVerifiko rrjedhjen e ajrit në pikat kritike",
                    ],
                ],
            ],
            [
                'emri' => 'Lëmues Wide Belt',
                'lloji' => 'sander',
                'marka' => 'Timesavers',
                'modeli' => '2100',
                'lokacioni' => 'Hala B',
                'kritikaliteti' => 'B',
                'ore_pune' => 600,
                'planet' => [
                    [
                        'titulli' => 'Pastrim & kontroll rrip abraziv',
                        'frekuenca' => 'dite',
                        'intervali' => 1,
                        'checklist' => "Kontrollo gjendjen e rripit abraziv\nPastro tavolinën dhe rrotullat\nVerifiko tracking-un e rripit\nKontrollo thithjen e pluhurit",
                    ],
                    [
                        'titulli' => 'Kontroll javore',
                        'frekuenca' => 'jave',
                        'intervali' => 1,
                        'checklist' => "Kontrollo tensionin e rripit\nInspekto pad-et dhe rrotullat\nLubrifiko sipas manualit\nKontrollo rripat e motorit",
                    ],
                ],
            ],
            [
                'emri' => 'Kompresor Ajri',
                'lloji' => 'compressor',
                'marka' => 'Atlas Copco',
                'modeli' => 'GA 15',
                'lokacioni' => 'Dhoma teknike',
                'kritikaliteti' => 'A',
                'ore_pune' => 3500,
                'planet' => [
                    [
                        'titulli' => 'Kontroll uji & presioni',
                        'frekuenca' => 'dite',
                        'intervali' => 1,
                        'checklist' => "Dreno ujin nga rezervuari\nKontrollo presionin e punës\nDëgjo për rrjedhje ajri\nKontrollo nivelin e vajit (nëse ka)",
                    ],
                    [
                        'titulli' => 'Ndryshim filtre mujore',
                        'frekuenca' => 'muaj',
                        'intervali' => 1,
                        'checklist' => "Ndrysho filtrin e ajrit\nKontrollo filtrin e vajit\nInspekto rripat\nVerifiko valvulat e sigurisë",
                    ],
                ],
            ],
        ];

        foreach ($makinat as $item) {
            $planet = $item['planet'];
            unset($item['planet']);

            $makina = Makina::updateOrCreate(
                ['emri' => $item['emri']],
                array_merge($item, [
                    'statusi' => 'aktive',
                    'data_blerjes' => now()->subYears(2)->toDateString(),
                ])
            );

            foreach ($planet as $plan) {
                MirembajtjePlan::updateOrCreate(
                    [
                        'makin_id' => $makina->makin_id,
                        'titulli' => $plan['titulli'],
                    ],
                    array_merge($plan, [
                        'makin_id' => $makina->makin_id,
                        'data_radhese' => now()->toDateString(),
                        'aktiv' => true,
                    ])
                );
            }
        }
    }
}
