<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjektRequest extends FormRequest
{
    public function authorize()
    {
        // Kontrollo nëse përdoruesi ka të drejtë të krijojë projekt
        return auth()->user()->can('create', \App\Models\Projektet::class);
    }

    public function rules()
    {
        return [
            'emri_projektit' => 'required|string|max:255',
            'pershkrimi' => 'nullable|string',
            'klient_id' => 'required|exists:klientet,klient_id',
            'data_fillimit_parashikuar' => 'nullable|date',
            'data_perfundimit_parashikuar' => 'nullable|date|after_or_equal:data_fillimit_parashikuar',
            'data_perfundimit_real' => 'nullable|date',
            'status_id' => 'required|exists:statuset_projektit,status_id',
            'mjeshtri_caktuar_id' => [
                'nullable',
                'exists:perdoruesit,perdorues_id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $mjeshtri = \App\Models\User::find($value);
                        if (!$mjeshtri || !$mjeshtri->hasRole('mjeshtër')) {
                            $fail('Përdoruesi i zgjedhur nuk është mjeshtër.');
                        }
                    }
                }
            ],
            'montuesi_caktuar_id' => [
                'nullable',
                'exists:perdoruesit,perdorues_id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $montuesi = \App\Models\User::find($value);
                        if (!$montuesi || !$montuesi->hasRole('montues')) {
                            $fail('Përdoruesi i zgjedhur nuk është montues.');
                        }
                    }
                }
            ],
            'shenime_projekt' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => [
                'nullable',
                'file',
                'mimes:pdf,xlsx,xls,docx,jpg,jpeg,png,zip,stl,step,skp,dwg',
                'max:20480' // 20MB
            ]
        ];
    }

    public function messages()
    {
        return [
            'emri_projektit.required' => 'Emri i projektit është i detyrueshëm.',
            'emri_projektit.max' => 'Emri i projektit nuk mund të jetë më i gjatë se 255 karaktere.',
            'klient_id.required' => 'Zgjedhja e klientit është e detyrueshme.',
            'klient_id.exists' => 'Klienti i zgjedhur nuk ekziston.',
            'data_perfundimit_parashikuar.after_or_equal' => 'Data e përfundimit duhet të jetë pas ose e barabartë me datën e fillimit.',
            'mjeshtri_caktuar_id.exists' => 'Mjeshtri i zgjedhur nuk ekziston.',
            'montuesi_caktuar_id.exists' => 'Montuesi i zgjedhur nuk ekziston.',
            'files.*.mimes' => 'Formatet e lejuara janë: PDF, Excel, Word, JPG, PNG, ZIP, STL, STEP, SKP, DWG.',
            'files.*.max' => 'Madhësia maksimale e skedarit është 20MB.'
        ];
    }
}
