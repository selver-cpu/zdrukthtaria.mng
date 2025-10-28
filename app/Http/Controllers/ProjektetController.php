<?php

namespace App\Http\Controllers;

use App\Models\Projektet;
use App\Models\Klientet;
use App\Models\DokumentetProjekti;
use App\Models\StatusetProjektit;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\LogsProjectActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\StoreProjektRequest;

class ProjektetController extends Controller
{
    /**
     * Kontrollon nëse përdoruesi ka të drejtë të krijojë/modifikojë projekte
     */
    protected function checkProjectManagementAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Ju lutem kyçuni për të vazhduar.');
        }
        
        // Vetëm Admin (1), Menaxher (2) dhe Disajnere (5) mund të krijojnë/modifikojnë projekte
        if (!in_array(auth()->user()->rol_id, [1, 2, 5])) {
            return redirect()->route('projektet.index')
                ->with('error', 'Vetëm administratori, menaxheri dhe disajnere mund të krijojnë ose modifikojnë projekte.');
        }
        
        return null;
    }
    
    /**
     * Filtron projektet bazuar në rolin e përdoruesit
     */
    protected function getFilteredProjects()
    {
        $user = auth()->user();
        
        // Admin, Menaxher dhe Disajnere shohin të gjitha projektet
        if (in_array($user->rol_id, [1, 2, 5])) {
            return Projektet::with(['klient', 'statusi_projektit'])->latest();
        }
        
        // Mjeshtër dhe Montues shohin vetëm projektet e tyre
        return Projektet::with(['klient', 'statusi_projektit'])
            ->where(function($query) use ($user) {
                $query->where('mjeshtri_caktuar_id', $user->perdorues_id)
                      ->orWhere('montuesi_caktuar_id', $user->perdorues_id);
            })
            ->latest();
    }
    use LogsProjectActions;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projektet = $this->getFilteredProjects()->paginate(10);
            
        return view('projektet.index', compact('projektet'));
    }
    
    /**
     * Show the form for creating a new project.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkProjectManagementAccess()) {
            return $redirect;
        }
        
        $klientet = Klientet::orderBy('emri_klientit')->get();
        $statuset = StatusetProjektit::all();
        $mjeshtrat = User::where('rol_id', 3)->get(); // Rol ID 3 për mjeshtër
        $montuesit = User::where('rol_id', 4)->get();   // Rol ID 4 për montues

        return view('projektet.create', compact('klientet', 'statuset', 'mjeshtrat', 'montuesit'));
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkProjectManagementAccess()) {
            return $redirect;
        }
        
        $projekt = Projektet::with(['klient', 'statusi_projektit', 'mjeshtri', 'montuesi'])->findOrFail($id);
        
        $klientet = Klientet::orderBy('emri_klientit')->get();
        $statuset = StatusetProjektit::orderBy('renditja')->get();
        
        // Get users with role 'mjeshtër' (role_id = 3)
        $mjeshtre = User::whereHas('rol', function($query) {
            $query->where('rol_id', 3);
        })->get();
        
        // Get users with role 'montues' (role_id = 4)
        $montues = User::whereHas('rol', function($query) {
            $query->where('rol_id', 4);
        })->get();

        return view('projektet.edit', compact('projekt', 'klientet', 'statuset', 'mjeshtre', 'montues'));
    }

    /**
     * Update the specified project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'emri_projektit' => 'required|string|max:255',
            'klient_id' => 'required|exists:klientet,klient_id',
            'status_id' => 'required|exists:statuset_projektit,status_id',
            'mjeshtri_caktuar_id' => 'nullable|exists:perdoruesit,perdorues_id',
            'montuesi_caktuar_id' => 'nullable|exists:perdoruesit,perdorues_id',
            'pershkrimi' => 'nullable|string',
            'data_fillimit_parashikuar' => 'nullable|date',
            'data_perfundimit_parashikuar' => 'nullable|date|after_or_equal:data_fillimit_parashikuar',
            'data_perfundimit_real' => 'nullable|date|after_or_equal:data_fillimit_parashikuar',
            'shenime_projekt' => 'nullable|string',
        ]);

        $projekt = Projektet::findOrFail($id);
        
        // Ruaj vlerat e vjetra për krahasim
        $oldMjeshtriId = $projekt->mjeshtri_caktuar_id;
        $oldMontuesiId = $projekt->montuesi_caktuar_id;
        $oldStatusId = $projekt->status_id;
        
        // Update the project
        $projekt->update($validated);
        
        // Dërgo njoftim nëse është caktuar një mjeshtër i ri
        if ($oldMjeshtriId != $projekt->mjeshtri_caktuar_id && $projekt->mjeshtri_caktuar_id) {
            $mjeshtriMesazhi = 'Ju jeni caktuar si mjeshtër për projektin: ' . $projekt->emri_projektit;
            $this->notificationService->sendProjectNotification(
                $projekt,
                $mjeshtriMesazhi,
                [$projekt->mjeshtri_caktuar_id],
                true // Dërgo email
            );
        }
        
        // Dërgo njoftim nëse është caktuar një montues i ri
        if ($oldMontuesiId != $projekt->montuesi_caktuar_id && $projekt->montuesi_caktuar_id) {
            $montuesiMesazhi = 'Ju jeni caktuar si montues për projektin: ' . $projekt->emri_projektit;
            $this->notificationService->sendProjectNotification(
                $projekt,
                $montuesiMesazhi,
                [$projekt->montuesi_caktuar_id],
                true // Dërgo email
            );
        }
        
        // Dërgo njoftim nëse është ndryshuar statusi i projektit
        if ($oldStatusId != $projekt->status_id) {
            // Merr emrin e statusit të ri
            $newStatus = \App\Models\StatusetProjektit::find($projekt->status_id);
            if ($newStatus) {
                $statusMesazhi = 'Statusi i projektit "' . $projekt->emri_projektit . '" u ndryshua në: ' . $newStatus->emri_statusit;
                
                // Gjej administratoret dhe menaxheret
                $adminUsers = User::whereHas('rol', function($query) {
                    $query->where('rol_id', 1); // Admin
                })->pluck('perdorues_id')->toArray();
                
                $managerUsers = User::whereHas('rol', function($query) {
                    $query->where('rol_id', 2); // Menaxher
                })->pluck('perdorues_id')->toArray();
                
                // Bashko listat e përdoruesve për të njoftuar
                $usersToNotify = array_merge($adminUsers, $managerUsers);
                
                // Shto mjeshtrin dhe montuesin e caktuar nëbe listën e njoftimeve
                if ($projekt->mjeshtri_caktuar_id) {
                    $usersToNotify[] = $projekt->mjeshtri_caktuar_id;
                }
                
                if ($projekt->montuesi_caktuar_id) {
                    $usersToNotify[] = $projekt->montuesi_caktuar_id;
                }
                
                // Largo duplikatet
                $usersToNotify = array_unique($usersToNotify);
                
                // Dërgo njoftimet
                $this->notificationService->sendProjectNotification(
                    $projekt,
                    $statusMesazhi,
                    $usersToNotify,
                    true // Dërgo email
                );
            }
        }
        
        // Log the update action
        $this->logProjectAction(
            auth('web')->id() ?? null,
            'update',
            'Projekti u përditësua',
            $projekt->projekt_id,
            'Projekti "' . $projekt->emri_projektit . '" u përditësua me sukses.'
        );

        return redirect()->route('projektet.show', $projekt->projekt_id)
            ->with('success', 'Projekti u përditësua me sukses!');
    }

    /**
     * Display the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $projekt = Projektet::with([
            'klient', 
            'statusi_projektit', 
            'mjeshtri', 
            'montuesi', 
            'materialet', 
            'dokumentet',
            'proceset' => function($query) {
                $query->orderBy('data_ndryshimit', 'desc');
            },
            'fazat'
        ])->findOrFail($id);

        $fazat = \App\Models\FazatProjekti::orderBy('emri_fazes')->get();
        $materialet = \App\Models\Materialet::whereNotNull('emri_materialit')->orderBy('emri_materialit')->get();

        return view('projektet.show', compact('projekt', 'fazat', 'materialet'));
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  \App\Http\Requests\StoreProjektRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjektRequest $request)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkProjectManagementAccess()) {
            return $redirect;
        }
        
        try {
            DB::beginTransaction();

            // Krijo projektin
            $projekti = Projektet::create([
                'klient_id' => $request->klient_id,
                'emri_projektit' => $request->emri_projektit,
                'pershkrimi' => $request->pershkrimi,
                'data_fillimit_parashikuar' => $request->data_fillimit_parashikuar,
                'data_perfundimit_parashikuar' => $request->data_perfundimit_parashikuar,
                'status_id' => $request->status_id ?? 1, // status fillestar "pending" nëse nuk është specifikuar
                'mjeshtri_caktuar_id' => $request->mjeshtri_caktuar_id,
                'montuesi_caktuar_id' => $request->montuesi_caktuar_id,
                'shenime_projekt' => $request->shenime_projekt,
                'krijues_id' => auth('web')->user()->perdorues_id
            ]);

            // Ruaj veprimin në ditar
            $this->logProjectAction(
                $projekti->projekt_id,
                'Projekti u krijua',
                $projekti->toArray()
            );

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
                              . '_' . time() 
                              . '.' . $file->getClientOriginalExtension();
                    
                    $path = $file->storeAs(
                        'projektet/' . $projekti->projekt_id,
                        $filename,
                        'public'
                    );

                    $dokument = DokumentetProjekti::create([
                        'projekt_id' => $projekti->projekt_id,
                        'emri_skedarit' => $file->getClientOriginalName(),
                        'lloji_skedarit' => $file->getClientMimeType(),
                        'rruga_skedarit' => $path,
                        'madhesia_skedarit' => $file->getSize(),
                        'perdorues_id_ngarkues' => auth('web')->user()->perdorues_id,
                        'data_ngarkimit' => now()
                    ]);

                    // Ruaj veprimin e ngarkimit të dokumentit në ditar
                    $this->logProjectAction(
                        $projekti->projekt_id,
                        'Dokument u ngarkua',
                        [
                            'dokument_id' => $dokument->dokument_id,
                            'emri_skedarit' => $dokument->emri_skedarit,
                            'lloji_skedarit' => $dokument->lloji_skedarit,
                            'madhesia_skedarit' => $dokument->madhesia_skedarit
                        ]
                    );
                }
            }

            DB::commit();

            // Dërgo njoftimet për krijimin e projektit
            $this->notificationService->notifyProjectCreation($projekti);

            return redirect()
                ->route('projektet.show', $projekti->projekt_id)
                ->with('success', 'Projekti u krijua me sukses dhe njoftimet u dërguan.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the actual error for debugging
            Log::error('Project creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fshij skedarët e ngarkuar nëse ka
            if (isset($projekti) && $request->hasFile('files')) {
                Storage::disk('public')->deleteDirectory('projektet/' . $projekti->projekt_id);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ndodhi një gabim gjatë krijimit të projektit: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created project phase in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projektId
     * @return \Illuminate\Http\Response
     */
    public function storeFaza(Request $request, $projektId)
    {
        $request->validate([
            'faza_id' => 'required|exists:fazat_projekti,id',
            'data_fillimit' => 'required|date',
            'data_perfundimit' => 'required|date|after_or_equal:data_fillimit',
            'pershkrimi' => 'nullable|string',
        ]);

        $projekt = Projektet::findOrFail($projektId);
        
        // Check if phase already exists for this project
        if ($projekt->fazat()->where('faza_id', $request->faza_id)->exists()) {
            return redirect()->back()
                ->with('error', 'Kjo fazë ekziston tashmë në këtë projekt.');
        }

        try {
            // Attach phase to project with pivot data
            $projekt->fazat()->attach($request->faza_id, [
                'statusi_fazes' => 'pritje',
                'data_fillimit' => $request->data_fillimit,
                'data_perfundimit' => $request->data_perfundimit,
                'komente' => $request->pershkrimi, // Using pershkrimi from request as komente in database
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log the action
            $this->logProjectAction(
                $projekt->projekt_id,
                'Faza u shtua në projekt',
                [
                    'faza_id' => $request->faza_id,
                    'data_fillimit' => $request->data_fillimit,
                    'data_perfundimit' => $request->data_perfundimit
                ]
            );

            return redirect()->back()
                ->with('success', 'Faza u shtua me sukses në projekt!');
                
        } catch (\Exception $e) {
            Log::error('Error adding phase to project: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Ndodhi një gabim gjatë shtimit të fazës. Ju lutemi provoni përsëri.');
        }
    }

    /**
     * Update the specified project phase in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projektId
     * @param  int  $fazaPivotId
     * @return \Illuminate\Http\Response
     */
    public function updateFaza(Request $request, $projektId, $fazaPivotId)
    {
        $request->validate([
            'data_fillimit' => 'required|date',
            'data_perfundimit' => 'required|date|after_or_equal:data_fillimit',
            'statusi_fazes' => 'required|in:pritje,në_progres,kompletuar,anuluar',
            'pershkrimi' => 'nullable|string',
        ]);

        $projekt = Projektet::findOrFail($projektId);
        
        // Merr të dhënat e vjetra të fazës para përditësimit
        $fazaPivot = $projekt->fazats()->wherePivot('id', $fazaPivotId)->first()->pivot;
        $oldStatus = $fazaPivot->statusi_fazes;
        
        // Update the pivot record
        $projekt->fazats()->updateExistingPivot($fazaPivotId, [
            'statusi_fazes' => $request->statusi_fazes,
            'data_fillimit' => $request->data_fillimit,
            'data_perfundimit' => $request->data_perfundimit,
            'pershkrimi' => $request->pershkrimi,
            'updated_at' => now(),
        ]);
        
        // Dërgo njoftim nëse është ndryshuar statusi i fazës
        if ($oldStatus != $request->statusi_fazes) {
            // Merr emrin e fazës
            $faza = $projekt->fazats()->wherePivot('id', $fazaPivotId)->first();
            
            if ($faza) {
                $fazaMesazhi = 'Statusi i fazës "' . $faza->emri_fazes . '" në projektin "' . $projekt->emri_projektit . '" u ndryshua në: ' . $request->statusi_fazes;
                
                // Gjej administratoret dhe menaxheret
                $adminUsers = User::whereHas('rol', function($query) {
                    $query->where('rol_id', 1); // Admin
                })->pluck('perdorues_id')->toArray();
                
                $managerUsers = User::whereHas('rol', function($query) {
                    $query->where('rol_id', 2); // Menaxher
                })->pluck('perdorues_id')->toArray();
                
                // Bashko listat e përdoruesve për të njoftuar
                $usersToNotify = array_merge($adminUsers, $managerUsers);
                
                // Shto mjeshtrin dhe montuesin e caktuar në listën e njoftimeve
                if ($projekt->mjeshtri_caktuar_id) {
                    $usersToNotify[] = $projekt->mjeshtri_caktuar_id;
                }
                
                if ($projekt->montuesi_caktuar_id) {
                    $usersToNotify[] = $projekt->montuesi_caktuar_id;
                }
                
                // Largo duplikatet
                $usersToNotify = array_unique($usersToNotify);
                
                // Dërgo njoftimet
                $this->notificationService->sendProjectNotification(
                    $projekt,
                    $fazaMesazhi,
                    $usersToNotify,
                    true // Dërgo email
                );
            }
        }

        // Log the action
        $this->logProjectAction(
            $projekt->projekt_id,
            'Faza u përditësua',
            [
                'faza_pivot_id' => $fazaPivotId,
                'statusi_fazes' => $request->statusi_fazes,
                'data_fillimit' => $request->data_fillimit,
                'data_perfundimit' => $request->data_perfundimit
            ]
        );

        return redirect()->back()
            ->with('success', 'Faza u përditësua me sukses!');
    }

    /**
     * Remove the specified project phase from storage.
     *
     * @param  int  $projektId
     * @param  int  $fazaPivotId
     * @return \Illuminate\Http\Response
     */
    public function destroyFaza($projektId, $fazaPivotId)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkProjectManagementAccess()) {
            return $redirect;
        }
        
        $projekt = Projektet::findOrFail($projektId);
        
        // Get the phase before detaching it for logging
        $faza = $projekt->fazat()->where('projekt_faza_pune.id', $fazaPivotId)->first();
        
        if (!$faza) {
            return redirect()->back()
                ->with('error', 'Faza nuk u gjet në këtë projekt.');
        }
        
        // Detach the phase
        $projekt->fazat()->wherePivot('id', $fazaPivotId)->detach();
        
        // Log the action
        $this->logProjectAction(
            $projekt->projekt_id,
            'Faza u hoq nga projekti',
            [
                'faza_id' => $faza->id,
                'emri_fazes' => $faza->emri_fazes,
                'faza_pivot_id' => $fazaPivotId
            ]
        );

        return redirect()->back()
            ->with('success', 'Faza u hoq me sukses nga projekti!');
    }
    
    /**
     * Store a newly created project material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projektId
     * @return \Illuminate\Http\Response
     */
    public function storeMaterial(Request $request, $projektId)
    {
        try {
            $validated = $request->validate([
                'material_id' => 'required|exists:materialet,material_id',
                'sasia_perdorur' => 'required|numeric|min:0.01',
                'pershkrimi' => 'nullable|string',
            ]);

            $projekt = Projektet::findOrFail($projektId);
            
            // Check if material already exists for this project using the pivot table directly
            $exists = $projekt->materialet()
                ->where('projekt_materiale.material_id', $validated['material_id'])
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ky material ekziston tashmë në këtë projekt.'
                ], 422);
            }

            // Attach material to project with quantity
            $projekt->materialet()->attach($validated['material_id'], [
                'sasia_perdorur' => $validated['sasia_perdorur'],
                'pershkrimi' => $validated['pershkrimi'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log the action
            $this->logProjectAction(
                $projekt->projekt_id,
                'Materiali u shtua në projekt',
                [
                    'material_id' => $validated['material_id'],
                    'sasia_perdorur' => $validated['sasia_perdorur']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Materiali u shtua me sukses në projekt!'
            ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Të dhënat nuk janë valide',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error adding material to project: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Ndodhi një gabim gjatë ruajtjes së materialit.'
            ], 500);
            
            return redirect()->back()
                ->with('error', 'Ndodhi një gabim gjatë shtimit të materialit. Ju lutemi provoni përsëri.');
        }
    }

    public function updateMaterial(Request $request, $projektId, $materialId)
    {
        $request->validate([
            'sasia_perdorur' => 'required|numeric|min:0.01',
            'pershkrimi' => 'nullable|string',
        ]);

        $projekt = Projektet::findOrFail($projektId);
        
        // Update the pivot record
        $projekt->materialet()->updateExistingPivot($materialId, [
            'sasia_perdorur' => $request->sasia_perdorur,
            'pershkrimi' => $request->pershkrimi,
            'updated_at' => now(),
        ]);

        // Log the action
        $this->logProjectAction(
            $projekt->projekt_id,
            'Materiali u përditësua',
            [
                'material_id' => $materialId,
                'sasia_perdorur' => $request->sasia_perdorur
            ]
        );

        return redirect()->back()
            ->with('success', 'Materiali u përditësua me sukses!');
    }
    
    /**
     * Remove the specified project material from storage.
     *
     * @param  int  $projektId
     * @param  int  $materialId
     * @return \Illuminate\Http\Response
     */
    public function destroyMaterial($projektId, $materialId)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkProjectManagementAccess()) {
            return $redirect;
        }
        
        $projekt = Projektet::findOrFail($projektId);
        
        // Check if material exists in this project
        if (!$projekt->materialet()->where('material_id', $materialId)->exists()) {
            return redirect()->back()
                ->with('error', 'Ky material nuk u gjet në këtë projekt.');
        }
        
        // Detach the material
        $projekt->materialet()->detach($materialId);
        
        // Log the action
        $this->logProjectAction(
            $projekt->projekt_id,
            'Materiali u hoq nga projekti',
            ['material_id' => $materialId]
        );

        return redirect()->back()
            ->with('success', 'Materiali u hoq me sukses nga projekti!');
    }
    
    /**
     * Get users by role for email selection
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUsersForEmail($id)
    {
        $projekt = Projektet::findOrFail($id);
        
        // Get all admins
        $admins = User::whereHas('rol', function($query) {
            $query->where('rol_id', 1);
        })->get();
        
        // Get all managers
        $managers = User::whereHas('rol', function($query) {
            $query->where('rol_id', 2);
        })->get();
        
        // Get all master craftsmen
        $craftsmen = User::whereHas('rol', function($query) {
            $query->where('rol_id', 3); // Assuming role_id 3 is for master craftsmen
        })->get();
        
        // Get all installers
        $installers = User::whereHas('rol', function($query) {
            $query->where('rol_id', 4); // Assuming role_id 4 is for installers
        })->get();
        
        return view('projektet.email-form', compact('projekt', 'admins', 'managers', 'craftsmen', 'installers'));
    }

    /**
     * Send email to specific recipients about the project
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request, $id)
    {
        $projekt = Projektet::with(['klient', 'statusi_projektit', 'mjeshtri', 'montuesi'])->findOrFail($id);
        $userId = $request->input('user_id');
        
        if (!$userId) {
            return redirect()->back()->with('error', 'Ju lutem zgjidhni një përdorues për të dërguar email-in!');
        }
        
        $recipient = User::find($userId);
        
        if (!$recipient) {
            return redirect()->back()->with('error', 'Përdoruesi i zgjedhur nuk u gjet!');
        }
        
        $mesazhi = 'Informacion për projektin: ' . $projekt->emri_projektit;
        
        $this->notificationService->sendProjectEmail($projekt, $recipient, $mesazhi);
        return redirect()->route('projektet.show', $projekt->projekt_id)
            ->with('success', 'Email-i u dërgua me sukses tek ' . $recipient->emri . ' ' . $recipient->mbiemri . '!');
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Kontrollo qasjen
        if ($redirect = $this->checkProjectManagementAccess()) {
            return $redirect;
        }
        
        try {
            $projekt = Projektet::findOrFail($id);
            
            // Check if project can be deleted
            if ($projekt->materialet()->count() > 0) {
                return redirect()->route('projektet.index')
                    ->with('error', 'Projekti nuk mund të fshihet sepse ka materiale të lidhura me të.');
            }
            
            // Log the action before deletion
            $this->logProjectAction(
                $projekt->projekt_id,
                'Projekti u fshi',
                ['emri_projektit' => $projekt->emri_projektit]
            );
            
            // Delete the project
            $projekt->delete();
            
            return redirect()->route('projektet.index')
                ->with('success', 'Projekti u fshi me sukses.');
        } catch (\Exception $e) {
            Log::error('Error deleting project: ' . $e->getMessage());
            return redirect()->route('projektet.index')
                ->with('error', 'Ndodhi një gabim gjatë fshirjes së projektit.');
        }
    }
}
