<?php

namespace Tests\Feature;

use App\Models\Makina;
use App\Models\MirembajtjeEvidenca;
use App\Models\MirembajtjePlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MakinaMirembajtjeTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'rol_id' => 1,
            'emri' => 'Admin',
            'mbiemri' => 'Makina',
            'email' => 'admin-makina@test.com',
            'fjalekalimi_hash' => Hash::make('password'),
            'aktiv' => 1,
        ]);
    }

    public function test_admin_can_create_machine(): void
    {
        $response = $this->actingAs($this->admin)->post(route('makinat.store'), [
            'emri' => 'CNC Test',
            'lloji' => 'cnc',
            'marka' => 'Homag',
            'modeli' => 'N-500',
            'statusi' => 'aktive',
            'kritikaliteti' => 'A',
            'ore_pune' => 100,
        ]);

        $makina = Makina::where('emri', 'CNC Test')->first();
        $this->assertNotNull($makina);
        $response->assertRedirect(route('makinat.show', $makina));
        $this->assertDatabaseHas('makinat', [
            'emri' => 'CNC Test',
            'lloji' => 'cnc',
            'kritikaliteti' => 'A',
        ]);
    }

    public function test_can_add_pm_plan_and_complete_it(): void
    {
        $makina = Makina::create([
            'emri' => 'Sharrë Test',
            'lloji' => 'panel_saw',
            'statusi' => 'aktive',
            'kritikaliteti' => 'A',
            'ore_pune' => 50,
        ]);

        $this->actingAs($this->admin)->post(route('mirembajtje.store-plan', $makina), [
            'titulli' => 'Pastrim ditor',
            'frekuenca' => 'dite',
            'intervali' => 1,
            'data_radhese' => now()->subDay()->toDateString(),
            'checklist' => "Pastro pluhurin\nKontrollo tehen",
            'aktiv' => 1,
        ])->assertRedirect();

        $plan = MirembajtjePlan::where('titulli', 'Pastrim ditor')->first();
        $this->assertNotNull($plan);
        $this->assertTrue($plan->eshteVonuar());

        $this->actingAs($this->admin)->post(route('mirembajtje.complete-plan', $plan), [
            'gjetjet' => 'Gjithçka OK',
            'ore_pune' => 51,
        ])->assertRedirect();

        $plan->refresh();
        $this->assertFalse($plan->eshteVonuar());
        $this->assertEquals(now()->addDay()->toDateString(), $plan->data_radhese->toDateString());

        $this->assertDatabaseHas('mirembajtje_evidenca', [
            'makin_id' => $makina->makin_id,
            'plan_id' => $plan->plan_id,
            'statusi' => 'perfunduar',
            'lloji' => 'preventive',
        ]);

        $this->assertEquals(51.0, (float) $makina->fresh()->ore_pune);
    }

    public function test_can_register_corrective_work_order(): void
    {
        $makina = Makina::create([
            'emri' => 'Kantues Test',
            'lloji' => 'edge_bander',
            'statusi' => 'aktive',
            'kritikaliteti' => 'B',
        ]);

        $this->actingAs($this->admin)->post(route('mirembajtje.store-evidenca', $makina), [
            'lloji' => 'corrective',
            'titulli' => 'Ndryshim rripi transportues',
            'pershkrimi' => 'Rripi ishte i konsumuar',
            'statusi' => 'e_hapur',
            'prioriteti' => 'larte',
        ])->assertRedirect();

        $this->assertDatabaseHas('mirembajtje_evidenca', [
            'makin_id' => $makina->makin_id,
            'lloji' => 'corrective',
            'statusi' => 'e_hapur',
            'prioriteti' => 'larte',
        ]);
    }

    public function test_pm_dashboard_loads(): void
    {
        $this->withoutVite();

        $this->actingAs($this->admin)
            ->get(route('mirembajtje.dashboard'))
            ->assertOk()
            ->assertSee('Planifikimi i Mirëmbajtjes');
    }

    public function test_hour_based_plan_advances_on_complete(): void
    {
        $makina = Makina::create([
            'emri' => 'CNC Ore',
            'lloji' => 'cnc',
            'statusi' => 'aktive',
            'kritikaliteti' => 'A',
            'ore_pune' => 500,
        ]);

        $plan = MirembajtjePlan::create([
            'makin_id' => $makina->makin_id,
            'titulli' => 'Servisim 500h',
            'frekuenca' => 'ore',
            'intervali' => 500,
            'data_radhese' => now()->toDateString(),
            'ore_fundit_kryer' => 0,
            'aktiv' => true,
        ]);

        $this->assertTrue($plan->eshteVonuar());

        $this->actingAs($this->admin)->post(route('mirembajtje.complete-plan', $plan), [
            'ore_pune' => 520,
        ])->assertRedirect();

        $plan->refresh();
        $this->assertEquals(520.0, (float) $plan->ore_fundit_kryer);
        $this->assertFalse($plan->fresh()->eshteVonuar());
    }
}
